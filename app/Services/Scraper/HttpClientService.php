<?php

declare(strict_types=1);

namespace App\Services\Scraper;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Base HTTP client for all scrapers.
 * Handles throttling, retries, and common HTTP operations.
 */
class HttpClientService
{
    private const BASE_URL = 'https://www.menuegypt.com';
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY_MS = 1000;
    private const TIMEOUT_SECONDS = 30;
    private const THROTTLE_DELAY_MS = 500;

    private float $lastRequestTime = 0;

    /**
     * Fetch a page and return a DomCrawler instance.
     */
    public function fetchPage(string $url): ?Crawler
    {
        $html = $this->fetchHtml($url);

        if ($html === null) {
            return null;
        }

        return new Crawler($html, $url);
    }

    /**
     * Fetch raw HTML content from a URL.
     */
    public function fetchHtml(string $url): ?string
    {
        $this->throttle();

        $fullUrl = $this->resolveUrl($url);

        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $response = Http::timeout(self::TIMEOUT_SECONDS)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        'Accept-Language' => 'ar,en;q=0.5',
                        'Accept-Encoding' => 'gzip, deflate',
                        'Connection' => 'keep-alive',
                    ])
                    ->withOptions([
                        'allow_redirects' => [
                            'max' => 3,
                            'strict' => false,
                            'referer' => true,
                            'protocols' => ['http', 'https'],
                        ],
                    ])
                    ->get($fullUrl);

                if ($response->successful()) {
                    $this->lastRequestTime = microtime(true);
                    return $response->body();
                }

                Log::warning("HTTP {$response->status()} for {$fullUrl} (attempt {$attempt})");

            } catch (\Exception $e) {
                Log::warning("Request failed for {$fullUrl} (attempt {$attempt}): {$e->getMessage()}");
            }

            if ($attempt < self::MAX_RETRIES) {
                usleep(self::RETRY_DELAY_MS * 1000 * $attempt);
            }
        }

        Log::error("All {$attempt} attempts failed for: {$fullUrl}");
        return null;
    }

    /**
     * Download a file (image) and return its contents.
     */
    public function downloadFile(string $url): ?string
    {
        $this->throttle();

        $fullUrl = $this->resolveUrl($url);

        try {
            $response = Http::timeout(self::TIMEOUT_SECONDS)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
                    'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
                    'Referer' => self::BASE_URL,
                ])
                ->get($fullUrl);

            if ($response->successful()) {
                $this->lastRequestTime = microtime(true);
                return $response->body();
            }
        } catch (\Exception $e) {
            Log::warning("Failed to download file: {$fullUrl} - {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Resolve relative URLs to full URLs.
     */
    public function resolveUrl(string $url): string
    {
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return rtrim(self::BASE_URL, '/') . '/' . ltrim($url, '/');
    }

    /**
     * Throttle requests to avoid overwhelming the target server.
     */
    private function throttle(): void
    {
        if ($this->lastRequestTime > 0) {
            $elapsed = (microtime(true) - $this->lastRequestTime) * 1000;
            $remaining = self::THROTTLE_DELAY_MS - $elapsed;

            if ($remaining > 0) {
                usleep((int) ($remaining * 1000));
            }
        }
    }

    public function getBaseUrl(): string
    {
        return self::BASE_URL;
    }
}
