@extends('admin.layout')

@section('title', 'إدارة المطاعم')
@section('page_title', 'إدارة المطاعم')

@section('content')
    <!-- Header with Actions -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500">إجمالي {{ $restaurants->total() }} مطعم</p>
        </div>
        <a href="{{ route('admin.restaurants.create') }}"
            class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-5 rounded-xl transition-colors text-sm">
            + إضافة مطعم
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form action="{{ route('admin.restaurants.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="ابحث بالاسم أو الرابط أو الهاتف..."
                    class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
            </div>
            <select name="city_id" class="px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none bg-white">
                <option value="">جميع المدن</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name_ar ?? $city->name }}</option>
                @endforeach
            </select>
            <select name="category_id" class="px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none bg-white">
                <option value="">جميع التصنيفات</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name_ar ?? $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-800 text-white font-bold py-2 px-5 rounded-xl text-sm hover:bg-gray-700">بحث</button>
            <a href="{{ route('admin.restaurants.index') }}" class="text-gray-500 hover:text-primary-600 text-sm py-2">إعادة تعيين</a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-right bg-gray-50 text-xs text-gray-500 uppercase">
                        <th class="px-4 py-3 font-semibold">#</th>
                        <th class="px-4 py-3 font-semibold">المطعم</th>
                        <th class="px-4 py-3 font-semibold">الهاتف</th>
                        <th class="px-4 py-3 font-semibold">التصنيفات</th>
                        <th class="px-4 py-3 font-semibold">صور المنيو</th>
                        <th class="px-4 py-3 font-semibold">المشاهدات</th>
                        <th class="px-4 py-3 font-semibold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($restaurants as $restaurant)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-400">{{ $restaurant->id }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($restaurant->logo_url)
                                        <img src="{{ $restaurant->logo_url }}" alt="" class="w-10 h-10 rounded-lg object-contain bg-gray-50 border border-gray-100">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center">
                                            <span class="text-sm font-bold text-primary-600">{{ mb_substr($restaurant->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-gray-800">{{ $restaurant->name }}</div>
                                        @if($restaurant->name_ar)
                                            <div class="text-xs text-gray-400">{{ $restaurant->name_ar }}</div>
                                        @endif
                                        <div class="text-xs text-gray-400">{{ $restaurant->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $restaurant->hotline ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($restaurant->categories->take(3) as $cat)
                                        <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $cat->name }}</span>
                                    @endforeach
                                    @if($restaurant->categories->count() > 3)
                                        <span class="text-xs text-gray-400">+{{ $restaurant->categories->count() - 3 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $restaurant->menu_images_count }}</span>
                            </td>
                            <td class="px-4 py-3 font-bold text-gray-700">{{ number_format($restaurant->total_views) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($restaurant->slug)
                                        <a href="{{ route('restaurant.show', $restaurant->slug) }}" target="_blank"
                                            class="text-gray-400 hover:text-blue-600" title="عرض">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.restaurants.edit', $restaurant) }}"
                                        class="text-gray-400 hover:text-accent-600" title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.restaurants.destroy', $restaurant) }}" method="POST"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا المطعم؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600" title="حذف">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">لا توجد مطاعم</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $restaurants->links('pagination.tailwind') }}
    </div>
@endsection
