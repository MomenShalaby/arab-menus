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
                <form action="{{ route('search') }}" method="GET" class="bg-white rounded-2xl shadow-2xl p-6 space-y-4" id="search-form">
                    <!-- Quick Search with Autocomplete -->
                    <div class="relative">
                        <input type="text" name="search" id="live-search-input"
                            placeholder="ابحث عن مطعم بالاسم..."
                            value="{{ old('search') }}"
                            autocomplete="off"
                            class="w-full px-5 py-3 rounded-xl border border-gray-200 text-gray-800 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-lg">
                        <!-- Autocomplete Dropdown -->
                        <div id="search-results" class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 hidden max-h-80 overflow-y-auto"></div>
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
                                <option value="{{ $city->id }}" {{ (isset($defaultCityId) && $city->id == $defaultCityId) ? 'selected' : '' }}>
                                    {{ $city->name_ar ?? $city->name }}
                                </option>
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
                                <option value="{{ $category->id }}">{{ $category->name_ar ?? $category->name }} ({{ $category->restaurants_count }})</option>
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

    <!-- ناكل ايه Section -->
    @if($categories->isNotEmpty())
    <section class="bg-gradient-to-br from-accent-50 to-primary-50 py-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-8">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-800 mb-2">🤔 ناكل إيه؟</h2>
                <p class="text-gray-500 text-lg">محتار تاكل إيه؟ اختار نوع الأكل وسيب الباقي علينا!</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
                <!-- Category Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">اختار نوع/أنواع الأكل اللي تحبها:</label>
                    <div class="flex flex-wrap gap-2" id="nakl-categories">
                        @foreach($categories as $category)
                            <button type="button"
                                data-id="{{ $category->id }}"
                                class="nakl-cat-btn px-4 py-2 rounded-full border-2 border-gray-200 text-gray-600 text-sm font-medium transition-all duration-200 hover:border-accent-400 hover:text-accent-600"
                                onclick="toggleNaklCategory(this)">
                                {{ $category->name_ar ?? $category->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- City Filter (optional) -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">في مدينة (اختياري):</label>
                    <select id="nakl-city"
                        class="w-full sm:w-64 px-4 py-2 rounded-xl border border-gray-200 text-gray-700 focus:border-accent-500 outline-none">
                        <option value="">أي مدينة</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ (isset($defaultCityId) && $city->id == $defaultCityId) ? 'selected' : '' }}>
                                {{ $city->name_ar ?? $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Button -->
                <button type="button" id="nakl-btn"
                    class="w-full bg-accent-500 hover:bg-accent-600 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 text-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-95">
                    🎲 يلا نختار مطعم!
                </button>

                <!-- Result -->
                <div id="nakl-result" class="hidden mt-6 bg-gradient-to-r from-accent-50 to-primary-50 rounded-xl p-6 border border-accent-200 animate-fade-in">
                    <div class="flex items-center gap-4">
                        <div id="nakl-logo" class="w-20 h-20 bg-white rounded-xl shadow-sm flex items-center justify-center flex-shrink-0 overflow-hidden border border-gray-100">
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-accent-600 font-medium mb-1">🎉 هتاكل في:</p>
                            <h3 id="nakl-name" class="text-2xl font-extrabold text-gray-800"></h3>
                            <p id="nakl-cats" class="text-sm text-gray-500 mt-1"></p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a id="nakl-link" href="#"
                            class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-6 rounded-lg transition-colors text-sm">
                            📖 شوف المنيو
                        </a>
                        <button type="button" onclick="pickRandomRestaurant()"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded-lg transition-colors text-sm">
                            🔄 جرب تاني
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Picker Wheel Section -->
    @if($categories->isNotEmpty())
    <section class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-8">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-800 mb-2">🎡 عجلة الاختيار</h2>
                <p class="text-gray-500 text-lg">ضيف المطاعم اللي عاجباك ولف العجلة والحظ يختار!</p>
            </div>

            <div class="bg-gray-50 rounded-2xl shadow-lg p-6 sm:p-8">
                <!-- Add restaurants to wheel -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ضيف مطاعم للعجلة:</label>
                    <div class="flex gap-2">
                        <input type="text" id="wheel-input" placeholder="اكتب اسم مطعم..."
                            class="flex-1 px-4 py-2 rounded-xl border border-gray-200 text-gray-700 focus:border-primary-500 outline-none"
                            autocomplete="off">
                        <button type="button" onclick="addToWheel()"
                            class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-xl transition-colors">
                            ➕ ضيف
                        </button>
                    </div>
                    <!-- Wheel items list -->
                    <div id="wheel-items" class="flex flex-wrap gap-2 mt-3"></div>
                </div>

                <!-- The Wheel -->
                <div class="relative flex flex-col items-center">
                    <div class="relative mb-4">
                        <!-- Arrow pointer -->
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 z-10 text-3xl drop-shadow-lg" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">▼</div>
                        <canvas id="wheel-canvas" width="340" height="340" class="rounded-full shadow-xl border-4 border-gray-200"></canvas>
                    </div>

                    <button type="button" id="spin-btn" onclick="spinWheel()" disabled
                        class="bg-accent-500 hover:bg-accent-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 px-10 rounded-xl transition-all duration-300 text-lg shadow-lg hover:shadow-xl transform hover:scale-105 active:scale-95">
                        🎰 لف العجلة!
                    </button>

                    <!-- Result -->
                    <div id="wheel-result" class="hidden mt-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200 text-center w-full">
                        <p class="text-lg text-green-600 font-medium mb-1">🎉 العجلة اختارت:</p>
                        <h3 id="wheel-winner" class="text-3xl font-extrabold text-gray-800"></h3>
                    </div>
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
                @if($restaurant->slug)
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
            <h2 class="text-2xl font-bold text-gray-700 mb-3">لا توجد بيانات حالياً</h2>
            <p class="text-gray-500 mb-6">يرجى تشغيل أمر جلب البيانات أولاً:</p>
            <code class="bg-gray-800 text-green-400 px-4 py-2 rounded-lg text-sm inline-block direction-ltr">
                php artisan scrape:all --sync --city=tanta
            </code>
        </div>
    </section>
    @endif
@endsection

@push('styles')
<style>
    .nakl-cat-btn.active {
        border-color: #f97316;
        background-color: #fff7ed;
        color: #ea580c;
        font-weight: 700;
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake {
        animation: shake 0.3s ease-in-out;
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
                option.textContent = zone.name_ar || zone.name;
                zoneSelect.appendChild(option);
            });
            zoneSelect.disabled = false;
        } catch (error) {
            console.error('Failed to load zones:', error);
        }
    }

    // ====== ناكل ايه ======
    const selectedNaklCategories = new Set();

    function toggleNaklCategory(btn) {
        const id = btn.dataset.id;
        if (selectedNaklCategories.has(id)) {
            selectedNaklCategories.delete(id);
            btn.classList.remove('active');
        } else {
            selectedNaklCategories.add(id);
            btn.classList.add('active');
        }
    }

    document.getElementById('nakl-btn')?.addEventListener('click', pickRandomRestaurant);

    async function pickRandomRestaurant() {
        const btn = document.getElementById('nakl-btn');
        const result = document.getElementById('nakl-result');
        const cityId = document.getElementById('nakl-city')?.value || '';

        btn.disabled = true;
        btn.textContent = '🎲 جاري الاختيار...';
        result.classList.add('hidden');

        try {
            const catIds = Array.from(selectedNaklCategories).join(',');
            const res = await fetch(`/api/random-restaurant?category_ids=${catIds}&city_id=${cityId}`);

            if (!res.ok) {
                throw new Error('not found');
            }

            const data = await res.json();

            // Show result
            document.getElementById('nakl-name').textContent = data.name;
            document.getElementById('nakl-cats').textContent = data.categories.map(c => c.name_ar || c.name).join(' • ');
            document.getElementById('nakl-link').href = data.url;

            const logoDiv = document.getElementById('nakl-logo');
            if (data.logo_url) {
                logoDiv.innerHTML = `<img src="${data.logo_url}" alt="${data.name}" class="w-full h-full object-contain p-1">`;
            } else {
                logoDiv.innerHTML = `<span class="text-2xl font-bold text-primary-600">${data.name.charAt(0)}</span>`;
            }

            result.classList.remove('hidden');
        } catch (e) {
            alert('مفيش مطاعم بالمواصفات دي، جرب اختيارات تانية!');
        } finally {
            btn.disabled = false;
            btn.textContent = '🎲 يلا نختار مطعم!';
        }
    }

    // ====== PICKER WHEEL ======
    const wheelItems = [];
    const wheelColors = ['#ef4444','#f97316','#eab308','#22c55e','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#f43f5e','#6366f1'];
    let spinning = false;
    let currentAngle = 0;

    function addToWheel() {
        const input = document.getElementById('wheel-input');
        const name = input.value.trim();
        if (!name || wheelItems.includes(name)) return;
        if (wheelItems.length >= 10) {
            alert('الحد الأقصى 10 اختيارات');
            return;
        }
        wheelItems.push(name);
        input.value = '';
        renderWheelItems();
        drawWheel();
        document.getElementById('spin-btn').disabled = wheelItems.length < 2;
    }

    // Enter key support
    document.getElementById('wheel-input')?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addToWheel();
        }
    });

    function removeFromWheel(index) {
        wheelItems.splice(index, 1);
        renderWheelItems();
        drawWheel();
        document.getElementById('spin-btn').disabled = wheelItems.length < 2;
        document.getElementById('wheel-result').classList.add('hidden');
    }

    function renderWheelItems() {
        const container = document.getElementById('wheel-items');
        container.innerHTML = wheelItems.map((item, i) => `
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium text-white" style="background-color: ${wheelColors[i % wheelColors.length]}">
                ${item}
                <button onclick="removeFromWheel(${i})" class="mr-1 hover:opacity-70">✕</button>
            </span>
        `).join('');
    }

    function drawWheel() {
        const canvas = document.getElementById('wheel-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const w = canvas.width, h = canvas.height;
        const cx = w / 2, cy = h / 2, r = Math.min(cx, cy) - 8;

        ctx.clearRect(0, 0, w, h);

        if (wheelItems.length === 0) {
            ctx.fillStyle = '#f3f4f6';
            ctx.beginPath();
            ctx.arc(cx, cy, r, 0, Math.PI * 2);
            ctx.fill();
            ctx.fillStyle = '#9ca3af';
            ctx.font = 'bold 16px Tajawal';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('ضيف مطاعم للعجلة', cx, cy);
            return;
        }

        const n = wheelItems.length;
        const arc = (Math.PI * 2) / n;

        for (let i = 0; i < n; i++) {
            const startAngle = currentAngle + i * arc;
            const endAngle = startAngle + arc;

            // Draw slice
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, r, startAngle, endAngle);
            ctx.closePath();
            ctx.fillStyle = wheelColors[i % wheelColors.length];
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 2;
            ctx.stroke();

            // Draw text
            ctx.save();
            ctx.translate(cx, cy);
            ctx.rotate(startAngle + arc / 2);
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#fff';
            ctx.font = `bold ${Math.min(16, 120 / n)}px Tajawal`;
            const textR = r * 0.6;
            // Truncate long names
            let text = wheelItems[i];
            if (text.length > 12) text = text.substring(0, 10) + '..';
            ctx.fillText(text, textR, 0);
            ctx.restore();
        }

        // Center circle
        ctx.beginPath();
        ctx.arc(cx, cy, 18, 0, Math.PI * 2);
        ctx.fillStyle = '#fff';
        ctx.fill();
        ctx.strokeStyle = '#d1d5db';
        ctx.lineWidth = 2;
        ctx.stroke();
    }

    function spinWheel() {
        if (spinning || wheelItems.length < 2) return;
        spinning = true;
        document.getElementById('spin-btn').disabled = true;
        document.getElementById('wheel-result').classList.add('hidden');

        const n = wheelItems.length;
        const arc = (Math.PI * 2) / n;
        const totalRotation = Math.PI * 2 * (5 + Math.random() * 5); // 5-10 full spins
        const startAngle = currentAngle;
        const targetAngle = startAngle + totalRotation;
        const duration = 4000;
        const startTime = performance.now();

        function animate(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // Ease out cubic
            const eased = 1 - Math.pow(1 - progress, 3);
            currentAngle = startAngle + totalRotation * eased;
            drawWheel();

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                spinning = false;
                document.getElementById('spin-btn').disabled = false;

                // Determine winner: the slice at the top (angle = -PI/2 = 3PI/2)
                const normalized = ((currentAngle % (Math.PI * 2)) + Math.PI * 2) % (Math.PI * 2);
                // The pointer is at top = -PI/2 (= 3PI/2 or 270°)
                // We need to find which slice is at angle -(PI/2) relative to current rotation
                const pointerAngle = (Math.PI * 2 - normalized + Math.PI * 1.5) % (Math.PI * 2);
                const winnerIndex = Math.floor(pointerAngle / arc) % n;

                document.getElementById('wheel-winner').textContent = wheelItems[winnerIndex];
                document.getElementById('wheel-result').classList.remove('hidden');
            }
        }

        requestAnimationFrame(animate);
    }

    // Initial draw
    document.addEventListener('DOMContentLoaded', drawWheel);
</script>
@endpush
