@extends('admin.layout')

@section('title', 'إضافة مطعم')
@section('page_title', 'إضافة مطعم جديد')

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('admin.restaurants.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-800 mb-2">معلومات المطعم</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">الاسم (English) *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">الاسم (عربي)</label>
                        <input type="text" name="name_ar" value="{{ old('name_ar') }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">الرابط (Slug)</label>
                        <input type="text" name="slug" value="{{ old('slug') }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none" dir="ltr">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">رقم الهاتف</label>
                        <input type="text" name="hotline" value="{{ old('hotline') }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none" dir="ltr">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">رابط الشعار (Logo URL)</label>
                        <input type="url" name="logo_url" value="{{ old('logo_url') }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none" dir="ltr">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">رابط المصدر</label>
                        <input type="url" name="source_url" value="{{ old('source_url') }}"
                            class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none" dir="ltr">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-800 mb-2">التصنيفات والمدن</h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">التصنيفات</label>
                    <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto p-2 border border-gray-200 rounded-xl">
                        @foreach($categories as $cat)
                            <label class="inline-flex items-center gap-1 px-3 py-1 rounded-full border border-gray-200 text-sm cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="categories[]" value="{{ $cat->id }}" {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }}
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
                                <input type="checkbox" name="cities[]" value="{{ $city->id }}" {{ in_array($city->id, old('cities', [])) ? 'checked' : '' }}
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
                                <input type="checkbox" name="zones[]" value="{{ $zone->id }}" {{ in_array($zone->id, old('zones', [])) ? 'checked' : '' }}
                                    class="rounded text-primary-600">
                                {{ $zone->name_ar ?? $zone->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-8 rounded-xl transition-colors text-sm">
                    حفظ المطعم
                </button>
                <a href="{{ route('admin.restaurants.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
