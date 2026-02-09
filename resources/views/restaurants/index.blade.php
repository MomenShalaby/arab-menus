@extends('layouts.app')

@section('title', 'تصفح المطاعم - منيوهات العرب')
@section('meta_description', 'تصفح قائمة المطاعم المتاحة وابحث حسب المدينة والمنطقة والتصنيف')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Filters Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">تصفية النتائج</h2>
            <form action="{{ route('search') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <input type="text" name="search"
                            placeholder="ابحث عن مطعم..."
                            value="{{ $filters['search'] ?? '' }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
                    </div>

                    <!-- City -->
                    <select name="city_id" id="filter-city"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none bg-white text-sm">
                        <option value="">جميع المدن</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ ($filters['city_id'] ?? '') == $city->id ? 'selected' : '' }}>
                                {{ $city->name_ar ?? $city->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Zone -->
                    <select name="zone_id" id="filter-zone"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none bg-white text-sm">
                        <option value="">جميع المناطق</option>
                    </select>

                    <!-- Category -->
                    <select name="category_id"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none bg-white text-sm">
                        <option value="">جميع الأقسام</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name_ar ?? $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-8 rounded-xl transition-colors duration-200 text-sm shadow-sm">
                        🔍 بحث
                    </button>
                    <a href="{{ route('search') }}" class="text-gray-500 hover:text-primary-600 text-sm">إعادة تعيين</a>
                </div>
            </form>
        </div>

        <!-- Results Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">المطاعم</h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $restaurants->total() }} نتيجة
                </p>
            </div>

            <!-- Sort -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">ترتيب حسب:</span>
                <select onchange="window.location.href=this.value"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 outline-none focus:border-primary-500">
                    <option value="{{ route('search', array_merge($filters, ['sort' => 'name'])) }}" {{ ($filters['sort'] ?? 'name') === 'name' ? 'selected' : '' }}>
                        الاسم
                    </option>
                    <option value="{{ route('search', array_merge($filters, ['sort' => 'views'])) }}" {{ ($filters['sort'] ?? '') === 'views' ? 'selected' : '' }}>
                        الأكثر مشاهدة
                    </option>
                    <option value="{{ route('search', array_merge($filters, ['sort' => 'latest'])) }}" {{ ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' }}>
                        الأحدث
                    </option>
                </select>
            </div>
        </div>

        <!-- Restaurant Cards Grid -->
        @if($restaurants->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($restaurants as $restaurant)
                    <a href="{{ route('restaurant.show', $restaurant->slug) }}"
                        class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary-200">
                        <!-- Logo -->
                        <div class="aspect-square bg-gray-50 flex items-center justify-center p-4 overflow-hidden">
                            @if($restaurant->logo_url)
                                <img data-src="{{ $restaurant->logo_url }}"
                                    alt="{{ $restaurant->name }}"
                                    class="lazy-image w-full h-full object-contain group-hover:scale-110 transition-transform duration-300"
                                    loading="lazy">
                            @else
                                <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-xl font-bold text-primary-600">{{ mb_substr($restaurant->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="p-3 border-t border-gray-50">
                            <h3 class="font-bold text-gray-800 text-sm truncate text-center">{{ $restaurant->name }}</h3>
                            @if($restaurant->categories->isNotEmpty())
                                <p class="text-xs text-gray-400 mt-1 truncate text-center">
                                    {{ $restaurant->categories->pluck('name')->take(2)->implode(' • ') }}
                                </p>
                            @endif
                            @if($restaurant->menuImages->isNotEmpty())
                                <div class="flex items-center justify-center gap-1 mt-2">
                                    <svg class="w-3 h-3 text-accent-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                                    </svg>
                                    <span class="text-xs text-gray-400">{{ $restaurant->menuImages->count() }} صور</span>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10 flex justify-center">
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
                <h3 class="text-xl font-bold text-gray-700 mb-2">لا توجد نتائج</h3>
                <p class="text-gray-500">جرب تغيير معايير البحث أو التصفية</p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    // Dynamic zone loading
    const filterCity = document.getElementById('filter-city');
    const filterZone = document.getElementById('filter-zone');

    async function loadZones(cityId, selectedZoneId = null) {
        filterZone.innerHTML = '<option value="">جميع المناطق</option>';

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
