<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Source
    |--------------------------------------------------------------------------
    |
    | Base URL of the site being scraped. Pulled into HttpClientService so the
    | target is no longer hardcoded and can be overridden per-environment.
    |
    */
    'base_url' => env('SCRAPER_BASE_URL', 'https://www.menuegypt.com'),

    /*
    |--------------------------------------------------------------------------
    | HTTP client
    |--------------------------------------------------------------------------
    |
    | Tuning for the shared HttpClientService. throttle_ms is the minimum delay
    | between outbound requests (politeness); concurrency caps parallel image
    | downloads when using the HTTP pool.
    |
    */
    'http' => [
        'max_retries'    => (int) env('SCRAPER_MAX_RETRIES', 3),
        'retry_delay_ms' => (int) env('SCRAPER_RETRY_DELAY_MS', 1000),
        'timeout'        => (int) env('SCRAPER_TIMEOUT', 30),
        'throttle_ms'    => (int) env('SCRAPER_THROTTLE_MS', 500),
        'concurrency'    => (int) env('SCRAPER_CONCURRENCY', 5),
        'user_agent'     => env(
            'SCRAPER_USER_AGENT',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scraping defaults
    |--------------------------------------------------------------------------
    */
    'max_pages' => (int) env('SCRAPER_MAX_PAGES', 10),

    /*
    |--------------------------------------------------------------------------
    | Scheduled scraping
    |--------------------------------------------------------------------------
    |
    | Drives routes/console.php. `cities` is the comma-separated list of city
    | slugs the scheduler keeps fresh; leave null to cover every city in the DB.
    | Times are 24h "HH:MM" in the app timezone.
    |
    */
    'schedule' => [
        'enabled' => (bool) env('SCRAPER_SCHEDULE_ENABLED', true),

        // null => all cities in the database; or e.g. 'tanta,cairo,giza'
        'cities' => env('SCRAPER_SCHEDULE_CITIES', 'tanta'),

        // Daily: discover new restaurant listings + scrape newly found details.
        'listings_at' => env('SCRAPER_LISTINGS_AT', '04:00'),
        'details_at'  => env('SCRAPER_DETAILS_AT', '05:00'),

        // Weekly full refresh of every restaurant's details (menus, branches…).
        'full_refresh_day' => env('SCRAPER_FULL_REFRESH_DAY', 'sunday'),
        'full_refresh_at'  => env('SCRAPER_FULL_REFRESH_AT', '03:00'),
    ],
];
