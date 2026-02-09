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

            <!-- Menu Images Management -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">صور المنيو ({{ $restaurant->menuImages->count() }})</h3>
                </div>

                <!-- Add New Image -->
                <form action="{{ route('admin.restaurants.menu-images.store', $restaurant) }}" method="POST" class="flex gap-2 mb-4">
                    @csrf
                    <input type="text" name="image_url" placeholder="رابط الصورة (URL)" required dir="ltr"
                        class="flex-1 px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-xl text-sm transition-colors whitespace-nowrap">
                        + إضافة صورة
                    </button>
                </form>

                @if($restaurant->menuImages->isNotEmpty())
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                        @foreach($restaurant->menuImages->sortBy('sort_order') as $img)
                            <div class="relative group aspect-square bg-gray-50 rounded-lg overflow-hidden border border-gray-100">
                                <img src="{{ $img->image_url }}" alt="" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <form action="{{ route('admin.restaurants.menu-images.destroy', [$restaurant, $img]) }}" method="POST"
                                        onsubmit="return confirm('حذف هذه الصورة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white rounded-full p-2 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                                <span class="absolute bottom-1 right-1 bg-black/60 text-white text-xs px-1.5 py-0.5 rounded">{{ $img->sort_order ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center py-4">لا توجد صور منيو بعد</p>
                @endif
            </div>

            <!-- Branches Management -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">الفروع ({{ $restaurant->branches->count() }})</h3>
                </div>

                <!-- Add New Branch -->
                <form action="{{ route('admin.restaurants.branches.store', $restaurant) }}" method="POST" class="flex flex-wrap gap-2 mb-4">
                    @csrf
                    <input type="text" name="name" placeholder="اسم الفرع *" required
                        class="flex-1 min-w-[150px] px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
                    <input type="text" name="address" placeholder="العنوان"
                        class="flex-1 min-w-[150px] px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
                    <input type="text" name="phone" placeholder="الهاتف" dir="ltr"
                        class="w-36 px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-xl text-sm transition-colors whitespace-nowrap">
                        + إضافة فرع
                    </button>
                </form>

                @if($restaurant->branches->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($restaurant->branches as $branch)
                            <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3 text-sm" id="branch-{{ $branch->id }}">
                                <!-- Display Mode -->
                                <div class="flex-1 flex items-center gap-3 branch-display">
                                    <span class="font-semibold text-gray-800">{{ $branch->name }}</span>
                                    @if($branch->address)
                                        <span class="text-gray-400">- {{ $branch->address }}</span>
                                    @endif
                                    @if($branch->phone)
                                        <span class="text-gray-400 dir-ltr">📞 {{ $branch->phone }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1 branch-display">
                                    <button type="button" onclick="editBranch({{ $branch->id }})" class="text-blue-600 hover:text-blue-800 p-1" title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.restaurants.branches.destroy', [$restaurant, $branch]) }}" method="POST"
                                        onsubmit="return confirm('حذف هذا الفرع؟')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="حذف">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>

                                <!-- Edit Mode (hidden by default) -->
                                <form action="{{ route('admin.restaurants.branches.update', [$restaurant, $branch]) }}" method="POST"
                                    class="hidden flex-1 flex flex-wrap items-center gap-2 branch-edit">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $branch->name }}" required
                                        class="flex-1 min-w-[120px] px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:border-primary-500 outline-none">
                                    <input type="text" name="address" value="{{ $branch->address }}" placeholder="العنوان"
                                        class="flex-1 min-w-[120px] px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:border-primary-500 outline-none">
                                    <input type="text" name="phone" value="{{ $branch->phone }}" placeholder="الهاتف" dir="ltr"
                                        class="w-32 px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:border-primary-500 outline-none">
                                    <button type="submit" class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-blue-700">حفظ</button>
                                    <button type="button" onclick="cancelEditBranch({{ $branch->id }})" class="text-gray-500 text-xs px-3 py-1.5 rounded-lg hover:bg-gray-200">إلغاء</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center py-4">لا توجد فروع بعد</p>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-8 rounded-xl transition-colors text-sm">
                    حفظ التعديلات
                </button>
                <a href="{{ route('admin.restaurants.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">إلغاء</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function editBranch(id) {
        const row = document.getElementById('branch-' + id);
        row.querySelectorAll('.branch-display').forEach(el => el.classList.add('hidden'));
        row.querySelector('.branch-edit').classList.remove('hidden');
    }

    function cancelEditBranch(id) {
        const row = document.getElementById('branch-' + id);
        row.querySelectorAll('.branch-display').forEach(el => el.classList.remove('hidden'));
        row.querySelector('.branch-edit').classList.add('hidden');
    }
</script>
@endpush
