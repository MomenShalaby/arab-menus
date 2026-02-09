@extends('admin.layout')

@section('title', 'الإعدادات')
@section('page_title', 'إعدادات الموقع')

@section('content')
    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6 max-w-3xl">
        @csrf
        @method('PUT')

        <!-- Ads Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    إعدادات الإعلانات
                </h3>
            </div>

            <!-- Ads Enable/Disable -->
            <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-200 bg-gray-50">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="ads_enabled" value="0">
                    <input type="checkbox" name="ads_enabled" value="1"
                        {{ $settings['ads_enabled'] == '1' ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600 rtl:peer-checked:after:-translate-x-full"></div>
                </label>
                <div>
                    <span class="text-sm font-bold text-gray-800">تفعيل الإعلانات على الموقع</span>
                    <p class="text-xs text-gray-500">عند التفعيل ستظهر الإعلانات في الأماكن المحددة أدناه</p>
                </div>
            </div>

            <!-- Header Ads -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">كود إعلان الـ Header (أعلى الصفحة)</label>
                <textarea name="ads_header_code" rows="4" dir="ltr"
                    class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none font-mono"
                    placeholder="أدخل كود الإعلان (HTML/JS) هنا...">{{ $settings['ads_header_code'] }}</textarea>
                <p class="text-xs text-gray-400 mt-1">يظهر أسفل شريط التنقل في جميع الصفحات</p>
            </div>

            <!-- Between Restaurants -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">كود إعلان بين المطاعم</label>
                <textarea name="ads_between_restaurants_code" rows="4" dir="ltr"
                    class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none font-mono"
                    placeholder="أدخل كود الإعلان (HTML/JS) هنا...">{{ $settings['ads_between_restaurants_code'] }}</textarea>
                <p class="text-xs text-gray-400 mt-1">يظهر بين كروت المطاعم في صفحات نتائج البحث والصفحة الرئيسية</p>
            </div>

            <!-- Sidebar Ads -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">كود إعلان الشريط الجانبي</label>
                <textarea name="ads_sidebar_code" rows="4" dir="ltr"
                    class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none font-mono"
                    placeholder="أدخل كود الإعلان (HTML/JS) هنا...">{{ $settings['ads_sidebar_code'] }}</textarea>
                <p class="text-xs text-gray-400 mt-1">يظهر في صفحة تفاصيل المطعم بجانب المحتوى</p>
            </div>

            <!-- Footer Ads -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">كود إعلان الـ Footer (أسفل الصفحة)</label>
                <textarea name="ads_footer_code" rows="4" dir="ltr"
                    class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none font-mono"
                    placeholder="أدخل كود الإعلان (HTML/JS) هنا...">{{ $settings['ads_footer_code'] }}</textarea>
                <p class="text-xs text-gray-400 mt-1">يظهر قبل الـ Footer في جميع الصفحات</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-8 rounded-xl transition-colors text-sm">
                حفظ الإعدادات
            </button>
        </div>
    </form>
@endsection
