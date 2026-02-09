@extends('admin.layout')
@section('title', 'تعديل ' . $category->name)
@section('page_title', 'تعديل: ' . ($category->name_ar ?? $category->name))

@section('content')
    <div class="max-w-xl">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">الاسم (English) *</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">الاسم (عربي)</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar', $category->name_ar) }}" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">الرابط (Slug)</label>
                    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" class="w-full px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none" dir="ltr">
                </div>
                <div class="text-sm text-gray-500">
                    عدد المطاعم: <strong>{{ $category->restaurants_count }}</strong>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-8 rounded-xl transition-colors text-sm">حفظ التعديلات</button>
                <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">إلغاء</a>
            </div>
        </form>
    </div>
@endsection
