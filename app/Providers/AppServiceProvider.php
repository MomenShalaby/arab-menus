<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceRootUrl(config('app.url'));

            if (str_starts_with((string) config('app.url'), 'https://')) {
                URL::forceScheme('https');
            }
        }

        // Share locale and ads settings with all views
        View::composer('*', function ($view) {
            $locale = session('locale', 'ar');
            app()->setLocale($locale);

            $view->with('currentLocale', $locale);
            $view->with('isRtl', $locale === 'ar');

            // --- SEO: Canonical & hreflang URLs ---
            // Canonical = the current full URL (after SetLocaleMiddleware has already
            // redirected ?lang=ar to the clean URL, so this is always correct).
            $view->with('canonicalUrl', request()->fullUrl());

            // Build the two language-alternate URLs for hreflang tags.
            $queryWithoutLang = request()->except('lang');
            $baseUrl          = request()->url();

            $hreflangArUrl = empty($queryWithoutLang)
                ? $baseUrl
                : $baseUrl . '?' . http_build_query($queryWithoutLang);

            $hreflangEnUrl = $baseUrl . '?' . http_build_query(
                array_merge($queryWithoutLang, ['lang' => 'en'])
            );

            $view->with('hreflangArUrl', $hreflangArUrl);
            $view->with('hreflangEnUrl', $hreflangEnUrl);

            // Share ads settings
            try {
                $view->with('adsEnabled', Setting::adsEnabled());
                $view->with('adsHeaderCode', Setting::get('ads_header_code', ''));
                $view->with('adsRestaurantHeaderCode', Setting::get('ads_restaurant_header_code', ''));
                $view->with('adsAfterMenuCode', Setting::get('ads_after_menu_code', ''));
                $view->with('adsSidebarCode', Setting::get('ads_sidebar_code', ''));
                $view->with('adsFooterCode', Setting::get('ads_footer_code', ''));
                $view->with('adsBetweenCode', Setting::get('ads_between_restaurants_code', ''));
            } catch (\Exception $e) {
                // Settings table may not exist yet
                $view->with('adsEnabled', false);
                $view->with('adsHeaderCode', '');
                $view->with('adsRestaurantHeaderCode', '');
                $view->with('adsAfterMenuCode', '');
                $view->with('adsSidebarCode', '');
                $view->with('adsFooterCode', '');
                $view->with('adsBetweenCode', '');
            }
        });
    }
}
