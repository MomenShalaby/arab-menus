<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\City;
use App\Models\MenuImage;
use App\Models\Restaurant;
use App\Models\ScrapingLog;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_restaurants' => Restaurant::count(),
            'scraped_restaurants' => Restaurant::whereNotNull('last_scraped_at')->count(),
            'total_cities' => City::count(),
            'total_zones' => Zone::count(),
            'total_categories' => Category::count(),
            'total_menu_images' => MenuImage::count(),
            'total_branches' => Branch::count(),
            'total_views' => Restaurant::sum('total_views'),
        ];

        // Top 10 most viewed restaurants
        $topRestaurants = Restaurant::orderByDesc('total_views')
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->limit(10)
            ->get(['id', 'name', 'slug', 'total_views', 'logo_url']);

        // Recently scraped
        $recentlyScraped = Restaurant::whereNotNull('last_scraped_at')
            ->orderByDesc('last_scraped_at')
            ->limit(10)
            ->get(['id', 'name', 'slug', 'last_scraped_at', 'total_views']);

        // Restaurants per city
        $restaurantsPerCity = City::withCount('restaurants')
            ->orderByDesc('restaurants_count')
            ->limit(10)
            ->get(['id', 'name', 'name_ar']);

        // Restaurants per category
        $restaurantsPerCategory = Category::withCount('restaurants')
            ->orderByDesc('restaurants_count')
            ->limit(10)
            ->get(['id', 'name', 'name_ar']);

        // Recent scraping logs
        $recentLogs = ScrapingLog::orderByDesc('created_at')
            ->limit(20)
            ->get();

        // Restaurants without menus
        $noMenuCount = Restaurant::whereNotNull('last_scraped_at')
            ->whereDoesntHave('menuImages')
            ->count();

        // Restaurants without slug
        $noSlugCount = Restaurant::whereNull('slug')
            ->orWhere('slug', '')
            ->count();

        // Daily views (last 30 days) - approximate from total_views
        $dailyScrapedCount = ScrapingLog::where('type', 'restaurant_detail')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(items_scraped) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'topRestaurants',
            'recentlyScraped',
            'restaurantsPerCity',
            'restaurantsPerCategory',
            'recentLogs',
            'noMenuCount',
            'noSlugCount',
            'dailyScrapedCount',
        ));
    }
}
