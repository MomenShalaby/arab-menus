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
