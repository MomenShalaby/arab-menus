<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\Scraper\ScrapeLocationsJob;
use App\Jobs\Scraper\ScrapeRestaurantDetailJob;
use App\Jobs\Scraper\ScrapeRestaurantListingsJob;
use App\Models\City;
use App\Models\Restaurant;
use App\Models\Zone;
use Illuminate\Console\Command;

/**
 * Artisan command to trigger the full scraping pipeline.
 *
 * Usage examples:
 *   php artisan scrape:all                        - Scrape everything
 *   php artisan scrape:all --city=tanta            - Scrape only Tanta
 *   php artisan scrape:all --city=tanta --zone=tanta - Scrape Tanta zone in Tanta
 *   php artisan scrape:all --sync                  - Run synchronously (no queue)
 */
class ScrapeAllCommand extends Command
{
    protected $signature = 'scrape:all
                            {--city= : Scrape a specific city by slug}
                            {--zone= : Scrape a specific zone by slug}
                            {--sync : Run synchronously instead of via queue}
                            {--max-pages=10 : Maximum pages to scrape per listing}';

    protected $description = 'Scrape restaurant data from menuegypt.com';

    public function handle(): int
    {
        $this->info('🕷️  Starting scraping pipeline...');
        $this->newLine();

        $sync = $this->option('sync');
        $maxPages = (int) $this->option('max-pages');

        // Step 1: Scrape locations
        $this->info('📍 Step 1: Scraping locations (cities & zones)...');

        if ($sync) {
            dispatch_sync(new ScrapeLocationsJob());
        } else {
            ScrapeLocationsJob::dispatch()->onQueue('scraper');
        }

        $this->info('   ✅ Locations job dispatched.');
        $this->newLine();

        // Wait a moment for locations to be populated if running sync
        if ($sync) {
            $this->scrapeRestaurants($sync, $maxPages);
        } else {
            // When using queues, dispatch after a delay so locations are ready
            $this->info('📋 Step 2: Dispatching restaurant listing jobs...');
            $this->info('   Jobs will be dispatched after locations are scraped.');
            $this->info('   Make sure queue worker is running: php artisan queue:work --queue=scraper');
            $this->newLine();

            // Schedule the restaurant scraping
            $this->scheduleRestaurantJobs($maxPages);
        }

        $this->info('🎉 Scraping pipeline initialized!');

        return self::SUCCESS;
    }

    private function scrapeRestaurants(bool $sync, int $maxPages): void
    {
        $this->info('📋 Step 2: Scraping restaurant listings...');

        $citySlug = $this->option('city');
        $zoneSlug = $this->option('zone');

        if ($citySlug) {
            $zones = $this->getZonesForCity($citySlug, $zoneSlug);
            $this->processZones($citySlug, $zones, $sync, $maxPages);
        } else {
            // Scrape all cities
            $cities = City::with('zones')->get();

            if ($cities->isEmpty()) {
                $this->warn('   No cities found. Location scraping may have failed.');
                return;
            }

            $bar = $this->output->createProgressBar($cities->count());
            $bar->start();

            foreach ($cities as $city) {
                foreach ($city->zones as $zone) {
                    $this->processZone($city->slug, $zone->slug, $sync, $maxPages);
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
        }

        // Step 3: Scrape restaurant details
        $this->info('🍽️  Step 3: Scraping restaurant details...');

        $restaurants = Restaurant::whereNull('last_scraped_at')->get();
        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            if ($sync) {
                dispatch_sync(new ScrapeRestaurantDetailJob($restaurant->slug));
            } else {
                ScrapeRestaurantDetailJob::dispatch($restaurant->slug)
                    ->onQueue('scraper')
                    ->delay(now()->addSeconds(rand(1, 5)));
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    /**
     * @return \Illuminate\Support\Collection<int, Zone>
     */
    private function getZonesForCity(string $citySlug, ?string $zoneSlug): \Illuminate\Support\Collection
    {
        $query = Zone::whereHas('city', fn($q) => $q->where('slug', $citySlug));

        if ($zoneSlug) {
            $query->where('slug', $zoneSlug);
        }

        return $query->get();
    }

    /**
     * @param \Illuminate\Support\Collection<int, Zone> $zones
     */
    private function processZones(string $citySlug, \Illuminate\Support\Collection $zones, bool $sync, int $maxPages): void
    {
        foreach ($zones as $zone) {
            $this->processZone($citySlug, $zone->slug, $sync, $maxPages);
        }
    }

    private function processZone(string $citySlug, string $zoneSlug, bool $sync, int $maxPages): void
    {
        $this->line("   Scraping: {$citySlug}/{$zoneSlug}");

        if ($sync) {
            dispatch_sync(new ScrapeRestaurantListingsJob($citySlug, $zoneSlug, $maxPages));
        } else {
            ScrapeRestaurantListingsJob::dispatch($citySlug, $zoneSlug, $maxPages)
                ->onQueue('scraper');
        }
    }

    private function scheduleRestaurantJobs(int $maxPages): void
    {
        $citySlug = $this->option('city');
        $zoneSlug = $this->option('zone');

        if ($citySlug) {
            if ($zoneSlug) {
                ScrapeRestaurantListingsJob::dispatch($citySlug, $zoneSlug, $maxPages)
                    ->onQueue('scraper')
                    ->delay(now()->addSeconds(10));
            } else {
                // Will need to wait for zones to be populated
                $this->info("   Will scrape all zones in city: {$citySlug}");
                // Dispatch a delayed job that discovers zones
                ScrapeRestaurantListingsJob::dispatch($citySlug, $citySlug, $maxPages)
                    ->onQueue('scraper')
                    ->delay(now()->addSeconds(15));
            }
        } else {
            $this->info('   All city/zone combinations will be scraped.');
            $this->info('   Run "php artisan scrape:restaurants" after locations are ready.');
        }
    }
}
