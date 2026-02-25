@extends('layouts.app')

@php
    // --- Resolve active city / category objects ---
    $activeCity     = !empty($filters['city_id'])     ? $cities->firstWhere('id', $filters['city_id'])         : null;
    $activeCategory = !empty($filters['category_id']) ? $categories->firstWhere('id', $filters['category_id']) : null;
    $isAr           = ($currentLocale ?? 'ar') === 'ar';

    // --- Build SEO-friendly page title ---
    if ($activeCity) {
        $pageTitle = $isAr
            ? 'مطاعم ' . ($activeCity->name_ar ?? $activeCity->name) . ' | منيوهات مطاعم ' . ($activeCity->name_ar ?? $activeCity->name)
            : 'Restaurants in ' . $activeCity->name . ' | ' . $activeCity->name . ' Restaurant Menus';
    } elseif ($activeCategory) {
        $pageTitle = $isAr
            ? ($activeCategory->name_ar ?? $activeCategory->name) . ' في مصر | منيوهات مطاعم ' . ($activeCategory->name_ar ?? $activeCategory->name)
            : $activeCategory->name . ' Restaurants in Egypt | Menus';
    } elseif (!empty($filters['search'])) {
        $pageTitle = $isAr
            ? 'نتائج البحث عن: ' . $filters['search']
            : 'Search results for: ' . $filters['search'];
    } else {
        $pageTitle = $isAr ? 'تصفح منيوهات المطاعم' : 'Browse Restaurant Menus';
    }

    // --- Build SEO description ---
    if ($activeCity) {
        $metaDesc = $isAr
            ? 'اكتشف منيوهات مطاعم ' . ($activeCity->name_ar ?? $activeCity->name) . ' - أسعار الأكل والمشروبات والوجبات. أكثر من ' . $restaurants->total() . ' مطعم في ' . ($activeCity->name_ar ?? $activeCity->name) . '.'
            : 'Discover restaurant menus in ' . $activeCity->name . ' – food prices, meals, and delivery. Over ' . $restaurants->total() . ' restaurants available.';
    } elseif ($activeCategory) {
        $metaDesc = $isAr
            ? 'أفضل مطاعم ' . ($activeCategory->name_ar ?? $activeCategory->name) . ' في مصر - منيوهات وأسعار ' . $restaurants->total() . ' مطعم.'
            : 'Best ' . $activeCategory->name . ' restaurants in Egypt – menus and prices for ' . $restaurants->total() . ' restaurants.';
    } else {
        $metaDesc = $isAr
            ? 'تصفح منيوهات المطاعم في مصر. ابحث حسب المدينة والمنطقة والتصنيف. أكثر من ' . $restaurants->total() . ' مطعم متاح.'
            : 'Browse restaurant menus in Egypt. Search by city, zone, and category. Over ' . $restaurants->total() . ' restaurants available.';
    }

    // --- Canonical: strip page / sort / direction / zone_id so every paginated
    //     or sorted variant points back to the clean city/category filter URL. ---
    $canonicalParams = [];
    if (!empty($filters['city_id']))     $canonicalParams['city_id']     = $filters['city_id'];
    if (!empty($filters['category_id'])) $canonicalParams['category_id'] = $filters['category_id'];
    if (($currentLocale ?? 'ar') === 'en') $canonicalParams['lang']      = 'en';
    $searchCanonical = url('/search') . (count($canonicalParams) ? '?' . http_build_query($canonicalParams) : '');

    // --- Robots: noindex text-search results and pure sort/page variants ---
    $shouldNoIndex = !empty($filters['search'])
        || (!$activeCity && !$activeCategory && (request()->has('sort') || request()->has('page')));
@endphp

@section('title', $pageTitle . ' - ناكل ايه | Nakol Eh')
@section('meta_description', $metaDesc)
@section('meta_keywords', $activeCity
    ? (($activeCity->name_ar ?? $activeCity->name) . ', مطاعم ' . ($activeCity->name_ar ?? $activeCity->name) . ', منيوهات ' . ($activeCity->name_ar ?? $activeCity->name) . ', restaurants ' . $activeCity->name . ', ' . $activeCity->name . ' menus')
    : ($activeCategory
        ? (($activeCategory->name_ar ?? $activeCategory->name) . ', مطاعم ' . ($activeCategory->name_ar ?? $activeCategory->name) . ', ' . $activeCategory->name . ' restaurants Egypt')
        : 'تصفح مطاعم, منيوهات مطاعم مصر, browse restaurants Egypt, restaurant menus, قوائم طعام'))
@section('canonical_url', $searchCanonical)
@if($shouldNoIndex)
@section('robots_meta', 'noindex, follow')
@endif

@push('structured_data')
<!-- Structured Data: ItemList for restaurant listing -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "{{ $pageTitle }}",
    "description": "{{ $metaDesc }}",
    "numberOfItems": {{ $restaurants->total() }},
    "itemListElement": [
        @foreach($restaurants->take(10) as $index => $restaurant)
        @if($restaurant->slug)
        {
            "@@type": "ListItem",
            "position": {{ $index + 1 }},
            "url": "{{ route('restaurant.show', $restaurant->slug) }}",
            "name": "{{ ($currentLocale ?? 'ar') === 'ar' ? ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name }}"
        }{{ !$loop->last ? ',' : '' }}
        @endif
        @endforeach
    ]
}
</script>

