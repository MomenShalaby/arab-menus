<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminZoneController extends Controller
{
    public function index(Request $request): View
    {
        $query = Zone::with('city')->withCount('restaurants');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('name_ar', 'LIKE', "%{$search}%");
            });
        }

        if ($cityId = $request->get('city_id')) {
            $query->where('city_id', $cityId);
        }

        $zones = $query->orderBy('name')->paginate(25)->withQueryString();
        $cities = City::orderBy('name')->get();

        return view('admin.zones.index', compact('zones', 'cities'));
    }

    public function create(): View
    {
        $cities = City::orderBy('name')->get();
        return view('admin.zones.create', compact('cities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'source_url' => 'nullable|url|max:500',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        Zone::create($data);

        return redirect()->route('admin.zones.index')
            ->with('success', 'تم إضافة المنطقة بنجاح');
    }

    public function edit(Zone $zone): View
    {
        $zone->load('city');
        $zone->loadCount('restaurants');
        $cities = City::orderBy('name')->get();
        return view('admin.zones.edit', compact('zone', 'cities'));
    }

    public function update(Request $request, Zone $zone): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'source_url' => 'nullable|url|max:500',
        ]);

        $zone->update($data);

        return redirect()->route('admin.zones.index')
            ->with('success', 'تم تحديث المنطقة بنجاح');
    }

    public function destroy(Zone $zone): RedirectResponse
    {
        $zone->restaurants()->detach();
        $zone->delete();

        return redirect()->route('admin.zones.index')
            ->with('success', 'تم حذف المنطقة بنجاح');
    }
}
