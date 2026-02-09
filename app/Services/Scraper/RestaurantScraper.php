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

            // Free crawler memory immediately
            unset($crawler);

            if ($data !== null) {
                $this->persistRestaurantDetail($data);
                $log->markCompleted(1);
            } else {
                $log->markFailed("No data extracted for: {$slug}");
            }

            // Free memory
            gc_collect_cycles();

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
                    // Clean up common suffixes/prefixes from page titles
                    $name = preg_replace('/^Menu Egypt\s+/i', '', $name);
                    $name = preg_replace('/\s+(hotline|number|delivery|menu|egypt|phone|order|prices?)[\s,].*$/i', '', $name);
                    $name = preg_replace('/\s+(Cafe|Restaurant|Restaurants)\s+(menu|hotline|delivery).*$/i', '', $name);
                    $name = preg_replace('/\s+menu\s*,?.*$/i', '', $name);
                    $name = preg_replace('/\s+menu$/i', '', $name);
                    $name = rtrim(trim($name), ',');
                }
            } catch (\Exception) {
                // fallback
            }

            // Try to get cleaner name from title tag or og:title
            if (empty($name) || strlen($name) > 60) {
                try {
                    $ogTitle = $crawler->filter('meta[property="og:title"]');
                    if ($ogTitle->count() > 0) {
                        $ogName = trim($ogTitle->first()->attr('content') ?? '');
                        $ogName = preg_replace('/\s+(menu|hotline|delivery|egypt).*$/i', '', $ogName);
                        $ogName = rtrim(trim($ogName), ',');
                        if (!empty($ogName) && strlen($ogName) < strlen($name)) {
                            $name = $ogName;
                        }
                    }
                } catch (\Exception) {}
            }

            if (empty($name)) {
                $name = ucwords(str_replace('-', ' ', $slug));
            }

            // Extract Arabic name (name_ar)
            // Strategy: The English page embeds the Arabic name in several places:
            // 1. mailto: sharing link has subject=الاسم_بالعربي%20menu
            // 2. Branch URLs contain /slug/الاسم_بالعربي-فرع/category
            // 3. "اكتشاف المزيد" section sometimes has the Arabic name
            $nameAr = null;
            try {
                $pageHtml = $crawler->html();

                // Method 1: Extract from mailto: subject line (most reliable)
                // Pattern: mailto:?subject=كنتاكى%20menu or subject=كنتاكى menu
                if (preg_match('/mailto:\?subject=([^&"]+)/i', $pageHtml, $mailMatch)) {
                    $subject = urldecode($mailMatch[1]);
                    // Remove " menu and contacts" or similar suffixes
                    $subject = preg_replace('/\s*(menu|and|contacts|delivery|hotline).*$/i', '', $subject);
                    $subject = trim($subject);
                    // Check if it actually contains Arabic characters
                    if (preg_match('/[\x{0600}-\x{06FF}]/u', $subject)) {
                        $nameAr = $subject;
                    }
                }

                // Method 2: Extract from branch URLs if mailto didn't work
                if (empty($nameAr)) {
                    // Branch URLs look like: /kfc/كنتاكى-فرع-اسم/fried-chicken
                    // The Arabic slug is the second segment, URL-encoded
                    if (preg_match('#/' . preg_quote($slug, '#') . '/([^/"]+)/#', $pageHtml, $branchMatch)) {
                        $arSlug = urldecode($branchMatch[1]);
                        // The Arabic slug is like كنتاكى-فرع-المكان — take the first word before the dash
                        $parts = explode('-', $arSlug);
                        // Collect Arabic-only parts from the start (the restaurant name part)
                        $arParts = [];
                        foreach ($parts as $part) {
                            $part = trim($part);
                            if (preg_match('/^[\x{0600}-\x{06FF}\s]+$/u', $part)) {
                                $arParts[] = $part;
                            } else {
                                break; // Stop at first non-Arabic part (branch location)
                            }
                        }
                        if (!empty($arParts)) {
                            $nameAr = implode(' ', $arParts);
                        }
                    }
                }

                // Method 3: Extract from WhatsApp sharing link
                if (empty($nameAr)) {
                    if (preg_match('/whatsapp:\/\/send\?text=[^"]*?for\s+([^"]+?)\s+menu/i', $pageHtml, $waMatch)) {
                        $waName = urldecode(trim($waMatch[1]));
                        if (preg_match('/[\x{0600}-\x{06FF}]/u', $waName)) {
                            $nameAr = $waName;
                        }
                    }
                }

                // Clean up Arabic name
                if ($nameAr) {
                    $nameAr = trim($nameAr);
                    // Remove common suffixes/prefixes that aren't part of the name
                    $nameAr = preg_replace('/\s*(منيو|قائمة|مطعم|مطاعم|فرع|رقم|هوتلاين|ديليفري)\s*$/u', '', $nameAr);
                    $nameAr = trim($nameAr);
                    if (mb_strlen($nameAr) < 2 || mb_strlen($nameAr) > 60) {
                        $nameAr = null;
                    }
                }
            } catch (\Exception) {
                // No Arabic name found
            }

            // Extract hotline/phone number - get ALL phone numbers
            $hotline = null;
            try {
                $telLinks = $crawler->filter('a[href^="tel:"]');
                if ($telLinks->count() > 0) {
                    $phones = [];
                    $telLinks->each(function (Crawler $telLink) use (&$phones): void {
                        $phone = trim($telLink->text(''));
                        $phone = preg_replace('/[^0-9+\-\s]/', '', $phone);
                        $phone = trim($phone);
                        if (!empty($phone) && !in_array($phone, $phones)) {
                            $phones[] = $phone;
                        }
                    });
                    $hotline = !empty($phones) ? implode(' - ', $phones) : null;
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

            // Extract menu images (full-size only, convert _s thumbnails to full-size)
            $menuImageUrls = [];
            try {
                // First try: extract full-size URLs from JS/JSON arrays on the page
                $pageHtml = $crawler->html();
                preg_match_all('#https?://[^"\s]+/restaurants_menus/[^"\s]+#', $pageHtml, $fullMatches);
                if (!empty($fullMatches[0])) {
                    foreach ($fullMatches[0] as $url) {
                        $url = strtok($url, '?') ?: $url;
                        if (!str_contains($url, 'restaurants_menus_s')) {
                            $menuImageUrls[] = $url;
                        }
                    }
                }

                // If no full-size images found in JS, get from img tags and convert _s to full
                if (empty($menuImageUrls)) {
                    $allMenuSrcs = [];
                    $crawler->filter('img[src*="restaurants_menus"]')->each(
                        function (Crawler $img) use (&$allMenuSrcs): void {
                            $src = $img->attr('src') ?? '';
                            if (!empty($src) && str_contains($src, 'restaurants_menus')) {
                                $allMenuSrcs[] = $src;
                            }
                        }
                    );

                    foreach ($allMenuSrcs as $src) {
                        // Skip tiny icons or ads
                        if (str_contains($src, 'icon') || str_contains($src, 'ad_')) {
                            continue;
                        }
                        // Strip cache-busting query strings
                        $src = strtok($src, '?') ?: $src;
                        // Convert _s thumbnail to full-size:
                        // restaurants_menus_s/slug_menu_1_s.jpg -> restaurants_menus/slug_menu_1.jpg
                        $src = str_replace('restaurants_menus_s/', 'restaurants_menus/', $src);
                        $src = preg_replace('/_s\.(jpg|png|jpeg|webp)$/i', '.$1', $src);
                        if (!str_starts_with($src, 'http')) {
                            $src = $this->httpClient->resolveUrl($src);
                        }
                        $menuImageUrls[] = $src;
                    }
                }

                // Try to find additional menu images by pattern if none found
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
            $menuImageUrls = array_values($menuImageUrls);

            // Extract "Updated on" date
            $updatedAtSource = null;
            try {
                $pageText = $crawler->filter('body')->text('');
                if (preg_match('/Updated on[:\s]*(\d{4}-\d{2}-\d{2})/i', $pageText, $m)) {
                    $updatedAtSource = $m[1];
                }
            } catch (\Exception) {
                // no date found
            }

            // Extract branches
            $branches = [];
            try {
                // Branches are often under h5 tags with address text, followed by links
                $crawler->filter('h5')->each(function (Crawler $h5) use (&$branches, $slug): void {
                    $address = trim($h5->text(''));
                    if (empty($address) || strlen($address) < 3) {
                        return;
                    }
                    // Skip if this h5 is just a section header (not an address)
                    if (str_contains(strtolower($address), 'menu') || str_contains(strtolower($address), 'category')) {
                        return;
                    }
                    // Find the next sibling or nearby link for branch name
                    $branchName = null;
                    try {
                        $parent = $h5->closest('div');
                        if ($parent && $parent->count() > 0) {
                            $link = $parent->filter('a');
                            if ($link->count() > 0) {
                                $branchName = trim($link->first()->text(''));
                            }
                        }
                    } catch (\Exception) {
                        // no branch name found
                    }
                    $branches[] = [
                        'name' => $branchName ?: $address,
                        'address' => $address,
                    ];
                });
            } catch (\Exception) {
                // no branches
            }

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
                'name_ar' => $nameAr,
                'slug' => $slug,
                'logo_url' => $logoUrl,
                'hotline' => $hotline,
                'source_url' => $this->httpClient->resolveUrl("/{$slug}"),
                'categories' => array_filter($categories),
                'menu_image_urls' => array_values($menuImageUrls),
                'updated_at_source' => $updatedAtSource,
                'branches' => $branches,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to extract restaurant detail for {$slug}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Known Arabic translations for common food categories.
     */
    private const CATEGORY_AR_MAP = [
        'pizza' => 'بيتزا',
        'burgers' => 'برجر',
        'burger' => 'برجر',
        'chicken' => 'دجاج / فراخ',
        'sandwiches' => 'ساندوتشات',
        'sandwich' => 'ساندوتشات',
        'seafood' => 'مأكولات بحرية',
        'grills' => 'مشويات',
        'grill' => 'مشويات',
        'oriental' => 'أكل شرقي',
        'desserts' => 'حلويات',
        'dessert' => 'حلويات',
        'sweets' => 'حلويات',
        'pastries' => 'معجنات',
        'pasta' => 'مكرونة / باستا',
        'crepe' => 'كريب',
        'crepes' => 'كريب',
        'shawerma' => 'شاورما',
        'shawarma' => 'شاورما',
        'koshary' => 'كشري',
        'koshari' => 'كشري',
        'kushary' => 'كشري',
        'kebab' => 'كباب',
        'foul' => 'فول',
        'falafel' => 'فلافل / طعمية',
        'taameya' => 'فلافل / طعمية',
        'juice' => 'عصائر',
        'juices' => 'عصائر',
        'drinks' => 'مشروبات',
        'beverages' => 'مشروبات',
        'coffee' => 'قهوة',
        'cafe' => 'كافيه',
        'breakfast' => 'إفطار',
        'sushi' => 'سوشي',
        'chinese' => 'أكل صيني',
        'asian' => 'أكل آسيوي',
        'indian' => 'أكل هندي',
        'italian' => 'أكل إيطالي',
        'syrian' => 'أكل سوري',
        'lebanese' => 'أكل لبناني',
        'egyptian' => 'أكل مصري',
        'fateer' => 'فطير',
        'fiteer' => 'فطير',
        'hawawshi' => 'حواوشي',
        'liver' => 'كبدة',
        'ice cream' => 'آيس كريم',
        'waffle' => 'وافل',
        'waffles' => 'وافل',
        'cake' => 'كيك',
        'cakes' => 'كيك',
        'meals' => 'وجبات',
        'meal' => 'وجبات',
        'fried chicken' => 'دجاج مقلي',
        'broasted' => 'بروستد',
        'hot dog' => 'هوت دوج',
        'wraps' => 'لفائف',
        'salad' => 'سلطات',
        'salads' => 'سلطات',
        'soup' => 'شوربة',
        'soups' => 'شوربة',
        'appetizers' => 'مقبلات',
        'sides' => 'أطباق جانبية',
        'rice' => 'أرز',
        'meat' => 'لحوم',
        'fish' => 'أسماك',
        'family meals' => 'وجبات عائلية',
        'kids meals' => 'وجبات أطفال',
    ];

    /**
     * Get Arabic name for a category.
     */
    private function getCategoryArName(string $categoryName): ?string
    {
        $lower = strtolower(trim($categoryName));
        return self::CATEGORY_AR_MAP[$lower] ?? null;
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
                    [
                        'name' => $categoryName,
                        'name_ar' => $this->getCategoryArName($categoryName),
                    ],
                );

                // Update AR name if missing
                if (empty($category->name_ar) && $arName = $this->getCategoryArName($categoryName)) {
                    $category->update(['name_ar' => $arName]);
                }

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
                'updated_at_source' => $data->updatedAtSource,
                'last_scraped_at' => now(),
            ],
        );

        // Persist branches
        if (! empty($data->branches)) {
            foreach ($data->branches as $branchData) {
                \App\Models\Branch::updateOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'name' => $branchData['name'] ?? $branchData['address'] ?? 'Branch',
                    ],
                    [
                        'address' => $branchData['address'] ?? null,
                    ],
                );
            }
        }

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
