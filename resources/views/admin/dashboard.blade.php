@extends('admin.layout')

@section('title', 'لوحة التحكم')
@section('page_title', 'لوحة التحكم الرئيسية')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">إجمالي المطاعم</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($stats['total_restaurants']) }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <div class="mt-2 flex items-center gap-1 text-xs text-green-600">
                <span>{{ number_format($stats['scraped_restaurants']) }} تم جلب بياناتها</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">المدن</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($stats['total_cities']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-400">{{ number_format($stats['total_zones']) }} منطقة</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">التصنيفات</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($stats['total_categories']) }}</p>
                </div>
                <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">إجمالي المشاهدات</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($stats['total_views']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_menu_images']) }}</p>
            <p class="text-xs text-gray-500 mt-1">صور المنيو</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_branches']) }}</p>
            <p class="text-xs text-gray-500 mt-1">الفروع</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($noMenuCount) }}</p>
            <p class="text-xs text-gray-500 mt-1">بدون صور منيو</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-red-600">{{ number_format($noSlugCount) }}</p>
            <p class="text-xs text-gray-500 mt-1">بدون رابط</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Viewed Restaurants -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                الأكثر مشاهدة
            </h3>
            <div class="space-y-3">
                @foreach($topRestaurants as $i => $r)
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center text-xs font-bold text-gray-500">{{ $i + 1 }}</span>
                        @if($r->logo_url)
                            <img src="{{ $r->logo_url }}" alt="{{ $r->name }}" class="w-8 h-8 rounded-lg object-contain bg-gray-50">
                        @else
                            <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center">
                                <span class="text-xs font-bold text-primary-600">{{ mb_substr($r->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('admin.restaurants.edit', $r) }}" class="text-sm font-semibold text-gray-800 hover:text-primary-600 truncate block">{{ $r->name }}</a>
                        </div>
                        <span class="text-sm font-bold text-accent-600">{{ number_format($r->total_views) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Restaurants Per City -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                المطاعم حسب المدينة
            </h3>
            <div class="space-y-3">
                @foreach($restaurantsPerCity as $city)
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $city->name_ar ?? $city->name }}</span>
                                <span class="text-sm font-bold text-gray-600">{{ number_format($city->restaurants_count) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                @php $maxCount = $restaurantsPerCity->max('restaurants_count') ?: 1; @endphp
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($city->restaurants_count / $maxCount) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Restaurants Per Category -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                المطاعم حسب التصنيف
            </h3>
            <div class="space-y-3">
                @foreach($restaurantsPerCategory as $cat)
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $cat->name_ar ?? $cat->name }}</span>
                                <span class="text-sm font-bold text-gray-600">{{ number_format($cat->restaurants_count) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                @php $maxCat = $restaurantsPerCategory->max('restaurants_count') ?: 1; @endphp
                                <div class="bg-accent-500 h-2 rounded-full" style="width: {{ ($cat->restaurants_count / $maxCat) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recently Scraped -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                آخر المطاعم المُحدّثة
            </h3>
            <div class="space-y-3">
                @foreach($recentlyScraped as $r)
                    <div class="flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('admin.restaurants.edit', $r) }}" class="text-sm font-semibold text-gray-800 hover:text-primary-600 truncate block">{{ $r->name }}</a>
                            <span class="text-xs text-gray-400">{{ $r->last_scraped_at->diffForHumans() }}</span>
                        </div>
                        <span class="text-xs text-gray-400">{{ number_format($r->total_views) }} مشاهدة</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Scraping Logs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-logs-feed="{{ route('admin.logs.feed') }}">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                سجل عمليات الجلب
                <span class="flex items-center gap-1.5 text-xs font-normal text-gray-400">
                    <span id="logs-live-dot" class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span id="logs-live-label">مباشر</span>
                </span>
            </h3>
            <div class="flex items-center gap-2 text-xs">
                <span class="px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium">جاري: <span id="logs-count-running">0</span></span>
                <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 font-medium">مكتمل (24س): <span id="logs-count-completed">0</span></span>
                <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 font-medium">فشل (24س): <span id="logs-count-failed">0</span></span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right text-xs text-gray-500 border-b border-gray-100">
                        <th class="pb-3 font-semibold">النوع</th>
                        <th class="pb-3 font-semibold">الرابط</th>
                        <th class="pb-3 font-semibold">الحالة</th>
                        <th class="pb-3 font-semibold">العناصر</th>
                        <th class="pb-3 font-semibold">التاريخ</th>
                    </tr>
                </thead>
                <tbody id="logs-tbody" class="divide-y divide-gray-50">
                    @foreach($recentLogs as $log)
                        <tr>
                            <td class="py-2.5">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $log->type === 'restaurant_detail' ? 'bg-blue-100 text-blue-700' : ($log->type === 'restaurant_listing' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $log->type }}
                                </span>
                            </td>
                            <td class="py-2.5 max-w-[200px] truncate text-gray-600" title="{{ $log->url }}">{{ Str::limit($log->url, 40) }}</td>
                            <td class="py-2.5">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $log->status === 'completed' ? 'bg-green-100 text-green-700' : ($log->status === 'failed' ? 'bg-red-100 text-red-700' : ($log->status === 'running' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700')) }}"
                                    @if($log->status === 'failed' && $log->error_message) title="{{ $log->error_message }}" @endif>
                                    {{ $log->status === 'completed' ? 'مكتمل' : ($log->status === 'failed' ? 'فشل' : ($log->status === 'running' ? 'جاري' : 'معلق')) }}
                                </span>
                            </td>
                            <td class="py-2.5 text-gray-600">{{ $log->items_scraped }}</td>
                            <td class="py-2.5 text-gray-400 text-xs">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const container = document.querySelector('[data-logs-feed]');
    if (!container) return;

    const feedUrl = container.dataset.logsFeed;
    const tbody = document.getElementById('logs-tbody');
    const dot = document.getElementById('logs-live-dot');
    const label = document.getElementById('logs-live-label');
    const POLL_MS = 5000;

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => (
        { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
    ));
    const truncate = (s, n) => (s && s.length > n ? s.slice(0, n) + '…' : (s || ''));

    const typeClass = (t) => t === 'restaurant_detail'
        ? 'bg-blue-100 text-blue-700'
        : (t === 'restaurant_listing' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700');

    const statusClass = (s) => ({
        completed: 'bg-green-100 text-green-700',
        failed: 'bg-red-100 text-red-700',
        running: 'bg-yellow-100 text-yellow-700',
    }[s] || 'bg-gray-100 text-gray-700');

    const statusLabel = (s) => ({
        completed: 'مكتمل', failed: 'فشل', running: 'جاري',
    }[s] || 'معلق');

    function renderRows(logs) {
        tbody.innerHTML = logs.map(log => `
            <tr>
                <td class="py-2.5">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium ${typeClass(log.type)}">${esc(log.type)}</span>
                </td>
                <td class="py-2.5 max-w-[200px] truncate text-gray-600" title="${esc(log.url)}">${esc(truncate(log.url, 40))}</td>
                <td class="py-2.5">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium ${statusClass(log.status)}"${log.error ? ` title="${esc(log.error)}"` : ''}>${statusLabel(log.status)}</span>
                </td>
                <td class="py-2.5 text-gray-600">${esc(log.items_scraped)}</td>
                <td class="py-2.5 text-gray-400 text-xs">${esc(log.time)}</td>
            </tr>
        `).join('');
    }

    function setCount(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = new Intl.NumberFormat().format(val);
    }

    let timer = null;

    async function poll() {
        try {
            const res = await fetch(feedUrl, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            renderRows(data.logs || []);
            setCount('logs-count-running', data.counts?.running ?? 0);
            setCount('logs-count-completed', data.counts?.completed_24h ?? 0);
            setCount('logs-count-failed', data.counts?.failed_24h ?? 0);
            dot.className = 'w-2 h-2 rounded-full bg-green-500 animate-pulse';
            label.textContent = 'مباشر';
        } catch (e) {
            dot.className = 'w-2 h-2 rounded-full bg-gray-400';
            label.textContent = 'غير متصل';
        }
    }

    function start() { if (!timer) { poll(); timer = setInterval(poll, POLL_MS); } }
    function stop() { clearInterval(timer); timer = null; }

    // Pause polling while the tab is hidden to avoid needless requests.
    document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());
    start();
})();
</script>
@endpush
