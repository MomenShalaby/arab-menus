<?php

declare(strict_types=1);

namespace App\Services\Restaurant;

use App\Models\Category;
use App\Models\City;
use App\Models\Restaurant;
use App\Models\Zone;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Business logic service for restaurant operations.
 * Handles filtering, searching, and data retrieval with caching.
 */
class RestaurantService
{
    private const CACHE_TTL_MINUTES = 60;
    private const PER_PAGE = 24;

    /**
     * Get all cities with zone counts, cached.
     *
     * @return Collection<int, City>
     */
    public function getCities(): Collection
    {
        return Cache::remember('cities_with_zones', self::CACHE_TTL_MINUTES * 60, function (): Collection {
            return City::withCount('zones')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get zones for a specific city.
     *
     * @return Collection<int, Zone>
     */
    public function getZonesForCity(int $cityId): Collection
    {
        return Cache::remember(
            "zones_city_{$cityId}",
            self::CACHE_TTL_MINUTES * 60,
            fn(): Collection => Zone::where('city_id', $cityId)
                ->orderBy('name')
                ->get(),
        );
    }

    /**
     * Get all categories, cached.
     *
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return Cache::remember('categories_all', self::CACHE_TTL_MINUTES * 60, function (): Collection {
            return Category::withCount('restaurants')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Search and filter restaurants with pagination.
     *
     * @param array<string, mixed> $filters
     */
    public function searchRestaurants(array $filters = [], int $perPage = self::PER_PAGE): LengthAwarePaginator
    {
        $query = Restaurant::with(['categories', 'menuImages'])
            ->whereNotNull('last_scraped_at');

        // Search by name
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search): void {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('name_ar', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        // Filter by city
        if (! empty($filters['city_id'])) {
            $query->whereHas('cities', function (Builder $q) use ($filters): void {
                $q->where('cities.id', $filters['city_id']);
            });
        }

        // Filter by zone
        if (! empty($filters['zone_id'])) {
            $query->whereHas('zones', function (Builder $q) use ($filters): void {
                $q->where('zones.id', $filters['zone_id']);
            });
        }

        // Filter by category
        if (! empty($filters['category_id'])) {
            $query->whereHas('categories', function (Builder $q) use ($filters): void {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        // Sorting
        $sortBy = $filters['sort'] ?? 'name';
        $sortDir = $filters['direction'] ?? 'asc';

        return match ($sortBy) {
            'views' => $query->orderBy('total_views', $sortDir)->paginate($perPage),
            'latest' => $query->orderBy('created_at', 'desc')->paginate($perPage),
            default => $query->orderBy('name', $sortDir)->paginate($perPage),
        };
    }

    /**
     * Get a single restaurant with all relations.
     */
    public function getRestaurant(string $slug): ?Restaurant
    {
        return Cache::remember(
            "restaurant_{$slug}",
            self::CACHE_TTL_MINUTES * 60,
            fn(): ?Restaurant => Restaurant::with(['menuImages', 'categories', 'cities', 'zones'])
                ->where('slug', $slug)
                ->first(),
        );
    }

    /**
     * Get similar restaurants based on shared categories.
     *
     * @return Collection<int, Restaurant>
     */
    public function getSimilarRestaurants(Restaurant $restaurant, int $limit = 6): Collection
    {
        $categoryIds = $restaurant->categories->pluck('id');

        if ($categoryIds->isEmpty()) {
            return Restaurant::where('id', '!=', $restaurant->id)
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        }

        return Restaurant::where('id', '!=', $restaurant->id)
            ->whereHas('categories', function (Builder $q) use ($categoryIds): void {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->with('categories')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Get featured/popular restaurants for the homepage.
     *
     * @return Collection<int, Restaurant>
     */
    public function getFeaturedRestaurants(int $limit = 12): Collection
    {
        return Cache::remember(
            'featured_restaurants',
            self::CACHE_TTL_MINUTES * 60,
            fn(): Collection => Restaurant::with(['categories', 'menuImages'])
                ->whereNotNull('last_scraped_at')
                ->whereHas('menuImages')
                ->orderByDesc('total_views')
                ->limit($limit)
                ->get(),
        );
    }

    /**
     * Get restaurant statistics for dashboard.
     *
     * @return array<string, int>
     */
    public function getStatistics(): array
    {
        return Cache::remember('site_statistics', self::CACHE_TTL_MINUTES * 60, fn(): array => [
            'total_restaurants' => Restaurant::count(),
            'total_cities' => City::count(),
            'total_zones' => Zone::count(),
            'total_categories' => Category::count(),
            'scraped_restaurants' => Restaurant::whereNotNull('last_scraped_at')->count(),
        ]);
    }
}
