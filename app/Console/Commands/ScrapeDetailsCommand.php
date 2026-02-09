<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\Scraper\ScrapeRestaurantDetailJob;
use App\Models\Restaurant;
use Illuminate\Console\Command;

/**
 * Scrape details and menu images for restaurants.
 *
 * Usage:
 *   php artisan scrape:details                    - Scrape all un-scraped
 *   php artisan scrape:details --restaurant=kfc    - Scrape specific restaurant
 *   php artisan scrape:details --force             - Re-scrape all restaurants
 *   php artisan scrape:details --sync              - Run synchronously
 */
class ScrapeDetailsCommand extends Command
{
    protected $signature = 'scrape:details
                            {--restaurant= : Scrape a specific restaurant by slug}
                            {--force : Re-scrape even if already scraped}
                            {--sync : Run synchronously}';

    protected $description = 'Scrape restaurant details and menu images';

    public function handle(): int
    {
        $slug = $this->option('restaurant');
        $force = $this->option('force');
        $sync = $this->option('sync');

        $query = Restaurant::query();

        if ($slug) {
            $query->where('slug', $slug);
        } elseif (! $force) {
            $query->whereNull('last_scraped_at');
        }

        $restaurants = $query->get();

        if ($restaurants->isEmpty()) {
            $this->info('No restaurants to scrape.');
            return self::SUCCESS;
        }

        $this->info("🍽️  Scraping details for {$restaurants->count()} restaurants...");
        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            if ($sync) {
                dispatch_sync(new ScrapeRestaurantDetailJob($restaurant->slug));
            } else {
                ScrapeRestaurantDetailJob::dispatch($restaurant->slug)
                    ->onQueue('scraper');
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('✅ Detail scraping jobs dispatched.');

        return self::SUCCESS;
    }
}
