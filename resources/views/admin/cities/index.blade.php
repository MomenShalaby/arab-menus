@extends('admin.layout')

@section('title', 'إدارة المدن')
@section('page_title', 'إدارة المدن')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">إجمالي {{ $cities->total() }} مدينة</p>
        <a href="{{ route('admin.cities.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-5 rounded-xl transition-colors text-sm">+ إضافة مدينة</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form action="{{ route('admin.cities.index') }}" method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث..."
                class="flex-1 px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
            <button type="submit" class="bg-gray-800 text-white font-bold py-2 px-5 rounded-xl text-sm">بحث</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right bg-gray-50 text-xs text-gray-500">
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">الاسم</th>
                    <th class="px-4 py-3">الاسم عربي</th>
                    <th class="px-4 py-3">الرابط</th>
                    <th class="px-4 py-3">المناطق</th>
                    <th class="px-4 py-3">المطاعم</th>
                    <th class="px-4 py-3">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($cities as $city)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">{{ $city->id }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $city->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $city->name_ar ?: '-' }}</td>
                        <td class="px-4 py-3 text-gray-400" dir="ltr">{{ $city->slug }}</td>
                        <td class="px-4 py-3"><span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $city->zones_count }}</span></td>
                        <td class="px-4 py-3"><span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $city->restaurants_count }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.cities.edit', $city) }}" class="text-gray-400 hover:text-accent-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" onsubmit="return confirm('حذف هذه المدينة سيحذف جميع المناطق التابعة لها. متأكد؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">لا توجد مدن</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $cities->links('pagination.tailwind') }}</div>
@endsection
