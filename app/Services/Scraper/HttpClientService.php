<?php

declare(strict_types=1);

namespace App\Services\Scraper;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Base HTTP client for all scrapers.
 * Handles throttling, retries, and common HTTP operations.
 *
 * All tuning lives in config/scraper.php (env-overridable) rather than
 * hardcoded constants, so the target and politeness can change per environment.
 */
class HttpClientService
{
    private readonly string $baseUrl;
    private readonly int $maxRetries;
    private readonly int $retryDelayMs;
    private readonly int $timeout;
    private readonly int $throttleMs;
    private readonly int $concurrency;
    private readonly string $userAgent;

    private float $lastRequestTime = 0;

    public function __construct()
    {
        $this->baseUrl      = rtrim((string) config('scraper.base_url'), '/');
        $this->maxRetries   = (int) config('scraper.http.max_retries', 3);
        $this->retryDelayMs = (int) config('scraper.http.retry_delay_ms', 1000);
        $this->timeout      = (int) config('scraper.http.timeout', 30);
        $this->throttleMs   = (int) config('scraper.http.throttle_ms', 500);
        $this->concurrency  = max(1, (int) config('scraper.http.concurrency', 5));
        $this->userAgent    = (string) config('scraper.http.user_agent');
    }

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

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'User-Agent' => $this->userAgent,
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

            if ($attempt < $this->maxRetries) {
                usleep($this->retryDelayMs * 1000 * $attempt);
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
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->imageHeaders())
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
     * Download many files concurrently using an HTTP pool.
     *
     * Far faster than looping downloadFile() for the dozens of menu images a
     * single restaurant can have. Requests run in batches of `concurrency`,
     * with a throttle pause between batches to stay polite.
     *
     * @param  array<string, string>  $urls  Keyed map of identifier => URL.
     * @return array<string, string|null>    Same keys => file contents (or null on failure).
     */
    public function downloadFiles(array $urls): array
    {
        $results = [];

        foreach (array_chunk($urls, $this->concurrency, true) as $batch) {
            $this->throttle();

            $responses = Http::pool(function ($pool) use ($batch) {
                foreach ($batch as $key => $url) {
                    $pool->as((string) $key)
                        ->timeout($this->timeout)
                        ->withHeaders($this->imageHeaders())
                        ->get($this->resolveUrl($url));
                }
            });

            foreach ($batch as $key => $url) {
                $response = $responses[(string) $key] ?? null;

                try {
                    $results[$key] = ($response instanceof \Illuminate\Http\Client\Response && $response->successful())
                        ? $response->body()
                        : null;
                } catch (\Throwable $e) {
                    Log::warning("Pooled download failed for {$url}: {$e->getMessage()}");
                    $results[$key] = null;
                }
            }

            $this->lastRequestTime = microtime(true);
        }

        return $results;
    }

    /**
     * Headers used when fetching image binaries.
     *
     * @return array<string, string>
     */
    private function imageHeaders(): array
    {
        return [
            'User-Agent' => $this->userAgent,
            'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
            'Referer' => $this->baseUrl,
        ];
    }

    /**
     * Resolve relative URLs to full URLs.
     */
    public function resolveUrl(string $url): string
    {
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return $this->baseUrl . '/' . ltrim($url, '/');
    }

    /**
     * Throttle requests to avoid overwhelming the target server.
     */
    private function throttle(): void
    {
        if ($this->lastRequestTime > 0) {
            $elapsed = (microtime(true) - $this->lastRequestTime) * 1000;
            $remaining = $this->throttleMs - $elapsed;

            if ($remaining > 0) {
                usleep((int) ($remaining * 1000));
            }
        }
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
