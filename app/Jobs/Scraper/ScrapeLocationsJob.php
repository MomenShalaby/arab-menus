<?php

declare(strict_types=1);

namespace App\Jobs\Scraper;

use App\Services\Scraper\LocationScraper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to scrape all cities and zones.
 * This should be run first before any restaurant scraping.
 */
class ScrapeLocationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;
    public int $backoff = 30;

    public function handle(LocationScraper $scraper): void
    {
        Log::info('Starting location scraping job...');

        $locations = $scraper->scrapeAll();

        Log::info('Location scraping completed.', [
            'cities_count' => count($locations),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Location scraping job failed: ' . $exception->getMessage());
    }
}
