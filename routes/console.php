<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes / Scheduler
|--------------------------------------------------------------------------
|
| Schedule automatic re-scraping to keep data fresh.
| Run: php artisan schedule:work
|
*/

// Re-scrape restaurant details weekly to update menus
Schedule::command('scrape:details --force')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scraper-schedule.log'));

// Re-scrape listings daily at 4 AM to discover new restaurants
Schedule::command('scrape:restaurants')
    ->daily()
    ->at('04:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scraper-schedule.log'));
