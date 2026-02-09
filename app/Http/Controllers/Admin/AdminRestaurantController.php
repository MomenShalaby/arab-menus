<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\City;
use App\Models\MenuImage;
use App\Models\Restaurant;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminRestaurantController extends Controller
{
    public function index(Request $request): View
    {
        $query = Restaurant::with(['categories', 'cities'])
            ->withCount('menuImages');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%")
                    ->orWhere('hotline', 'LIKE', "%{$search}%");
            });
        }

        if ($cityId = $request->get('city_id')) {
            $query->whereHas('cities', fn($q) => $q->where('cities.id', $cityId));
        }

        if ($categoryId = $request->get('category_id')) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $categoryId));
        }

        $sort = $request->get('sort', 'name');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort === 'views' ? 'total_views' : ($sort === 'latest' ? 'created_at' : 'name'), $dir);

        $restaurants = $query->paginate(25)->withQueryString();
        $cities = City::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.restaurants.index', compact('restaurants', 'cities', 'categories'));
    }

    public function create(): View
    {
        $cities = City::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();

        return view('admin.restaurants.create', compact('cities', 'categories', 'zones'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:restaurants,slug',
            'description' => 'nullable|string',
            'hotline' => 'nullable|string|max:255',
            'logo_url' => 'nullable|url|max:500',
            'source_url' => 'nullable|url|max:500',
            'categories' => 'nullable|array',
            'cities' => 'nullable|array',
            'zones' => 'nullable|array',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $restaurant = Restaurant::create([
            'name' => $data['name'],
            'name_ar' => $data['name_ar'] ?? null,
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'hotline' => $data['hotline'] ?? null,
            'logo_url' => $data['logo_url'] ?? null,
            'source_url' => $data['source_url'] ?? null,
            'last_scraped_at' => now(),
        ]);

        if (!empty($data['categories'])) {
            $restaurant->categories()->sync($data['categories']);
        }
        if (!empty($data['cities'])) {
            $restaurant->cities()->sync($data['cities']);
        }
        if (!empty($data['zones'])) {
            $restaurant->zones()->sync($data['zones']);
        }

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'تم إضافة المطعم بنجاح');
    }

    public function edit(Restaurant $restaurant): View
    {
        $restaurant->load(['categories', 'cities', 'zones', 'menuImages', 'branches']);
        $cities = City::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();

        return view('admin.restaurants.edit', compact('restaurant', 'cities', 'categories', 'zones'));
    }

    public function update(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:restaurants,slug,' . $restaurant->id,
            'description' => 'nullable|string',
            'hotline' => 'nullable|string|max:255',
            'logo_url' => 'nullable|string|max:500',
            'source_url' => 'nullable|string|max:500',
            'categories' => 'nullable|array',
            'cities' => 'nullable|array',
            'zones' => 'nullable|array',
        ]);

        $restaurant->update([
            'name' => $data['name'],
            'name_ar' => $data['name_ar'] ?? null,
            'slug' => $data['slug'] ?: $restaurant->slug,
            'description' => $data['description'] ?? null,
            'hotline' => $data['hotline'] ?? null,
            'logo_url' => $data['logo_url'] ?? null,
            'source_url' => $data['source_url'] ?? null,
        ]);

        $restaurant->categories()->sync($data['categories'] ?? []);
        $restaurant->cities()->sync($data['cities'] ?? []);
        $restaurant->zones()->sync($data['zones'] ?? []);

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'تم تحديث المطعم بنجاح');
    }

    public function destroy(Restaurant $restaurant): RedirectResponse
    {
        $restaurant->menuImages()->delete();
        $restaurant->branches()->delete();
        $restaurant->categories()->detach();
        $restaurant->cities()->detach();
        $restaurant->zones()->detach();
        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'تم حذف المطعم بنجاح');
    }

    // ===== Menu Images Management =====

    public function addMenuImage(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $data = $request->validate([
            'image_url' => 'required|url|max:1000',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $restaurant->menuImages()->create([
            'original_url' => $data['image_url'],
            'alt_text' => $data['alt_text'] ?? null,
            'sort_order' => $data['sort_order'] ?? ($restaurant->menuImages()->max('sort_order') + 1),
        ]);

        return redirect()->route('admin.restaurants.edit', $restaurant)
            ->with('success', 'تم إضافة صورة المنيو بنجاح');
    }

    public function deleteMenuImage(MenuImage $menuImage): RedirectResponse
    {
        $restaurantId = $menuImage->restaurant_id;
        $menuImage->delete();

        return redirect()->route('admin.restaurants.edit', $restaurantId)
            ->with('success', 'تم حذف صورة المنيو');
    }

    public function reorderMenuImages(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $data = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:menu_images,id',
        ]);

        foreach ($data['order'] as $index => $imageId) {
            MenuImage::where('id', $imageId)
                ->where('restaurant_id', $restaurant->id)
                ->update(['sort_order' => $index]);
        }

        return redirect()->route('admin.restaurants.edit', $restaurant)
            ->with('success', 'تم تحديث ترتيب الصور');
    }

    // ===== Branches Management =====

    public function addBranch(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'address_ar' => 'nullable|string|max:500',
        ]);

        $restaurant->branches()->create($data);

        return redirect()->route('admin.restaurants.edit', $restaurant)
            ->with('success', 'تم إضافة الفرع بنجاح');
    }

    public function updateBranch(Request $request, Branch $branch): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'address_ar' => 'nullable|string|max:500',
        ]);

        $branch->update($data);

        return redirect()->route('admin.restaurants.edit', $branch->restaurant_id)
            ->with('success', 'تم تحديث الفرع بنجاح');
    }

    public function deleteBranch(Branch $branch): RedirectResponse
    {
        $restaurantId = $branch->restaurant_id;
        $branch->delete();

        return redirect()->route('admin.restaurants.edit', $restaurantId)
            ->with('success', 'تم حذف الفرع');
    }
}
