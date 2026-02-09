<?php

declare(strict_types=1);

namespace App\Jobs\Scraper;

use App\Services\Scraper\RestaurantScraper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to scrape a single restaurant's detail page and menu images.
 * Implements ShouldBeUnique to avoid duplicate scraping of the same restaurant.
 */
class ScrapeRestaurantDetailJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 180;
    public int $backoff = 30;
    public int $uniqueFor = 3600; // 1 hour

    public function __construct(
        private readonly string $slug,
    ) {}

    /**
     * Unique ID to prevent duplicate jobs for the same restaurant.
     */
    public function uniqueId(): string
    {
        return 'scrape-restaurant-' . $this->slug;
    }

    public function handle(RestaurantScraper $scraper): void
    {
        Log::info("Scraping restaurant detail: {$this->slug}");

        $data = $scraper->scrapeRestaurantDetail($this->slug);

        if ($data !== null) {
            Log::info("Restaurant detail scraped: {$this->slug}", [
                'menu_images' => count($data->menuImageUrls),
            ]);
        } else {
            Log::warning("No data extracted for restaurant: {$this->slug}");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Restaurant detail scraping failed for {$this->slug}: {$exception->getMessage()}");
    }
}
