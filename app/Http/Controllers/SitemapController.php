<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Restaurant;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate XML Sitemap for SEO.
     */
    public function index(): Response
    {
        $restaurants = Restaurant::whereNotNull('slug')
            ->select('slug', 'updated_at', 'total_views')
            ->orderByDesc('total_views')
            ->get();

        $cities = City::select('id', 'name', 'updated_at')->get();
        $categories = Category::select('id', 'name', 'updated_at')->get();

        $content = view('sitemap.index', compact('restaurants', 'cities', 'categories'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
