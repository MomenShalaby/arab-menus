@extends('layouts.app')

@section('title', ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه - ابحث عن منيوهات المطاعم بسهولة' : 'Nakol Eh - Find Restaurant Menus Easily')
@section('meta_description', ($currentLocale ?? 'ar') === 'ar' ? 'ابحث عن منيوهات المطاعم في مصر بسهولة. دليل شامل للمنيوهات.' : 'Find restaurant menus in Egypt easily. Comprehensive menu guide.')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-bl from-primary-700 via-primary-600 to-accent-600 text-white overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1" fill="white"/>
                    </pattern>
                </defs>
                <rect fill="url(#dots)" width="100" height="100"/>
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-16 sm:py-24 relative z-10">
            <div class="text-center mb-10">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold mb-4 leading-tight">
                    {{ ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه' : 'Nakol Eh' }}
                </h1>
                <p class="text-lg sm:text-xl text-white/80 max-w-2xl mx-auto">
                    {{ ($currentLocale ?? 'ar') === 'ar' ? 'ابحث عن منيو أي مطعم بسهولة تامة' : 'Find any restaurant menu easily' }}
                </p>
            </div>

            <!-- Search Form -->
            <div class="max-w-3xl mx-auto">
                <form action="{{ route('search') }}" method="GET" class="bg-white rounded-2xl shadow-2xl p-6 space-y-4" id="search-form">
                    <!-- Quick Search with Autocomplete -->
                    <div class="relative">
                        <input type="text" name="search" id="live-search-input"
                            placeholder="{{ ($currentLocale ?? 'ar') === 'ar' ? 'ابحث عن مطعم بالاسم...' : 'Search for a restaurant by name...' }}"
                            value="{{ old('search') }}"
                            autocomplete="off"
                            class="w-full px-5 py-3 rounded-xl border border-gray-200 text-gray-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-lg">
                        <!-- Autocomplete Dropdown -->
                        <div id="search-results" class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 hidden max-h-80 overflow-y-auto"></div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex-1 border-t border-gray-200"></div>
                        <span class="text-gray-400 text-sm font-medium">{{ ($currentLocale ?? 'ar') === 'ar' ? 'أو تصفية حسب' : 'or filter by' }}</span>
                        <div class="flex-1 border-t border-gray-200"></div>
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- City -->
                        <select name="city_id" id="city-select"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none bg-white">
                            <option value="">{{ ($currentLocale ?? 'ar') === 'ar' ? 'اختر المدينة' : 'Select City' }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ (isset($defaultCityId) && $city->id == $defaultCityId) ? 'selected' : '' }}>
                                    {{ $city->name_ar ?? $city->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Zone (dynamic, populated via JS) -->
                        <select name="zone_id" id="zone-select"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none bg-white"
                            disabled>
                            <option value="">{{ ($currentLocale ?? 'ar') === 'ar' ? 'اختر المنطقة' : 'Select Zone' }}</option>
                        </select>

                        <!-- Category -->
                        <select name="category_id"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none bg-white">
                            <option value="">{{ ($currentLocale ?? 'ar') === 'ar' ? 'جميع الأقسام' : 'All Categories' }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name_ar ?? $category->name }} ({{ $category->restaurants_count }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-xl transition-colors duration-200 text-lg shadow-lg hover:shadow-xl">
                        🔍 {{ ($currentLocale ?? 'ar') === 'ar' ? 'بحث' : 'Search' }}
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    @if(!empty($stats))
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                <div>
                    <span class="block text-2xl font-bold text-primary-600">{{ number_format($stats['total_restaurants']) }}</span>
                    <span class="text-sm text-gray-500">{{ ($currentLocale ?? 'ar') === 'ar' ? 'مطعم' : 'Restaurants' }}</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-primary-600">{{ number_format($stats['total_cities']) }}</span>
                    <span class="text-sm text-gray-500">{{ ($currentLocale ?? 'ar') === 'ar' ? 'مدينة' : 'Cities' }}</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-primary-600">{{ number_format($stats['total_zones']) }}</span>
                    <span class="text-sm text-gray-500">{{ ($currentLocale ?? 'ar') === 'ar' ? 'منطقة' : 'Zones' }}</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-primary-600">{{ number_format($stats['total_categories']) }}</span>
                    <span class="text-sm text-gray-500">{{ ($currentLocale ?? 'ar') === 'ar' ? 'تصنيف' : 'Categories' }}</span>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ناكل ايه and Picker Wheel sections are available on their own dedicated pages --}}

    <!-- Featured Restaurants -->
    @if($featured->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ ($currentLocale ?? 'ar') === 'ar' ? 'المطاعم المميزة' : 'Featured Restaurants' }}</h2>
                <p class="text-gray-500 mt-1">{{ ($currentLocale ?? 'ar') === 'ar' ? 'أكثر المطاعم مشاهدة' : 'Most viewed restaurants' }}</p>
            </div>
            <a href="{{ route('search') }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm flex items-center gap-1">
                {{ ($currentLocale ?? 'ar') === 'ar' ? 'عرض الكل' : 'View All' }}
                <svg class="w-4 h-4 {{ ($isRtl ?? true) ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($featured as $index => $restaurant)
                @if($restaurant->slug)

                {{-- Ad between restaurants --}}
                @if(($adsEnabled ?? false) && !empty($adsBetweenCode ?? '') && $index > 0 && $index % 6 === 0)
                    <div class="col-span-full ads-container py-2">{!! $adsBetweenCode !!}</div>
                @endif

                <a href="{{ route('restaurant.show', $restaurant->slug) }}"
                    class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary-200">
                    <!-- Logo -->
                    <div class="aspect-square bg-gray-50 flex items-center justify-center p-4 overflow-hidden">
                        @if($restaurant->logo_url)
                            <img data-src="{{ $restaurant->logo_url }}"
                                alt="{{ ($currentLocale ?? 'ar') === 'ar' ? ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name }}"
                                class="lazy-image w-full h-full object-contain group-hover:scale-110 transition-transform duration-300"
                                loading="lazy">
                        @else
                            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-primary-600">{{ mb_substr(($currentLocale ?? 'ar') === 'ar' ? ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="p-3 text-center border-t border-gray-50">
                        <h3 class="font-bold text-gray-800 text-sm truncate">{{ ($currentLocale ?? 'ar') === 'ar' ? ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name }}</h3>
                        @if($restaurant->categories->isNotEmpty())
                            <p class="text-xs text-gray-400 mt-1 truncate">
                                {{ $restaurant->categories->pluck(($currentLocale ?? 'ar') === 'ar' ? 'name_ar' : 'name')->filter()->implode(' • ') ?: $restaurant->categories->pluck('name')->implode(' • ') }}
                            </p>
                        @endif
                        <div class="flex items-center justify-center gap-1 mt-1">
                            <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span class="text-xs text-gray-400">{{ number_format($restaurant->total_views) }}</span>
                        </div>
                    </div>
                </a>
                @endif
            @endforeach
        </div>
    </section>
    @endif

    <!-- Empty state when no data -->
    @if($featured->isEmpty() && $stats['total_restaurants'] === 0)
    <section class="max-w-7xl mx-auto px-4 py-20 text-center">
        <div class="max-w-md mx-auto">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-700 mb-3">{{ ($currentLocale ?? 'ar') === 'ar' ? 'لا توجد بيانات حالياً' : 'No data available' }}</h2>
            <p class="text-gray-500 mb-6">{{ ($currentLocale ?? 'ar') === 'ar' ? 'يرجى تشغيل أمر جلب البيانات أولاً:' : 'Please run the data fetch command first:' }}</p>
            <code class="bg-gray-800 text-green-400 px-4 py-2 rounded-lg text-sm inline-block direction-ltr">
                php artisan scrape:all --sync --city=tanta
            </code>
        </div>
    </section>
    @endif
@endsection

@push('styles')
<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script>
    // ====== LIVE SEARCH ======
    const searchInput = document.getElementById('live-search-input');
    const searchResults = document.getElementById('search-results');
    let searchTimer = null;

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            const q = searchInput.value.trim();
            if (q.length < 2) {
                searchResults.classList.add('hidden');
                searchResults.innerHTML = '';
                return;
            }
            searchTimer = setTimeout(async () => {
                try {
                    const res = await fetch(`/api/search?q=${encodeURIComponent(q)}`);
                    const data = await res.json();
                    if (data.length === 0) {
                        searchResults.innerHTML = '<div class="p-4 text-center text-gray-400 text-sm">لا توجد نتائج</div>';
                        searchResults.classList.remove('hidden');
                        return;
                    }
                    searchResults.innerHTML = data.map(r => `
                        <a href="${r.url}" class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-b-0">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                ${r.logo_url ? `<img src="${r.logo_url}" alt="${r.name}" class="w-full h-full object-contain">` : `<span class="text-sm font-bold text-primary-600">${r.name.charAt(0)}</span>`}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-800 text-sm truncate">${r.name}</div>
                                <div class="text-xs text-gray-400 truncate">${r.categories.map(c => c.name_ar || c.name).join(' • ')}</div>
                            </div>
                            ${r.hotline ? `<span class="text-xs text-green-600 font-medium hidden sm:block">📞 ${r.hotline}</span>` : ''}
                        </a>
                    `).join('');
                    searchResults.classList.remove('hidden');
                } catch (e) {
                    console.error('Search failed:', e);
                }
            }, 300);
        });

        // Close dropdown on click outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        // Close on Escape
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') searchResults.classList.add('hidden');
        });
    }

    // ====== DYNAMIC ZONES ======
    const citySelect = document.getElementById('city-select');
    const zoneSelect = document.getElementById('zone-select');

    if (citySelect && zoneSelect) {
        // Auto-load zones for default city
        if (citySelect.value) {
            loadZones(citySelect.value);
        }

        citySelect.addEventListener('change', () => {
            loadZones(citySelect.value);
        });
    }

    async function loadZones(cityId) {
        zoneSelect.innerHTML = '<option value="">{{ ($currentLocale ?? "ar") === "ar" ? "اختر المنطقة" : "Select Zone" }}</option>';
        if (!cityId) {
            zoneSelect.disabled = true;
            return;
        }
        try {
            const response = await fetch(`/api/zones/${cityId}`);
            const zones = await response.json();
            zones.forEach(zone => {
                const option = document.createElement('option');
                option.value = zone.id;
                option.textContent = zone.name_ar || zone.name;
                zoneSelect.appendChild(option);
            });
            zoneSelect.disabled = false;
        } catch (error) {
            console.error('Failed to load zones:', error);
        }
    }
</script>
@endpush
