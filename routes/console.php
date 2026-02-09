<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes / Scheduler
|--------------------------------------------------------------------------
|
| Schedule automatic re-scraping to keep data fresh.
|
| CRON SETUP (one single line in crontab -e):
| * * * * * cd /home/momen/projects/laravel/menuhat-alarab && php artisan schedule:run >> /dev/null 2>&1
|
| This single cron line handles ALL the scheduled tasks below.
| Laravel checks every minute which tasks are due and runs them.
|
*/

// 1. Re-scrape listings daily at 4 AM to discover NEW restaurants
//    Runs quickly — just fetches listing pages to find new restaurant slugs
Schedule::command('scrape:restaurants --city=tanta --sync')
    ->daily()
    ->at('04:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scraper-schedule.log'));

// 2. Scrape details for NEW (un-scraped) restaurants daily at 5 AM
//    Only scrapes restaurants that haven't been scraped yet (no --force)
//    This picks up new restaurants discovered by step 1
Schedule::command('scrape:details --sync')
    ->daily()
    ->at('05:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scraper-schedule.log'));

// 3. FULL re-scrape of ALL restaurant details weekly on Sundays at 3 AM
//    Updates menus, branches, Arabic names, hotlines, images for ALL restaurants
//    Uses --force to re-scrape even already-scraped restaurants
Schedule::command('scrape:details --force --sync')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scraper-schedule.log'));
