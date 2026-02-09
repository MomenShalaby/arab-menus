<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Restaurant\RestaurantService;
use App\Services\Scraper\HttpClientService;
use App\Services\Scraper\LocationScraper;
use App\Services\Scraper\RestaurantScraper;
use Illuminate\Support\ServiceProvider;

class ScraperServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Singleton: one HTTP client shared across all scrapers
        $this->app->singleton(HttpClientService::class);

        // Singletons for scraper services
        $this->app->singleton(LocationScraper::class, function ($app) {
            return new LocationScraper($app->make(HttpClientService::class));
        });

        $this->app->singleton(RestaurantScraper::class, function ($app) {
            return new RestaurantScraper($app->make(HttpClientService::class));
        });

        // Restaurant service
        $this->app->singleton(RestaurantService::class);
    }

    public function boot(): void
    {
        //
    }
}
