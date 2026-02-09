<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Restaurant\RestaurantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RestaurantController extends Controller
{
    public function __construct(
        private readonly RestaurantService $restaurantService,
    ) {}

    /**
     * Display a single restaurant's page with its menu images.
     */
    public function show(string $slug): View
    {
        $restaurant = $this->restaurantService->getRestaurant($slug);

        if (! $restaurant) {
            abort(404);
        }

        // Increment view count
        $restaurant->increment('total_views');

        $similar = $this->restaurantService->getSimilarRestaurants($restaurant);

        return view('restaurants.show', [
            'restaurant' => $restaurant,
            'similar' => $similar,
        ]);
    }

    /**
     * API: Return zones for a given city (for dynamic dropdowns).
     */
    public function zones(int $cityId): JsonResponse
    {
        $zones = $this->restaurantService->getZonesForCity($cityId);

        return response()->json($zones);
    }

    /**
     * API: Live search restaurants by name (autocomplete).
     */
    public function liveSearch(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = $this->restaurantService->liveSearch($query);

        return response()->json($results);
    }

    /**
     * API: Get a random restaurant from selected categories.
     */
    public function randomRestaurant(Request $request): JsonResponse
    {
        $categoryIds = $request->get('category_ids', []);
        $cityId = $request->get('city_id') ? (int) $request->get('city_id') : null;

        if (is_string($categoryIds)) {
            $categoryIds = array_filter(explode(',', $categoryIds));
        }
        $categoryIds = array_map('intval', $categoryIds);

        $restaurant = $this->restaurantService->getRandomRestaurant($categoryIds, $cityId);

        if (! $restaurant) {
            return response()->json(['error' => 'لم يتم العثور على مطعم'], 404);
        }

        return response()->json([
            'id' => $restaurant->id,
            'name' => $restaurant->name,
            'name_ar' => $restaurant->name_ar,
            'slug' => $restaurant->slug,
            'logo_url' => $restaurant->logo_url,
            'hotline' => $restaurant->hotline,
            'categories' => $restaurant->categories->map(fn($c) => [
                'name' => $c->name,
                'name_ar' => $c->name_ar,
            ]),
            'menu_count' => $restaurant->menuImages->count(),
            'url' => route('restaurant.show', $restaurant->slug),
        ]);
    }
}
