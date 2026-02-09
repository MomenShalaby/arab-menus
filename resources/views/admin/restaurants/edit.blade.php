@extends('admin.layout')

@section('title', 'تعديل ' . $restaurant->name)
@section('page_title', 'تعديل: ' . $restaurant->name)

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('admin.restaurants.update', $restaurant) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">معلومات المطعم</h3>
                    <div class="flex items-center gap-3 text-sm text-gray-500">
                        <span>المشاهدات: <strong class="text-gray-800">{{ number_format($restaurant->total_views) }}</strong></span>
                        @if($restaurant->slug)
                            <a href="{{ route('restaurant.show', $restaurant->slug) }}" target="_blank" class="text-blue-600 hover:underline">عرض الصفحة</a>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">الاسم (English) *</label>
                        <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" required
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">الاسم (عربي)</label>
                        <input type="text" name="name_ar" value="{{ old('name_ar', $restaurant->name_ar) }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">الرابط (Slug)</label>
                        <input type="text" name="slug" value="{{ old('slug', $restaurant->slug) }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none" dir="ltr">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">رقم الهاتف</label>
                        <input type="text" name="hotline" value="{{ old('hotline', $restaurant->hotline) }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none" dir="ltr">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">{{ old('description', $restaurant->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">رابط الشعار (Logo URL)</label>
                        <input type="text" name="logo_url" value="{{ old('logo_url', $restaurant->logo_url) }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none" dir="ltr">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">رابط المصدر</label>
                        <input type="text" name="source_url" value="{{ old('source_url', $restaurant->source_url) }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none" dir="ltr">
                    </div>
                </div>

                @if($restaurant->logo_url)
                    <div class="flex items-center gap-3">
                        <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="w-16 h-16 rounded-xl object-contain bg-gray-50 border border-gray-100">
                        <span class="text-xs text-gray-400">الشعار الحالي</span>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-800 mb-2">التصنيفات والمدن</h3>

                @php
                    $selectedCategories = old('categories', $restaurant->categories->pluck('id')->toArray());
                    $selectedCities = old('cities', $restaurant->cities->pluck('id')->toArray());
                    $selectedZones = old('zones', $restaurant->zones->pluck('id')->toArray());
                @endphp

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">التصنيفات</label>
                    <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto p-2 border border-gray-200 rounded-xl">
                        @foreach($categories as $cat)
                            <label class="inline-flex items-center gap-1 px-3 py-1 rounded-full border border-gray-200 text-sm cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="categories[]" value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCategories) ? 'checked' : '' }}
                                    class="rounded text-primary-600">
                                {{ $cat->name_ar ?? $cat->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">المدن</label>
                    <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto p-2 border border-gray-200 rounded-xl">
                        @foreach($cities as $city)
                            <label class="inline-flex items-center gap-1 px-3 py-1 rounded-full border border-gray-200 text-sm cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="cities[]" value="{{ $city->id }}" {{ in_array($city->id, $selectedCities) ? 'checked' : '' }}
                                    class="rounded text-primary-600">
                                {{ $city->name_ar ?? $city->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">المناطق</label>
                    <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto p-2 border border-gray-200 rounded-xl">
                        @foreach($zones as $zone)
                            <label class="inline-flex items-center gap-1 px-3 py-1 rounded-full border border-gray-200 text-sm cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="zones[]" value="{{ $zone->id }}" {{ in_array($zone->id, $selectedZones) ? 'checked' : '' }}
                                    class="rounded text-primary-600">
                                {{ $zone->name_ar ?? $zone->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Menu Images Info -->
            @if($restaurant->menuImages->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">صور المنيو ({{ $restaurant->menuImages->count() }})</h3>
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                        @foreach($restaurant->menuImages as $img)
                            <div class="aspect-square bg-gray-50 rounded-lg overflow-hidden border border-gray-100">
                                <img src="{{ $img->image_url }}" alt="" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Branches Info -->
            @if($restaurant->branches->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">الفروع ({{ $restaurant->branches->count() }})</h3>
                    <div class="space-y-2">
                        @foreach($restaurant->branches as $branch)
                            <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3 text-sm">
                                <span class="font-semibold text-gray-800">{{ $branch->name }}</span>
                                @if($branch->address)
                                    <span class="text-gray-400">- {{ $branch->address }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-8 rounded-xl transition-colors text-sm">
                    حفظ التعديلات
                </button>
                <a href="{{ route('admin.restaurants.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
