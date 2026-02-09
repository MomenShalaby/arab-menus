<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Restaurant\RestaurantService;
use Illuminate\Http\JsonResponse;
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
}
