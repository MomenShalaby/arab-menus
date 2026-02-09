<?php

declare(strict_types=1);

namespace App\Services\Scraper;

use App\DTOs\LocationData;
use App\Models\City;
use App\Models\ScrapingLog;
use App\Models\Zone;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Scrapes cities and zones from the menuegypt.com site.
 * Extracts location data from the search form dropdowns.
 */
class LocationScraper
{
    public function __construct(
        private readonly HttpClientService $httpClient,
    ) {}

    /**
     * Scrape all cities and their zones from the homepage.
     *
     * @return LocationData[]
     */
    public function scrapeAll(): array
    {
        $log = ScrapingLog::create([
            'type' => 'city',
            'url' => $this->httpClient->getBaseUrl(),
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            $crawler = $this->httpClient->fetchPage('/');

            if ($crawler === null) {
                $log->markFailed('Failed to fetch homepage');
                return [];
            }

            $locations = $this->extractLocations($crawler);
            $this->persistLocations($locations);

            $log->markCompleted(count($locations));

            return $locations;
        } catch (\Exception $e) {
            Log::error('Location scraping failed: ' . $e->getMessage());
            $log->markFailed($e->getMessage());
            return [];
        }
    }

    /**
     * Extract city/zone data from the homepage select elements.
     *
     * @return LocationData[]
     */
    private function extractLocations(Crawler $crawler): array
    {
        $locations = [];

        // Try to find city select dropdown - menuegypt uses select elements
        // The form has select boxes for city, zone, category
        $cityOptions = $crawler->filter('select[name="city"] option, select#city option, #citySelect option, select.city option');

        if ($cityOptions->count() === 0) {
            // Fallback: try to extract from links in footer or navigation
            $cityLinks = $crawler->filter('a[href*="/menus/"]');

            $cityLinks->each(function (Crawler $node) use (&$locations): void {
                $href = $node->attr('href') ?? '';
                $text = trim($node->text(''));

                if (preg_match('#/menus/([^/]+)/#', $href, $matches)) {
                    $citySlug = $matches[1];

                    if (! empty($text) && ! isset($locations[$citySlug])) {
                        // Extract zone from URL if present
                        $zones = [];
                        if (preg_match('#/menus/[^/]+/([^/]+)/#', $href, $zoneMatches)) {
                            $zoneSlug = $zoneMatches[1];
                            $zoneName = ucwords(str_replace('-', ' ', $zoneSlug));
                            $zones[] = ['name' => $zoneName, 'slug' => $zoneSlug];
                        }

                        $cityName = ucwords(str_replace('-', ' ', $citySlug));
                        $locations[$citySlug] = new LocationData(
                            cityName: $cityName,
                            citySlug: $citySlug,
                            zones: $zones,
                        );
                    }
                }
            });
        } else {
            $cityOptions->each(function (Crawler $option) use ($crawler, &$locations): void {
                $value = $option->attr('value') ?? '';
                $text = trim($option->text(''));

                if (empty($value) || empty($text)) {
                    return;
                }

                $slug = Str::slug($value);
                $zones = $this->extractZonesForCity($crawler, $value);

                $locations[$slug] = new LocationData(
                    cityName: $text,
                    citySlug: $slug,
                    zones: $zones,
                );
            });
        }

        // If scraping failed or returned too few cities, use known cities fallback.
        // The site has 10+ cities; if we found fewer than 5, scraping was incomplete.
        if (count($locations) < 5) {
            Log::info('Location scraping found too few cities (' . count($locations) . '), using known locations fallback.');
            $locations = $this->getKnownLocations();
        }

        return array_values($locations);
    }

    /**
     * Extract zones for a specific city from the page.
     *
     * @return array<int, array{name: string, slug: string}>
     */
    private function extractZonesForCity(Crawler $crawler, string $cityValue): array
    {
        $zones = [];

        $zoneOptions = $crawler->filter('select[name="zone"] option, select#zone option, #zoneSelect option');
        $zoneOptions->each(function (Crawler $option) use (&$zones): void {
            $value = $option->attr('value') ?? '';
            $text = trim($option->text(''));

            if (! empty($value) && ! empty($text)) {
                $zones[] = [
                    'name' => $text,
                    'slug' => Str::slug($value),
                ];
            }
        });

        return $zones;
    }

    /**
     * Persist scraped location data to the database.
     *
     * @param LocationData[] $locations
     */
    private function persistLocations(array $locations): void
    {
        foreach ($locations as $location) {
            $city = City::updateOrCreate(
                ['slug' => $location->citySlug],
                [
                    'name' => $location->cityName,
                    'name_ar' => $location->cityNameAr,
                    'source_url' => $this->httpClient->resolveUrl("/menus/{$location->citySlug}/"),
                ],
            );

            foreach ($location->zones as $zoneData) {
                Zone::updateOrCreate(
                    [
                        'city_id' => $city->id,
                        'slug' => $zoneData['slug'],
                    ],
                    [
                        'name' => $zoneData['name'],
                        'name_ar' => $zoneData['name_ar'] ?? null,
                        'source_url' => $this->httpClient->resolveUrl(
                            "/menus/{$location->citySlug}/{$zoneData['slug']}//restaurants-menus-hotline-delivery-number"
                        ),
                    ],
                );
            }
        }
    }

    /**
     * Known cities and zones as a fallback when scraping fails.
     * These are the most common cities on menuegypt.com.
     *
     * @return LocationData[]
     */
    private function getKnownLocations(): array
    {
        $knownData = [
            [
                'city' => 'Cairo', 'slug' => 'Cairo',
                'zones' => [
                    ['name' => 'Nasr City', 'slug' => 'nasr-city'],
                    ['name' => 'Masr El Gdida', 'slug' => 'masr-el-gdida'],
                    ['name' => 'Mohandeseen', 'slug' => 'mohandeseen'],
                    ['name' => 'Maadi', 'slug' => 'maadi'],
                    ['name' => 'Haram', 'slug' => 'haram'],
                    ['name' => 'Downtown', 'slug' => 'downtown'],
                    ['name' => 'Shoubra', 'slug' => 'shoubra'],
                    ['name' => 'New Cairo', 'slug' => 'new-cairo'],
                    ['name' => 'Sheikh Zayed', 'slug' => 'sheikh-zayed'],
                    ['name' => '6 October', 'slug' => '6-October'],
                    ['name' => 'El Rehab', 'slug' => 'el-rehab'],
                    ['name' => 'El Obour', 'slug' => 'el-obour'],
                    ['name' => 'Faisal', 'slug' => 'faisal'],
                    ['name' => 'Mokattam', 'slug' => 'mokattam'],
                    ['name' => 'Ain Shams', 'slug' => 'ain-shams'],
                    ['name' => 'El Manial', 'slug' => 'el-manial'],
                    ['name' => 'Zamalek', 'slug' => 'zamalek'],
                ],
            ],
            [
                'city' => 'Alexandria', 'slug' => 'alexandria',
                'zones' => [
                    ['name' => 'Smouha', 'slug' => 'smouha'],
                    ['name' => 'Gleem', 'slug' => 'gleem'],
                    ['name' => 'San Stefano', 'slug' => 'san-stefano'],
                    ['name' => 'Mandara', 'slug' => 'mandara'],
                    ['name' => 'Miami', 'slug' => 'miami'],
                    ['name' => 'Sidi Bishr', 'slug' => 'sidi-bishr'],
                    ['name' => 'Montazah', 'slug' => 'montazah'],
                    ['name' => 'Roushdy', 'slug' => 'roushdy'],
                ],
            ],
            [
                'city' => 'Tanta', 'slug' => 'tanta',
                'zones' => [
                    ['name' => 'Tanta', 'slug' => 'tanta'],
                ],
            ],
            [
                'city' => 'Mansoura', 'slug' => 'mansoura',
                'zones' => [
                    ['name' => 'Mansoura', 'slug' => 'mansoura'],
                ],
            ],
            [
                'city' => 'Zagazig', 'slug' => 'zagazig',
                'zones' => [
                    ['name' => 'Zagazig', 'slug' => 'zagazig'],
                ],
            ],
            [
                'city' => 'Ismailia', 'slug' => 'ismailia',
                'zones' => [
                    ['name' => 'Ismailia', 'slug' => 'ismailia'],
                ],
            ],
            [
                'city' => 'Assiut', 'slug' => 'assiut',
                'zones' => [
                    ['name' => 'Assiut', 'slug' => 'assiut'],
                ],
            ],
            [
                'city' => 'Port Said', 'slug' => 'port-said',
                'zones' => [
                    ['name' => 'Port Said', 'slug' => 'port-said'],
                ],
            ],
            [
                'city' => 'Suez', 'slug' => 'suez',
                'zones' => [
                    ['name' => 'Suez', 'slug' => 'suez'],
                ],
            ],
            [
                'city' => 'Damietta', 'slug' => 'damietta',
                'zones' => [
                    ['name' => 'Damietta', 'slug' => 'damietta'],
                ],
            ],
        ];

        return array_map(
            fn(array $data) => new LocationData(
                cityName: $data['city'],
                citySlug: $data['slug'],
                zones: $data['zones'],
            ),
            $knownData,
        );
    }
}
