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
                'city' => 'Cairo', 'slug' => 'Cairo', 'city_ar' => 'القاهرة',
                'zones' => [
                    ['name' => 'Nasr City', 'slug' => 'nasr-city', 'name_ar' => 'مدينة نصر'],
                    ['name' => 'Masr El Gdida', 'slug' => 'masr-el-gdida', 'name_ar' => 'مصر الجديدة'],
                    ['name' => 'Mohandeseen', 'slug' => 'mohandeseen', 'name_ar' => 'المهندسين'],
                    ['name' => 'Maadi', 'slug' => 'maadi', 'name_ar' => 'المعادي'],
                    ['name' => 'Haram', 'slug' => 'haram', 'name_ar' => 'الهرم'],
                    ['name' => 'Downtown', 'slug' => 'downtown', 'name_ar' => 'وسط البلد'],
                    ['name' => 'Shoubra', 'slug' => 'shoubra', 'name_ar' => 'شبرا'],
                    ['name' => 'New Cairo', 'slug' => 'new-cairo', 'name_ar' => 'القاهرة الجديدة'],
                    ['name' => 'Sheikh Zayed', 'slug' => 'sheikh-zayed', 'name_ar' => 'الشيخ زايد'],
                    ['name' => '6 October', 'slug' => '6-October', 'name_ar' => '6 أكتوبر'],
                    ['name' => 'El Rehab', 'slug' => 'el-rehab', 'name_ar' => 'الرحاب'],
                    ['name' => 'El Obour', 'slug' => 'el-obour', 'name_ar' => 'العبور'],
                    ['name' => 'Faisal', 'slug' => 'faisal', 'name_ar' => 'فيصل'],
                    ['name' => 'Mokattam', 'slug' => 'mokattam', 'name_ar' => 'المقطم'],
                    ['name' => 'Ain Shams', 'slug' => 'ain-shams', 'name_ar' => 'عين شمس'],
                    ['name' => 'El Manial', 'slug' => 'el-manial', 'name_ar' => 'المنيل'],
                    ['name' => 'Zamalek', 'slug' => 'zamalek', 'name_ar' => 'الزمالك'],
                ],
            ],
            [
                'city' => 'Alexandria', 'slug' => 'alexandria', 'city_ar' => 'الإسكندرية',
                'zones' => [
                    ['name' => 'Smouha', 'slug' => 'smouha', 'name_ar' => 'سموحة'],
                    ['name' => 'Gleem', 'slug' => 'gleem', 'name_ar' => 'جليم'],
                    ['name' => 'San Stefano', 'slug' => 'san-stefano', 'name_ar' => 'سان ستيفانو'],
                    ['name' => 'Mandara', 'slug' => 'mandara', 'name_ar' => 'المندرة'],
                    ['name' => 'Miami', 'slug' => 'miami', 'name_ar' => 'ميامي'],
                    ['name' => 'Sidi Bishr', 'slug' => 'sidi-bishr', 'name_ar' => 'سيدي بشر'],
                    ['name' => 'Montazah', 'slug' => 'montazah', 'name_ar' => 'المنتزه'],
                    ['name' => 'Roushdy', 'slug' => 'roushdy', 'name_ar' => 'رشدي'],
                ],
            ],
            [
                'city' => 'Tanta', 'slug' => 'tanta', 'city_ar' => 'طنطا',
                'zones' => [
                    ['name' => 'Tanta', 'slug' => 'tanta', 'name_ar' => 'طنطا'],
                ],
            ],
            [
                'city' => 'Mansoura', 'slug' => 'mansoura', 'city_ar' => 'المنصورة',
                'zones' => [
                    ['name' => 'Mansoura', 'slug' => 'mansoura', 'name_ar' => 'المنصورة'],
                ],
            ],
            [
                'city' => 'Zagazig', 'slug' => 'zagazig', 'city_ar' => 'الزقازيق',
                'zones' => [
                    ['name' => 'Zagazig', 'slug' => 'zagazig', 'name_ar' => 'الزقازيق'],
                ],
            ],
            [
                'city' => 'Ismailia', 'slug' => 'ismailia', 'city_ar' => 'الإسماعيلية',
                'zones' => [
                    ['name' => 'Ismailia', 'slug' => 'ismailia', 'name_ar' => 'الإسماعيلية'],
                ],
            ],
            [
                'city' => 'Assiut', 'slug' => 'assiut', 'city_ar' => 'أسيوط',
                'zones' => [
                    ['name' => 'Assiut', 'slug' => 'assiut', 'name_ar' => 'أسيوط'],
                ],
            ],
            [
                'city' => 'Port Said', 'slug' => 'port-said', 'city_ar' => 'بورسعيد',
                'zones' => [
                    ['name' => 'Port Said', 'slug' => 'port-said', 'name_ar' => 'بورسعيد'],
                ],
            ],
            [
                'city' => 'Suez', 'slug' => 'suez', 'city_ar' => 'السويس',
                'zones' => [
                    ['name' => 'Suez', 'slug' => 'suez', 'name_ar' => 'السويس'],
                ],
            ],
            [
                'city' => 'Damietta', 'slug' => 'damietta', 'city_ar' => 'دمياط',
                'zones' => [
                    ['name' => 'Damietta', 'slug' => 'damietta', 'name_ar' => 'دمياط'],
                ],
            ],
            [
                'city' => 'Beni Suef', 'slug' => 'beni-suef', 'city_ar' => 'بني سويف',
                'zones' => [
                    ['name' => 'Beni Suef', 'slug' => 'beni-suef', 'name_ar' => 'بني سويف'],
                ],
            ],
            [
                'city' => 'Minia', 'slug' => 'minia', 'city_ar' => 'المنيا',
                'zones' => [
                    ['name' => 'Minia', 'slug' => 'minia', 'name_ar' => 'المنيا'],
                ],
            ],
            [
                'city' => 'Sohag', 'slug' => 'sohag', 'city_ar' => 'سوهاج',
                'zones' => [
                    ['name' => 'Sohag', 'slug' => 'sohag', 'name_ar' => 'سوهاج'],
                ],
            ],
            [
                'city' => 'Qena', 'slug' => 'qena', 'city_ar' => 'قنا',
                'zones' => [
                    ['name' => 'Qena', 'slug' => 'qena', 'name_ar' => 'قنا'],
                ],
            ],
            [
                'city' => 'Luxor', 'slug' => 'luxor', 'city_ar' => 'الأقصر',
                'zones' => [
                    ['name' => 'Luxor', 'slug' => 'luxor', 'name_ar' => 'الأقصر'],
                ],
            ],
            [
                'city' => 'Aswan', 'slug' => 'aswan', 'city_ar' => 'أسوان',
                'zones' => [
                    ['name' => 'Aswan', 'slug' => 'aswan', 'name_ar' => 'أسوان'],
                ],
            ],
            [
                'city' => 'Red Sea', 'slug' => 'Red-Sea', 'city_ar' => 'البحر الأحمر',
                'zones' => [
                    ['name' => 'Hurghada', 'slug' => 'hurghada', 'name_ar' => 'الغردقة'],
                    ['name' => 'Sharm El Sheikh', 'slug' => 'sharm-el-sheikh', 'name_ar' => 'شرم الشيخ'],
                ],
            ],
            [
                'city' => 'Kafr El Sheikh', 'slug' => 'kafr-el-sheikh', 'city_ar' => 'كفر الشيخ',
                'zones' => [
                    ['name' => 'Kafr El Sheikh', 'slug' => 'kafr-el-sheikh', 'name_ar' => 'كفر الشيخ'],
                ],
            ],
            [
                'city' => 'Sharqia', 'slug' => 'sharqia', 'city_ar' => 'الشرقية',
                'zones' => [
                    ['name' => '10th of Ramadan', 'slug' => '10th-of-ramadan', 'name_ar' => 'العاشر من رمضان'],
                ],
            ],
            [
                'city' => 'Fayoum', 'slug' => 'fayoum', 'city_ar' => 'الفيوم',
                'zones' => [
                    ['name' => 'Fayoum', 'slug' => 'fayoum', 'name_ar' => 'الفيوم'],
                ],
            ],
        ];

        return array_map(
            fn(array $data) => new LocationData(
                cityName: $data['city'],
                citySlug: $data['slug'],
                cityNameAr: $data['city_ar'] ?? null,
                zones: $data['zones'],
            ),
            $knownData,
        );
    }
}
