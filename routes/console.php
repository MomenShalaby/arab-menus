<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes / Scheduler
|--------------------------------------------------------------------------
|
| Automatic re-scraping that keeps restaurant data fresh. Everything here is
| driven by config/scraper.php (env-overridable) — cities, times and cadence
| are no longer hardcoded.
|
| RUNNING THE SCHEDULER
| ---------------------
| Preferred (no crontab needed) — run the scheduler as a long-lived process,
| e.g. under Supervisor / systemd / a Docker entrypoint:
|
|     php artisan schedule:work
|
| This stays in the foreground and dispatches due tasks every minute, so there
| is no dependency on an OS-level cron entry.
|
| Legacy (shared hosting): a single crontab line still works if you can't run
| a persistent process —
|
|     * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
|
| Either way, a queue worker must drain the scraper queue:
|
|     php artisan queue:work --queue=scraper --tries=3
|
*/

if (config('scraper.schedule.enabled', true)) {

    $schedule = config('scraper.schedule');
    $logPath  = storage_path('logs/scraper-schedule.log');

    // Resolve which cities to keep fresh. Empty/null => every city in the DB.
    $cities = array_filter(array_map(
        'trim',
        explode(',', (string) ($schedule['cities'] ?? '')),
    ));

    // Build the optional "--city=" fragment shared by the scheduled commands.
    $cityArgs = empty($cities)
        ? ['']                                   // one run covering all cities
        : array_map(fn (string $c): string => " --city={$c}", $cities);

    foreach ($cityArgs as $cityArg) {
        // 1. Daily: discover NEW restaurant listings (fast — listing pages only).
        Schedule::command("scrape:restaurants{$cityArg}")
            ->dailyAt($schedule['listings_at'])
            ->withoutOverlapping()
            ->onOneServer()
            ->runInBackground()
            ->appendOutputTo($logPath);
    }

    // 2. Daily: scrape details for restaurants discovered above (un-scraped only).
    //    Dispatched to the queue; the worker handles the heavy lifting.
    Schedule::command('scrape:details')
        ->dailyAt($schedule['details_at'])
        ->withoutOverlapping()
        ->onOneServer()
        ->runInBackground()
        ->appendOutputTo($logPath);

    // 3. Weekly: full re-scrape of EVERY restaurant's details (menus, branches,
    //    Arabic names, hotlines, images) to pick up upstream changes.
    $fullRefreshDay = match (strtolower(trim((string) $schedule['full_refresh_day']))) {
        'monday'    => 1,
        'tuesday'   => 2,
        'wednesday' => 3,
        'thursday'  => 4,
        'friday'    => 5,
        'saturday'  => 6,
        default     => 0, // sunday
    };

    Schedule::command('scrape:details --force')
        ->weeklyOn($fullRefreshDay, $schedule['full_refresh_at'])
        ->withoutOverlapping()
        ->onOneServer()
        ->runInBackground()
        ->appendOutputTo($logPath);
}
