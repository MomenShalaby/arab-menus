<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCityController extends Controller
{
    public function index(Request $request): View
    {
        $query = City::withCount(['zones', 'restaurants']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('name_ar', 'LIKE', "%{$search}%");
            });
        }

        $cities = $query->orderBy('name')->paginate(25)->withQueryString();

        return view('admin.cities.index', compact('cities'));
    }

    public function create(): View
    {
        return view('admin.cities.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cities,slug',
            'source_url' => 'nullable|url|max:500',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        City::create($data);

        return redirect()->route('admin.cities.index')
            ->with('success', 'تم إضافة المدينة بنجاح');
    }

    public function edit(City $city): View
    {
        $city->loadCount(['zones', 'restaurants']);
        return view('admin.cities.edit', compact('city'));
    }

    public function update(Request $request, City $city): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cities,slug,' . $city->id,
            'source_url' => 'nullable|url|max:500',
        ]);

        $city->update($data);

        return redirect()->route('admin.cities.index')
            ->with('success', 'تم تحديث المدينة بنجاح');
    }

    public function destroy(City $city): RedirectResponse
    {
        $city->zones()->delete();
        $city->delete();

        return redirect()->route('admin.cities.index')
            ->with('success', 'تم حذف المدينة بنجاح');
    }
}
