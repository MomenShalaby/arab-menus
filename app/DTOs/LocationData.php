<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Data Transfer Object for scraped city/zone data.
 */
final readonly class LocationData
{
    /**
     * @param array<int, array{name: string, slug: string}> $zones
     */
    public function __construct(
        public string $cityName,
        public string $citySlug,
        public ?string $cityNameAr = null,
        public array $zones = [],
    ) {}
}
