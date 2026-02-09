@extends('admin.layout')
@section('title', 'إدارة المناطق')
@section('page_title', 'المناطق')

@section('actions')
    <a href="{{ route('admin.zones.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-5 rounded-xl transition-colors text-sm">+ إضافة منطقة</a>
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-3 items-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم..." class="px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none w-56">
                <select name="city_id" class="px-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
                    <option value="">كل المدن</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name_ar ?? $city->name }}</option>
                    @endforeach
                </select>
                <button class="bg-gray-800 text-white px-5 py-2 rounded-xl text-sm font-bold">بحث</button>
                @if(request('search') || request('city_id'))
                    <a href="{{ route('admin.zones.index') }}" class="text-gray-500 hover:text-red-500 py-2 text-sm">مسح</a>
                @endif
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="py-3 px-4 text-right font-semibold">#</th>
                        <th class="py-3 px-4 text-right font-semibold">الاسم</th>
                        <th class="py-3 px-4 text-right font-semibold">الاسم (عربي)</th>
                        <th class="py-3 px-4 text-right font-semibold">المدينة</th>
                        <th class="py-3 px-4 text-right font-semibold">المطاعم</th>
                        <th class="py-3 px-4 text-right font-semibold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($zones as $zone)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 text-gray-400">{{ $zone->id }}</td>
                            <td class="py-3 px-4 font-medium">{{ $zone->name }}</td>
                            <td class="py-3 px-4">{{ $zone->name_ar ?? '-' }}</td>
                            <td class="py-3 px-4">
                                @if($zone->city)
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-bold">{{ $zone->city->name_ar ?? $zone->city->name }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full text-xs font-bold">{{ $zone->restaurants_count }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.zones.edit', $zone) }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold">تعديل</a>
                                    <form action="{{ route('admin.zones.destroy', $zone) }}" method="POST" onsubmit="return confirm('حذف هذه المنطقة؟')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-500 hover:text-red-700 text-xs font-bold">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-gray-400">لا توجد مناطق</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">{{ $zones->links() }}</div>
    </div>
@endsection
