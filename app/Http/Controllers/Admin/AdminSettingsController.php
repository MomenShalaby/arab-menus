<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(): View
    {
        $settings = [
            'ads_enabled' => Setting::get('ads_enabled', '0'),
            'ads_header_code' => Setting::get('ads_header_code', ''),
            'ads_sidebar_code' => Setting::get('ads_sidebar_code', ''),
            'ads_footer_code' => Setting::get('ads_footer_code', ''),
            'ads_between_restaurants_code' => Setting::get('ads_between_restaurants_code', ''),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ads_enabled' => 'nullable|in:0,1',
            'ads_header_code' => 'nullable|string|max:5000',
            'ads_sidebar_code' => 'nullable|string|max:5000',
            'ads_footer_code' => 'nullable|string|max:5000',
            'ads_between_restaurants_code' => 'nullable|string|max:5000',
        ]);

        Setting::set('ads_enabled', $data['ads_enabled'] ?? '0');
        Setting::set('ads_header_code', $data['ads_header_code'] ?? '');
        Setting::set('ads_sidebar_code', $data['ads_sidebar_code'] ?? '');
        Setting::set('ads_footer_code', $data['ads_footer_code'] ?? '');
        Setting::set('ads_between_restaurants_code', $data['ads_between_restaurants_code'] ?? '');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم حفظ الإعدادات بنجاح');
    }
}
