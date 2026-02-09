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

        $total = $query->count();

        if ($total === 0) {
            $this->info('No restaurants to scrape.');
            return self::SUCCESS;
        }

        $this->info("🍽️  Scraping details for {$total} restaurants...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        // Use chunk to avoid loading all restaurants into memory at once
        $query->select(['id', 'slug'])->chunk(50, function ($restaurants) use ($sync, $bar) {
            foreach ($restaurants as $restaurant) {
                if ($sync) {
                    try {
                        dispatch_sync(new ScrapeRestaurantDetailJob($restaurant->slug));
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to scrape {$restaurant->slug}: {$e->getMessage()}");
                    }
                } else {
                    ScrapeRestaurantDetailJob::dispatch($restaurant->slug)
                        ->onQueue('scraper');
                }
                $bar->advance();
            }

            // Free memory between chunks
            gc_collect_cycles();
        });

        $bar->finish();
        $this->newLine(2);
        $this->info('✅ Detail scraping jobs dispatched.');

        return self::SUCCESS;
    }
}
