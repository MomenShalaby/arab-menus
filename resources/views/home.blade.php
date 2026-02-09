@extends('layouts.app')

@section('title', 'منيوهات العرب - ابحث عن منيوهات المطاعم بسهولة')
@section('meta_description', 'ابحث عن منيوهات المطاعم في مصر بسهولة. دليل شامل للمنيوهات بدون إعلانات.')

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
                    منيوهات العرب
                </h1>
                <p class="text-lg sm:text-xl text-white/80 max-w-2xl mx-auto">
                    ابحث عن منيو أي مطعم بسهولة تامة وبدون إعلانات مزعجة
                </p>
            </div>

            <!-- Search Form -->
            <div class="max-w-3xl mx-auto">
                <form action="{{ route('search') }}" method="GET" class="bg-white rounded-2xl shadow-2xl p-6 space-y-4">
                    <!-- Quick Search -->
                    <div>
                        <input type="text" name="search"
                            placeholder="ابحث عن مطعم بالاسم..."
                            value="{{ old('search') }}"
                            class="w-full px-5 py-3 rounded-xl border border-gray-200 text-gray-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-lg">
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex-1 border-t border-gray-200"></div>
                        <span class="text-gray-400 text-sm font-medium">أو تصفية حسب</span>
                        <div class="flex-1 border-t border-gray-200"></div>
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- City -->
                        <select name="city_id" id="city-select"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none bg-white">
                            <option value="">اختر المدينة</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>

                        <!-- Zone (dynamic, populated via JS) -->
                        <select name="zone_id" id="zone-select"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none bg-white"
                            disabled>
                            <option value="">اختر المنطقة</option>
                        </select>

                        <!-- Category -->
                        <select name="category_id"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none bg-white">
                            <option value="">جميع الأقسام</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->restaurants_count }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-xl transition-colors duration-200 text-lg shadow-lg hover:shadow-xl">
                        🔍 بحث
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
                    <span class="text-sm text-gray-500">مطعم</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-primary-600">{{ number_format($stats['total_cities']) }}</span>
                    <span class="text-sm text-gray-500">مدينة</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-primary-600">{{ number_format($stats['total_zones']) }}</span>
                    <span class="text-sm text-gray-500">منطقة</span>
                </div>
                <div>
                    <span class="block text-2xl font-bold text-primary-600">{{ number_format($stats['total_categories']) }}</span>
                    <span class="text-sm text-gray-500">تصنيف</span>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Featured Restaurants -->
    @if($featured->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">المطاعم المميزة</h2>
                <p class="text-gray-500 mt-1">أكثر المطاعم مشاهدة</p>
            </div>
            <a href="{{ route('search') }}" class="text-primary-600 hover:text-primary-700 font-medium text-sm flex items-center gap-1">
                عرض الكل
                <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($featured as $restaurant)
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
                            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-primary-600">{{ mb_substr($restaurant->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="p-3 text-center border-t border-gray-50">
                        <h3 class="font-bold text-gray-800 text-sm truncate">{{ $restaurant->name }}</h3>
                        @if($restaurant->categories->isNotEmpty())
                            <p class="text-xs text-gray-400 mt-1 truncate">
                                {{ $restaurant->categories->pluck('name')->implode(' • ') }}
                            </p>
                        @endif
                    </div>
                </a>
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
            <h2 class="text-2xl font-bold text-gray-700 mb-3">لا توجد بيانات حالياً</h2>
            <p class="text-gray-500 mb-6">يرجى تشغيل أمر جلب البيانات أولاً:</p>
            <code class="bg-gray-800 text-green-400 px-4 py-2 rounded-lg text-sm inline-block direction-ltr">
                php artisan scrape:all --sync --city=tanta
            </code>
        </div>
    </section>
    @endif
@endsection

@push('scripts')
<script>
    // Dynamic zone loading based on city selection
    const citySelect = document.getElementById('city-select');
    const zoneSelect = document.getElementById('zone-select');

    if (citySelect && zoneSelect) {
        citySelect.addEventListener('change', async () => {
            const cityId = citySelect.value;
            zoneSelect.innerHTML = '<option value="">اختر المنطقة</option>';

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
                    option.textContent = zone.name;
                    zoneSelect.appendChild(option);
                });

                zoneSelect.disabled = false;
            } catch (error) {
                console.error('Failed to load zones:', error);
            }
        });
    }
</script>
@endpush
