<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * - Reads the ?lang= query parameter and stores it in the session.
     * - Redirects ?lang=ar URLs to a clean URL (Arabic is the default locale,
     *   so keeping ?lang=ar creates duplicate content that hurts SEO).
     * - Allows ?lang=en to stay in the URL so Google can index the English version.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('lang')) {
            $lang = $request->get('lang');

            if (in_array($lang, ['ar', 'en'])) {
                session(['locale' => $lang]);
                app()->setLocale($lang);
            }

            // Arabic is the default — redirect ?lang=ar to the clean URL
            // to avoid duplicate content in Google Search Console.
            if ($lang === 'ar') {
                $query = $request->except('lang');

                $cleanUrl = empty($query)
                    ? $request->url()
                    : $request->url() . '?' . http_build_query($query);

                return redirect($cleanUrl, 301);
            }
        }

        return $next($request);
    }
}
