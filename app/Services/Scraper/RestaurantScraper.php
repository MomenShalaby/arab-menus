<?php

declare(strict_types=1);

namespace App\Services\Scraper;

use App\DTOs\RestaurantData;
use App\Models\Category;
use App\Models\City;
use App\Models\MenuImage;
use App\Models\Restaurant;
use App\Models\ScrapingLog;
use App\Models\Zone;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Scrapes restaurant listings and individual restaurant pages
 * from menuegypt.com. Handles pagination, deduplication, and
 * image downloading.
 */
class RestaurantScraper
{
    public function __construct(
        private readonly HttpClientService $httpClient,
    ) {}

    /**
     * Scrape restaurant listings for a specific city and zone.
     *
     * @return RestaurantData[]
     */
    public function scrapeListings(string $citySlug, string $zoneSlug, int $maxPages = 10): array
    {
        $baseUrl = "/menus/{$citySlug}/{$zoneSlug}//restaurants-menus-hotline-delivery-number";

        $log = ScrapingLog::create([
            'type' => 'restaurant_listing',
            'url' => $this->httpClient->resolveUrl($baseUrl),
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            $allRestaurants = [];
            $page = 1;

            while ($page <= $maxPages) {
                $url = $page === 1
                    ? $baseUrl
                    : "{$baseUrl}?page={$page}";

                $crawler = $this->httpClient->fetchPage($url);

                if ($crawler === null) {
                    Log::warning("Failed to fetch listing page {$page} for {$citySlug}/{$zoneSlug}");
                    break;
                }

                $restaurants = $this->extractRestaurantsFromListing($crawler);

                if (empty($restaurants)) {
                    break; // No more restaurants, stop pagination
                }

                $allRestaurants = array_merge($allRestaurants, $restaurants);

                // Check for next page link
                $hasNextPage = $crawler->filter('a[href*="page="]')->count() > 0
                    && $crawler->filter('a:contains("Next"), a:contains("»")')->count() > 0;

                if (! $hasNextPage) {
                    break;
                }

                $page++;
            }

            // Persist restaurants with city/zone associations
            $this->persistListingData($allRestaurants, $citySlug, $zoneSlug);

            $log->markCompleted(count($allRestaurants));

            return $allRestaurants;
        } catch (\Exception $e) {
            Log::error("Restaurant listing scraping failed: {$e->getMessage()}");
            $log->markFailed($e->getMessage());
            return [];
        }
    }

    /**
     * Extract restaurant data from a listing page.
     *
     * @return RestaurantData[]
     */
    private function extractRestaurantsFromListing(Crawler $crawler): array
    {
        $restaurants = [];

        // Restaurants are linked as cards with logos and names
        // Pattern: <a href="/restaurant-slug">Logo Name Categories</a>
        $restaurantLinks = $crawler->filter('a[href]');

        $baseUrl = $this->httpClient->getBaseUrl();

        $restaurantLinks->each(function (Crawler $node) use (&$restaurants, $baseUrl): void {
            $href = $node->attr('href') ?? '';

            if (empty($href)) {
                return;
            }

            // Normalize: convert absolute URLs from the same domain to relative paths
            if (str_starts_with($href, $baseUrl)) {
                $href = substr($href, strlen($baseUrl));
            }

            // Filter out menus/listing links and external links (other domains)
            if (str_contains($href, '/menus/') || (str_contains($href, 'http') && ! str_starts_with($href, '/'))) {
                return;
            }

            // Clean the href to get the slug
            $slug = trim($href, '/');

            // Skip non-restaurant links
            $skipPrefixes = [
                'about', 'user_panel', 'order', 'ar', 'sitefiles',
                'restaurants_menus', 'restaurants_logos', '#',
            ];
            foreach ($skipPrefixes as $prefix) {
                if (str_starts_with($slug, $prefix)) {
                    return;
                }
            }

            // Must be a simple slug (no slashes after trimming)
            if (str_contains($slug, '/') && ! str_contains($slug, '%')) {
                return;
            }

            // Extract restaurant name from h3.media-heading or fallback to link text
            $name = '';
            $categories = [];

            try {
                $heading = $node->filter('h3.media-heading, .media-heading, .Media-heading2 h3');
                if ($heading->count() > 0) {
                    $name = trim($heading->first()->text(''));
                }
            } catch (\Exception) {
                // fallback below
            }

            // Extract categories from h3.p_id or similar element
            try {
                $catNode = $node->filter('h3.p_id, .Media-parag2 h3, .Media-parag2');
                if ($catNode->count() > 0) {
                    $catText = trim($catNode->first()->text(''));
                    $categories = array_map('trim', explode(',', $catText));
                    $categories = array_filter($categories, fn(string $c): bool => ! empty($c) && strlen($c) > 1);
                    $categories = array_values($categories);
                }
            } catch (\Exception) {
                // no categories
            }

            // Fallback: parse from raw text
            if (empty($name)) {
                $text = trim($node->text(''));
                if (empty($text) || strlen($text) < 2) {
                    return;
                }
                $parts = explode("\n", $text);
                $name = trim($parts[0] ?? $text);
                $name = preg_replace('/^Logo\s+/i', '', $name);

                if (empty($categories) && count($parts) > 1) {
                    $catText = trim(end($parts));
                    $categories = array_map('trim', explode(',', $catText));
                    $categories = array_filter($categories, fn(string $c): bool => ! empty($c) && strlen($c) > 1);
                    $categories = array_values($categories);
                }
            }

            if (empty($name) || strlen($name) < 2) {
                return;
            }

            // Extract logo URL
            $logoUrl = null;
            try {
                $img = $node->filter('img');
                if ($img->count() > 0) {
                    $logoUrl = $img->first()->attr('src');
                    if ($logoUrl && ! str_starts_with($logoUrl, 'http')) {
                        $logoUrl = $this->httpClient->resolveUrl($logoUrl);
                    }
                }
            } catch (\Exception) {
                // No image found, that's fine
            }

            // Avoid duplicate entries
            if (isset($restaurants[$slug])) {
                return;
            }

            $restaurants[$slug] = RestaurantData::fromArray([
                'name' => $name,
                'slug' => $slug,
                'logo_url' => $logoUrl,
                'source_url' => $this->httpClient->resolveUrl("/{$slug}"),
                'categories' => $categories,
            ]);
        });

        return array_values($restaurants);
    }

    /**
     * Scrape a single restaurant's detail page.
     */
    public function scrapeRestaurantDetail(string $slug): ?RestaurantData
    {
        $url = "/{$slug}";

        $log = ScrapingLog::create([
            'type' => 'restaurant_detail',
            'url' => $this->httpClient->resolveUrl($url),
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            $crawler = $this->httpClient->fetchPage($url);

            if ($crawler === null) {
                $log->markFailed("Failed to fetch restaurant page: {$slug}");
                return null;
            }

            $data = $this->extractRestaurantDetail($crawler, $slug);

            if ($data !== null) {
                $this->persistRestaurantDetail($data);
                $log->markCompleted(1);
            } else {
                $log->markFailed("No data extracted for: {$slug}");
            }

            return $data;
        } catch (\Exception $e) {
            Log::error("Restaurant detail scraping failed for {$slug}: {$e->getMessage()}");
            $log->markFailed($e->getMessage());
            return null;
        }
    }

    /**
     * Extract detailed restaurant data from its page.
     */
    private function extractRestaurantDetail(Crawler $crawler, string $slug): ?RestaurantData
    {
        try {
            // Extract restaurant name from h1 or title
            $name = '';
            try {
                $h1 = $crawler->filter('h1');
                if ($h1->count() > 0) {
                    $name = trim($h1->first()->text(''));
                    // Clean up "Menu Egypt Restaurant hotline..." prefix
                    $name = preg_replace('/^Menu Egypt\s+/i', '', $name);
                    $name = preg_replace('/\s+hotline.*$/i', '', $name);
                    $name = preg_replace('/\s+menu$/i', '', $name);
                    $name = trim($name);
                }
            } catch (\Exception) {
                // fallback
            }

            if (empty($name)) {
                $name = ucwords(str_replace('-', ' ', $slug));
            }

            // Extract hotline/phone number
            $hotline = null;
            try {
                $telLinks = $crawler->filter('a[href^="tel:"]');
                if ($telLinks->count() > 0) {
                    $hotline = trim($telLinks->first()->text(''));
                    // Clean up
                    $hotline = preg_replace('/[^0-9+]/', '', $hotline);
                }
            } catch (\Exception) {
                // No hotline found
            }

            // Extract logo URL
            $logoUrl = null;
            try {
                $logoImgs = $crawler->filter('img[src*="restaurants_logos"]');
                if ($logoImgs->count() > 0) {
                    $logoUrl = $logoImgs->first()->attr('src');
                    if ($logoUrl && ! str_starts_with($logoUrl, 'http')) {
                        $logoUrl = $this->httpClient->resolveUrl($logoUrl);
                    }
                }
            } catch (\Exception) {
                // No logo found
            }

            // Extract menu images
            $menuImageUrls = [];
            try {
                // Menu images are in img tags with src containing "restaurants_menus"
                $menuImgs = $crawler->filter('img[src*="restaurants_menus"]');
                $menuImgs->each(function (Crawler $img) use (&$menuImageUrls): void {
                    $src = $img->attr('src') ?? $img->attr('data-src') ?? '';
                    if (! empty($src)) {
                        if (! str_starts_with($src, 'http')) {
                            $src = $this->httpClient->resolveUrl($src);
                        }
                        // Skip tiny icons or ads
                        if (! str_contains($src, 'icon') && ! str_contains($src, 'ad_')) {
                            $menuImageUrls[] = $src;
                        }
                    }
                });

                // Also check for lazy-loaded images (data-src)
                $lazyImgs = $crawler->filter('img[data-src*="restaurants_menus"]');
                $lazyImgs->each(function (Crawler $img) use (&$menuImageUrls): void {
                    $src = $img->attr('data-src') ?? '';
                    if (! empty($src) && ! in_array($src, $menuImageUrls, true)) {
                        if (! str_starts_with($src, 'http')) {
                            $src = $this->httpClient->resolveUrl($src);
                        }
                        $menuImageUrls[] = $src;
                    }
                });

                // Try to find additional menu images by pattern
                // Pattern: /restaurants_menus/{slug}_menu_{n}.jpg
                if (empty($menuImageUrls)) {
                    for ($i = 1; $i <= 20; $i++) {
                        $testUrl = $this->httpClient->resolveUrl(
                            "/restaurants_menus/{$slug}_menu_{$i}.jpg"
                        );
                        $menuImageUrls[] = $testUrl;
                    }
                }
            } catch (\Exception) {
                // Fallback: try common patterns
            }

            // Remove duplicates
            $menuImageUrls = array_unique($menuImageUrls);

            // Extract categories from page text
            $categories = [];
            try {
                // Categories often appear in the meta or breadcrumb area
                $categoryNodes = $crawler->filter('.category, .categories, [class*="categ"]');
                $categoryNodes->each(function (Crawler $node) use (&$categories): void {
                    $text = trim($node->text(''));
                    if (! empty($text)) {
                        $cats = array_map('trim', explode(',', $text));
                        $categories = array_merge($categories, $cats);
                    }
                });
            } catch (\Exception) {
                // No categories found
            }

            return RestaurantData::fromArray([
                'name' => $name,
                'slug' => $slug,
                'logo_url' => $logoUrl,
                'hotline' => $hotline,
                'source_url' => $this->httpClient->resolveUrl("/{$slug}"),
                'categories' => array_filter($categories),
                'menu_image_urls' => array_values($menuImageUrls),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to extract restaurant detail for {$slug}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Persist restaurant listing data with city/zone associations.
     *
     * @param RestaurantData[] $restaurants
     */
    private function persistListingData(array $restaurants, string $citySlug, string $zoneSlug): void
    {
        $city = City::where('slug', $citySlug)->first();
        $zone = Zone::where('slug', $zoneSlug)->first();

        foreach ($restaurants as $data) {
            $restaurant = Restaurant::updateOrCreate(
                ['slug' => $data->slug],
                [
                    'name' => $data->name,
                    'name_ar' => $data->nameAr,
                    'logo_url' => $data->logoUrl,
                    'source_url' => $data->sourceUrl,
                ],
            );

            // Associate with city
            if ($city) {
                $restaurant->cities()->syncWithoutDetaching([$city->id]);
            }

            // Associate with zone
            if ($zone) {
                $restaurant->zones()->syncWithoutDetaching([$zone->id]);
            }

            // Associate categories
            foreach ($data->categories as $categoryName) {
                $categoryName = trim($categoryName);
                if (empty($categoryName) || strlen($categoryName) < 2) {
                    continue;
                }

                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    ['name' => $categoryName],
                );

                $restaurant->categories()->syncWithoutDetaching([$category->id]);
            }
        }
    }

    /**
     * Persist detailed restaurant data including menu images.
     */
    private function persistRestaurantDetail(RestaurantData $data): void
    {
        $restaurant = Restaurant::updateOrCreate(
            ['slug' => $data->slug],
            [
                'name' => $data->name,
                'name_ar' => $data->nameAr,
                'logo_url' => $data->logoUrl,
                'hotline' => $data->hotline,
                'source_url' => $data->sourceUrl,
                'description' => $data->description,
                'last_scraped_at' => now(),
            ],
        );

        // Download and store logo
        if ($data->logoUrl) {
            $this->downloadAndStoreImage(
                $data->logoUrl,
                "logos/{$data->slug}",
                $restaurant,
                'local_logo_path',
            );
        }

        // Process menu images
        $order = 0;
        foreach ($data->menuImageUrls as $imageUrl) {
            $order++;

            // Skip if already exists
            $exists = MenuImage::where('restaurant_id', $restaurant->id)
                ->where('original_url', $imageUrl)
                ->exists();

            if ($exists) {
                continue;
            }

            $menuImage = MenuImage::create([
                'restaurant_id' => $restaurant->id,
                'original_url' => $imageUrl,
                'alt_text' => "{$data->name} menu {$order}",
                'sort_order' => $order,
            ]);

            // Download and store image locally
            $this->downloadMenuImage($imageUrl, $data->slug, $order, $menuImage);
        }
    }

    /**
     * Download and store an image locally.
     */
    private function downloadAndStoreImage(
        string $url,
        string $path,
        Restaurant $restaurant,
        string $field,
    ): void {
        try {
            $contents = $this->httpClient->downloadFile($url);

            if ($contents === null) {
                return;
            }

            $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION) ?: 'jpg';
            $fullPath = "{$path}.{$extension}";

            Storage::disk('public')->put($fullPath, $contents);

            $restaurant->update([$field => $fullPath]);
        } catch (\Exception $e) {
            Log::warning("Failed to download image {$url}: {$e->getMessage()}");
        }
    }

    /**
     * Download and store a menu image locally.
     */
    private function downloadMenuImage(
        string $url,
        string $slug,
        int $order,
        MenuImage $menuImage,
    ): void {
        try {
            $contents = $this->httpClient->downloadFile($url);

            if ($contents === null) {
                return;
            }

            $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION) ?: 'jpg';
            $localPath = "menus/{$slug}/{$slug}_menu_{$order}.{$extension}";

            Storage::disk('public')->put($localPath, $contents);

            // Get image dimensions
            $tempFile = tempnam(sys_get_temp_dir(), 'menu_');
            file_put_contents($tempFile, $contents);
            $imageInfo = @getimagesize($tempFile);
            @unlink($tempFile);

            $updateData = [
                'local_path' => $localPath,
                'file_size' => strlen($contents),
            ];

            if ($imageInfo !== false) {
                $updateData['width'] = $imageInfo[0];
                $updateData['height'] = $imageInfo[1];
            }

            $menuImage->update($updateData);
        } catch (\Exception $e) {
            Log::warning("Failed to download menu image {$url}: {$e->getMessage()}");
        }
    }
}
