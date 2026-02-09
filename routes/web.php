<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage with search form
Route::get('/', [HomeController::class, 'index'])->name('home');

// Search / Filter restaurants
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Restaurant detail page
Route::get('/restaurant/{slug}', [RestaurantController::class, 'show'])->name('restaurant.show');

// API: Get zones for a city (AJAX)
Route::get('/api/zones/{cityId}', [RestaurantController::class, 'zones'])
    ->where('cityId', '[0-9]+')
    ->name('api.zones');

// API: Live search restaurants (autocomplete)
Route::get('/api/search', [RestaurantController::class, 'liveSearch'])
    ->name('api.search');

// API: Get random restaurant (ناكل ايه)
Route::get('/api/random-restaurant', [RestaurantController::class, 'randomRestaurant'])
    ->name('api.random-restaurant');
