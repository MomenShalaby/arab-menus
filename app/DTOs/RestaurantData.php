<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Data Transfer Object for scraped restaurant data.
 */
final readonly class RestaurantData
{
    /**
     * @param string[] $categories
     * @param string[] $menuImageUrls
     */
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $nameAr,
        public ?string $logoUrl,
        public ?string $hotline,
        public ?string $sourceUrl,
        public array $categories = [],
        public array $menuImageUrls = [],
        public ?string $description = null,
    ) {}

    /**
     * Create DTO from scraped raw data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            slug: $data['slug'] ?? '',
            nameAr: $data['name_ar'] ?? null,
            logoUrl: $data['logo_url'] ?? null,
            hotline: $data['hotline'] ?? null,
            sourceUrl: $data['source_url'] ?? null,
            categories: $data['categories'] ?? [],
            menuImageUrls: $data['menu_image_urls'] ?? [],
            description: $data['description'] ?? null,
        );
    }
}
