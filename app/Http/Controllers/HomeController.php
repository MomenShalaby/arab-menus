<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SearchRestaurantRequest;
use App\Services\Restaurant\RestaurantService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly RestaurantService $restaurantService,
    ) {}

    /**
     * Display the homepage with search form and featured restaurants.
     */
    public function index(): View
    {
        return view('home', [
            'cities' => $this->restaurantService->getCities(),
            'categories' => $this->restaurantService->getCategories(),
            'featured' => $this->restaurantService->getFeaturedRestaurants(),
            'stats' => $this->restaurantService->getStatistics(),
            'defaultCityId' => $this->restaurantService->getDefaultCityId(),
        ]);
    }

    /**
     * Search/filter restaurants and display results.
     */
    public function search(SearchRestaurantRequest $request): View
    {
        $filters = $request->filters();

        return view('restaurants.index', [
            'restaurants' => $this->restaurantService->searchRestaurants($filters),
            'cities' => $this->restaurantService->getCities(),
            'categories' => $this->restaurantService->getCategories(),
            'filters' => $filters,
        ]);
    }

    /**
     * Display ناكل ايه page (random restaurant picker).
     */
    public function naklEih(): View
    {
        return view('nakl-eih', [
            'cities' => $this->restaurantService->getCities(),
            'categories' => $this->restaurantService->getCategories(),
        ]);
    }

    /**
     * Display picker wheel page.
     */
    public function pickerWheel(): View
    {
        return view('picker-wheel');
    }
}
