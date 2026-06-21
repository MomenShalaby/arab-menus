# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Laravel 12 / PHP 8.2 app that aggregates Egyptian restaurant menus (data scraped from menuegypt.com) into a bilingual (Arabic-default / English) public directory with a "ناكل ايه" (What should we eat?) random picker, plus an admin CMS for managing the catalog. SQLite is the default database; queues, cache, and sessions all use the `database` driver.

## Commands

```bash
# First-time setup (install deps, copy .env, key:generate, migrate, npm build)
composer setup

# Full dev environment — runs server + queue worker + log tailer (pail) + vite concurrently
composer dev

# Run tests (clears config first, then runs PHPUnit)
composer test
php artisan test --filter=SomeTest        # single test by name
php artisan test tests/Feature/FooTest.php # single file

# Lint / format (Laravel Pint)
./vendor/bin/pint            # fix
./vendor/bin/pint --test     # check only

# Frontend
npm run dev      # vite dev server
npm run build    # production build

# Seed the admin user (admin@arabmenus.com / admin1234)
php artisan db:seed --class=AdminSeeder
```

### Scraper commands

The scraper pulls data from `menuegypt.com`. Jobs go on the `scraper` queue, so a worker must be running for non-`--sync` runs: `php artisan queue:work --queue=scraper`.

```bash
php artisan scrape:all --sync                 # full pipeline (locations → listings → details), inline
php artisan scrape:all --city=tanta           # one city, via queue
php artisan scrape:restaurants --city=tanta   # listings only (discovers new restaurant slugs)
php artisan scrape:details --sync             # detail pages for restaurants with no last_scraped_at
php artisan scrape:details --force --sync     # re-scrape ALL restaurants (menus, branches, images)
```

`routes/console.php` schedules these automatically (daily listings + new details at 4/5 AM, full weekly re-scrape Sundays 3 AM). Production needs the standard `* * * * * php artisan schedule:run` cron line.

## Architecture

### Two surfaces in one app
- **Public site** (`HomeController`, `RestaurantController`, `SitemapController`) — search, filter, restaurant detail pages, the random picker, XML sitemap. Routes are unauthenticated.
- **Admin CMS** (`App\Http\Controllers\Admin\*`) — CRUD for restaurants, cities, zones, categories, menu images, branches, and ads/settings. All under `/admin`, gated by `AdminMiddleware` (checks `Auth::user()->is_admin`). Note admin routes bind models **by `id`** (`{restaurant:id}`), not slug, because slugs can be Arabic/null.

### Data model
`Restaurant` is the hub. It has many `MenuImage` (ordered by `sort_order`) and `Branch`, and belongs-to-many `City` (pivot `city_restaurant`, carries `branch_name`/`address`), `Zone` (`restaurant_zone`), and `Category` (`category_restaurant`). `City` → `Zone` is one-to-many. Most entities carry both `name` and `name_ar`. `Restaurant::getRouteKeyName()` is `slug` for public routes. `last_scraped_at` distinguishes scraped vs. un-scraped restaurants; `total_views` drives sitemap/featured ordering.

### Scraper subsystem (`app/Services/Scraper`, `app/Jobs/Scraper`)
Wired in `ScraperServiceProvider` (registered in `bootstrap/providers.php`). Layering:
- `HttpClientService` (singleton) — shared HTTP client with throttling (500ms between requests), retries, 30s timeout. Base URL `https://www.menuegypt.com` is hardcoded here.
- `LocationScraper` / `RestaurantScraper` — parse HTML via `Symfony\DomCrawler`, persist models, download logo/menu images to `storage/app/public`.
- Jobs (`ScrapeLocationsJob`, `ScrapeRestaurantListingsJob`, `ScrapeRestaurantDetailJob`) wrap the scrapers for queueing; `ScrapeAll/Restaurants/Details` commands dispatch them. `ScrapingLog` records each run's status.
- `app/DTOs` (`LocationData`, `RestaurantData`) carry parsed data between scraper and persistence.

### Localization & SEO (important, non-obvious)
Default locale at runtime is **Arabic**, even though `config/app.php` says `en`. The active locale lives in the **session**, not config:
- `SetLocaleMiddleware` (appended to the `web` group in `bootstrap/app.php`) reads `?lang=`, stores it in the session, and **301-redirects `?lang=ar` to the clean URL** (Arabic is canonical; keeping the param would create duplicate content). `?lang=en` is allowed to persist so Google can index English.
- `AppServiceProvider::boot()` has a `View::composer('*', ...)` that, for every view, sets the locale from the session and injects `currentLocale`, `isRtl`, `canonicalUrl`, `hreflangArUrl`, `hreflangEnUrl`, and the ad-slot codes. **All ad HTML and SEO meta come from here**, so global view data belongs in this composer, not individual controllers.
- There are no `lang/` translation files — bilingual strings come from `name`/`name_ar` columns and inline Blade conditionals on `$isRtl`/`$currentLocale`.
- The sitemap deliberately excludes restaurants without menu images or with non-ASCII slugs (`^[a-zA-Z0-9_-]+$`).

### Business logic
`RestaurantService` (singleton) holds all public-facing query logic — search/filter, featured, statistics, cities/zones/categories — with `Cache::remember` (60-min TTL). Default city is **Tanta** (`getDefaultCityId()`). Controllers stay thin and delegate here. `Setting::get()/set()` provide a cached key/value store (used for the ads toggle and ad-slot HTML), invalidated on write.

## Conventions
- Files use `declare(strict_types=1);` and typed signatures throughout — match this.
- Run `./vendor/bin/pint` before considering work done; it's the formatter of record.
