<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCityController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRestaurantController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminZoneController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SitemapController;
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

// ناكل ايه - Random restaurant picker
Route::get('/nakl-eih', [HomeController::class, 'naklEih'])->name('nakl-eih');

// Picker wheel
Route::get('/picker-wheel', [HomeController::class, 'pickerWheel'])->name('picker-wheel');

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

// Language toggle
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// XML Sitemap (SEO)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// ===== Admin Routes =====
Route::prefix('admin')->group(function () {
    // Auth routes (no middleware)
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Protected admin routes
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Restaurants CRUD (bind by ID since slug can be null)
        Route::get('/restaurants', [AdminRestaurantController::class, 'index'])->name('admin.restaurants.index');
        Route::get('/restaurants/create', [AdminRestaurantController::class, 'create'])->name('admin.restaurants.create');
        Route::post('/restaurants', [AdminRestaurantController::class, 'store'])->name('admin.restaurants.store');
        Route::get('/restaurants/{restaurant:id}/edit', [AdminRestaurantController::class, 'edit'])->name('admin.restaurants.edit');
        Route::put('/restaurants/{restaurant:id}', [AdminRestaurantController::class, 'update'])->name('admin.restaurants.update');
        Route::delete('/restaurants/{restaurant:id}', [AdminRestaurantController::class, 'destroy'])->name('admin.restaurants.destroy');

        // Cities CRUD
        Route::get('/cities', [AdminCityController::class, 'index'])->name('admin.cities.index');
        Route::get('/cities/create', [AdminCityController::class, 'create'])->name('admin.cities.create');
        Route::post('/cities', [AdminCityController::class, 'store'])->name('admin.cities.store');
        Route::get('/cities/{city:id}/edit', [AdminCityController::class, 'edit'])->name('admin.cities.edit');
        Route::put('/cities/{city:id}', [AdminCityController::class, 'update'])->name('admin.cities.update');
        Route::delete('/cities/{city:id}', [AdminCityController::class, 'destroy'])->name('admin.cities.destroy');

        // Categories CRUD
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
        Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('admin.categories.create');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/categories/{category:id}/edit', [AdminCategoryController::class, 'edit'])->name('admin.categories.edit');
        Route::put('/categories/{category:id}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{category:id}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

        // Zones CRUD
        Route::get('/zones', [AdminZoneController::class, 'index'])->name('admin.zones.index');
        Route::get('/zones/create', [AdminZoneController::class, 'create'])->name('admin.zones.create');
        Route::post('/zones', [AdminZoneController::class, 'store'])->name('admin.zones.store');
        Route::get('/zones/{zone:id}/edit', [AdminZoneController::class, 'edit'])->name('admin.zones.edit');
        Route::put('/zones/{zone:id}', [AdminZoneController::class, 'update'])->name('admin.zones.update');
        Route::delete('/zones/{zone:id}', [AdminZoneController::class, 'destroy'])->name('admin.zones.destroy');

        // Settings
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
        Route::put('/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');

        // Restaurant Menu Images Management
        Route::post('/restaurants/{restaurant:id}/menu-images', [AdminRestaurantController::class, 'addMenuImage'])->name('admin.restaurants.menu-images.store');
        Route::delete('/menu-images/{menuImage}', [AdminRestaurantController::class, 'deleteMenuImage'])->name('admin.restaurants.menu-images.destroy');
        Route::post('/restaurants/{restaurant:id}/menu-images/reorder', [AdminRestaurantController::class, 'reorderMenuImages'])->name('admin.restaurants.menu-images.reorder');

        // Restaurant Branches Management
        Route::post('/restaurants/{restaurant:id}/branches', [AdminRestaurantController::class, 'addBranch'])->name('admin.restaurants.branches.store');
        Route::put('/branches/{branch}', [AdminRestaurantController::class, 'updateBranch'])->name('admin.restaurants.branches.update');
        Route::delete('/branches/{branch}', [AdminRestaurantController::class, 'deleteBranch'])->name('admin.restaurants.branches.destroy');
    });
});