<!-- Structured Data: BreadcrumbList -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "{{ ($currentLocale ?? 'ar') === 'ar' ? 'الرئيسية' : 'Home' }}",
            "item": "{{ route('home') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "{{ ($currentLocale ?? 'ar') === 'ar' ? 'تصفح المطاعم' : 'Browse Restaurants' }}"
        }
    ]
}
</script>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
        <!-- Filters Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6 sm:mb-8">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4">{{ ($currentLocale ?? 'ar') === 'ar' ? 'تصفية النتائج' : 'Filter Results' }}</h2>
            <form action="{{ route('search') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
                    <!-- Search -->
                    <div class="sm:col-span-2 lg:col-span-2 relative">
                        <label for="index-live-search" class="sr-only">{{ ($currentLocale ?? 'ar') === 'ar' ? 'ابحث عن مطعم' : 'Search for a restaurant' }}</label>
                        <input type="text" name="search" id="index-live-search"
                            placeholder="{{ ($currentLocale ?? 'ar') === 'ar' ? 'ابحث عن مطعم...' : 'Search for a restaurant...' }}"
                            value="{{ $filters['search'] ?? '' }}"
                            autocomplete="off"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
                        <!-- Autocomplete Dropdown -->
                        <div id="index-search-results" class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 hidden max-h-80 overflow-y-auto"></div>
                    </div>

                    <!-- City -->
                    <div>
                        <label for="filter-city" class="sr-only">{{ ($currentLocale ?? 'ar') === 'ar' ? 'المدينة' : 'City' }}</label>
                        <select name="city_id" id="filter-city"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none bg-white text-sm">
                            <option value="">{{ ($currentLocale ?? 'ar') === 'ar' ? 'جميع المدن' : 'All Cities' }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ ($filters['city_id'] ?? '') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name_ar ?? $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Zone -->
                    <div>
                        <label for="filter-zone" class="sr-only">{{ ($currentLocale ?? 'ar') === 'ar' ? 'المنطقة' : 'Zone' }}</label>
                        <select name="zone_id" id="filter-zone"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none bg-white text-sm">
                            <option value="">{{ ($currentLocale ?? 'ar') === 'ar' ? 'جميع المناطق' : 'All Zones' }}</option>
                        </select>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="filter-category" class="sr-only">{{ ($currentLocale ?? 'ar') === 'ar' ? 'القسم' : 'Category' }}</label>
                        <select name="category_id" id="filter-category"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none bg-white text-sm">
                            <option value="">{{ ($currentLocale ?? 'ar') === 'ar' ? 'جميع الأقسام' : 'All Categories' }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name_ar ?? $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-6 sm:px-8 rounded-xl transition-colors duration-200 text-sm shadow-sm flex-shrink-0">
                        🔍 {{ ($currentLocale ?? 'ar') === 'ar' ? 'بحث' : 'Search' }}
                    </button>
                    <a href="{{ route('search') }}" class="text-gray-500 hover:text-primary-600 text-sm">{{ ($currentLocale ?? 'ar') === 'ar' ? 'إعادة تعيين' : 'Reset' }}</a>
                </div>
            </form>
        </div>

        <!-- Results Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6 gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">{{ ($currentLocale ?? 'ar') === 'ar' ? 'المطاعم' : 'Restaurants' }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $restaurants->total() }} {{ ($currentLocale ?? 'ar') === 'ar' ? 'نتيجة' : 'results' }}
                </p>
            </div>

            <!-- Sort -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <label for="sort-select" class="text-sm text-gray-500 whitespace-nowrap">{{ ($currentLocale ?? 'ar') === 'ar' ? 'ترتيب:' : 'Sort:' }}</label>
                <select id="sort-select" onchange="window.location.href=this.value"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 outline-none focus:border-primary-500 min-w-0">
                    <option value="{{ route('search', array_merge($filters, ['sort' => 'name'])) }}" {{ ($filters['sort'] ?? 'name') === 'name' ? 'selected' : '' }}>
                        {{ ($currentLocale ?? 'ar') === 'ar' ? 'الاسم' : 'Name' }}
                    </option>
                    <option value="{{ route('search', array_merge($filters, ['sort' => 'views'])) }}" {{ ($filters['sort'] ?? '') === 'views' ? 'selected' : '' }}>
                        {{ ($currentLocale ?? 'ar') === 'ar' ? 'الأكثر مشاهدة' : 'Most Viewed' }}
                    </option>
                    <option value="{{ route('search', array_merge($filters, ['sort' => 'latest'])) }}" {{ ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' }}>
                        {{ ($currentLocale ?? 'ar') === 'ar' ? 'الأحدث' : 'Latest' }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Restaurant Cards Grid -->
        @if($restaurants->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
                @foreach($restaurants as $restaurant)
                    @if($restaurant->slug)
                    <a href="{{ route('restaurant.show', $restaurant->slug) }}"
                        class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary-200"
                        title="{{ ($currentLocale ?? 'ar') === 'ar' ? 'منيو ' . ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name . ' menu' }}">
                        <!-- Logo -->
                        <div class="aspect-square bg-gray-50 flex items-center justify-center p-3 sm:p-4 overflow-hidden">
                            @if($restaurant->logo_url)
                                <img data-src="{{ $restaurant->logo_url }}"
                                    alt="{{ ($currentLocale ?? 'ar') === 'ar' ? 'منيو ' . ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name . ' menu' }}"
                                    class="lazy-image w-full h-full object-contain group-hover:scale-110 transition-transform duration-300"
                                    loading="lazy"
                                    width="200" height="200">
                            @else
                                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-lg sm:text-xl font-bold text-primary-600">{{ mb_substr(($currentLocale ?? 'ar') === 'ar' ? ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="p-2 sm:p-3 border-t border-gray-50">
                            <h3 class="font-bold text-gray-800 text-xs sm:text-sm truncate text-center">{{ ($currentLocale ?? 'ar') === 'ar' ? ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name }}</h3>
                            @if($restaurant->categories->isNotEmpty())
                                <p class="text-[10px] sm:text-xs text-gray-400 mt-1 truncate text-center">
                                    {{ $restaurant->categories->pluck(($currentLocale ?? 'ar') === 'ar' ? 'name_ar' : 'name')->map(fn($v, $k) => $v ?: $restaurant->categories[$k]->name)->take(2)->implode(' • ') }}
                                </p>
                            @endif
                            @if($restaurant->menuImages->isNotEmpty())
                                <div class="flex items-center justify-center gap-1 mt-1.5 sm:mt-2">
                                    <svg class="w-3 h-3 text-accent-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                                    </svg>
                                    <span class="text-[10px] sm:text-xs text-gray-400">{{ $restaurant->menuImages->count() }} {{ ($currentLocale ?? 'ar') === 'ar' ? 'صور' : 'images' }}</span>
                                </div>
                            @endif
                        </div>
                    </a>                    @endif                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8 sm:mt-10 flex justify-center overflow-x-auto">
                {{ $restaurants->withQueryString()->links('pagination.tailwind') }}
            </div>
        @else
            <!-- No Results -->
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">{{ ($currentLocale ?? 'ar') === 'ar' ? 'لا توجد نتائج' : 'No results found' }}</h3>
                <p class="text-gray-500">{{ ($currentLocale ?? 'ar') === 'ar' ? 'جرب تغيير معايير البحث أو التصفية' : 'Try changing your search or filter criteria' }}</p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    // ====== LIVE SEARCH ON INDEX PAGE ======
    const indexSearchInput = document.getElementById('index-live-search');
    const indexSearchResults = document.getElementById('index-search-results');
    let indexSearchTimer = null;

    if (indexSearchInput && indexSearchResults) {
        indexSearchInput.addEventListener('input', () => {
            clearTimeout(indexSearchTimer);
            const q = indexSearchInput.value.trim();
            if (q.length < 2) {
                indexSearchResults.classList.add('hidden');
                indexSearchResults.innerHTML = '';
                return;
            }
            indexSearchTimer = setTimeout(async () => {
                try {
                    const res = await fetch(`/api/search?q=${encodeURIComponent(q)}`);
                    const data = await res.json();
                    if (data.length === 0) {
                        indexSearchResults.innerHTML = '<div class="p-4 text-center text-gray-400 text-sm">لا توجد نتائج</div>';
                        indexSearchResults.classList.remove('hidden');
                        return;
                    }
                    indexSearchResults.innerHTML = data.map(r => `
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
                    indexSearchResults.classList.remove('hidden');
                } catch (e) {
                    console.error('Search failed:', e);
                }
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!indexSearchInput.contains(e.target) && !indexSearchResults.contains(e.target)) {
                indexSearchResults.classList.add('hidden');
            }
        });

        indexSearchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') indexSearchResults.classList.add('hidden');
        });
    }

    // Dynamic zone loading
    const filterCity = document.getElementById('filter-city');
    const filterZone = document.getElementById('filter-zone');

    async function loadZones(cityId, selectedZoneId = null) {
        filterZone.innerHTML = '<option value="">{{ ($currentLocale ?? "ar") === "ar" ? "جميع المناطق" : "All Zones" }}</option>';

        if (!cityId) return;

        try {
            const response = await fetch(`/api/zones/${cityId}`);
            const zones = await response.json();

            zones.forEach(zone => {
                const option = document.createElement('option');
                option.value = zone.id;
                option.textContent = zone.name_ar || zone.name;
                if (selectedZoneId && selectedZoneId == zone.id) {
                    option.selected = true;
                }
                filterZone.appendChild(option);
            });
        } catch (error) {
            console.error('Failed to load zones:', error);
        }
    }

    filterCity.addEventListener('change', () => loadZones(filterCity.value));

    // Load zones on page load if city is pre-selected
    @if(!empty($filters['city_id']))
        loadZones('{{ $filters['city_id'] }}', '{{ $filters['zone_id'] ?? '' }}');
    @endif
</script>
@endpush
