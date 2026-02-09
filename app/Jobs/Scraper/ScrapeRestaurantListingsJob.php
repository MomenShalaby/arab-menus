<?php

declare(strict_types=1);

namespace App\Jobs\Scraper;

use App\Services\Scraper\RestaurantScraper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to scrape restaurant listings for a specific city/zone.
 * Dispatched after locations have been scraped.
 */
class ScrapeRestaurantListingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;
    public int $backoff = 60;

    public function __construct(
        private readonly string $citySlug,
        private readonly string $zoneSlug,
        private readonly int $maxPages = 10,
    ) {}

    public function handle(RestaurantScraper $scraper): void
    {
        Log::info("Scraping restaurant listings for {$this->citySlug}/{$this->zoneSlug}...");

        $restaurants = $scraper->scrapeListings(
            $this->citySlug,
            $this->zoneSlug,
            $this->maxPages,
        );

        Log::info("Restaurant listings scraped for {$this->citySlug}/{$this->zoneSlug}.", [
            'restaurants_count' => count($restaurants),
        ]);

        // Dispatch detail scraping jobs for each restaurant
        foreach ($restaurants as $restaurant) {
            ScrapeRestaurantDetailJob::dispatch($restaurant->slug)
                ->onQueue('scraper')
                ->delay(now()->addSeconds(rand(2, 10)));
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Restaurant listing scraping failed for {$this->citySlug}/{$this->zoneSlug}: {$exception->getMessage()}");
    }
}
