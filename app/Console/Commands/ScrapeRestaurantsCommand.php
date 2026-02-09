<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\Scraper\ScrapeRestaurantListingsJob;
use App\Models\City;
use Illuminate\Console\Command;

/**
 * Scrape restaurant listings for all (or specific) cities and zones.
 *
 * Usage:
 *   php artisan scrape:restaurants
 *   php artisan scrape:restaurants --city=tanta
 *   php artisan scrape:restaurants --city=tanta --zone=tanta --sync
 */
class ScrapeRestaurantsCommand extends Command
{
    protected $signature = 'scrape:restaurants
                            {--city= : Scrape a specific city}
                            {--zone= : Scrape a specific zone}
                            {--sync : Run synchronously}
                            {--max-pages=10 : Max pages per listing}';

    protected $description = 'Scrape restaurant listings from menuegypt.com';

    public function handle(): int
    {
        $citySlug = $this->option('city');
        $zoneSlug = $this->option('zone');
        $sync = $this->option('sync');
        $maxPages = (int) $this->option('max-pages');

        $query = City::with('zones');

        if ($citySlug) {
            $query->where('slug', $citySlug);
        }

        $cities = $query->get();

        if ($cities->isEmpty()) {
            $this->error('No cities found. Run "php artisan scrape:all" first.');
            return self::FAILURE;
        }

        $totalJobs = 0;

        foreach ($cities as $city) {
            $zones = $city->zones;

            if ($zoneSlug) {
                $zones = $zones->where('slug', $zoneSlug);
            }

            foreach ($zones as $zone) {
                $this->info("📋 Dispatching: {$city->slug}/{$zone->slug}");

                if ($sync) {
                    dispatch_sync(new ScrapeRestaurantListingsJob($city->slug, $zone->slug, $maxPages));
                } else {
                    ScrapeRestaurantListingsJob::dispatch($city->slug, $zone->slug, $maxPages)
                        ->onQueue('scraper')
                        ->delay(now()->addSeconds($totalJobs * 5));
                }

                $totalJobs++;
            }
        }

        $this->info("✅ {$totalJobs} listing scraping jobs dispatched.");

        return self::SUCCESS;
    }
}
