@extends('layouts.app')

@section('title', $restaurant->name . ' - منيوهات العرب')
@section('meta_description', 'منيو ' . $restaurant->name . ' - اطلع على قائمة الطعام والأرقام والفروع')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-primary-600">الرئيسية</a>
            <span>/</span>
            <a href="{{ route('search') }}" class="hover:text-primary-600">المطاعم</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">{{ $restaurant->name }}</span>
        </nav>

        <!-- Restaurant Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-start gap-6">
                <!-- Logo -->
                <div class="w-24 h-24 sm:w-32 sm:h-32 bg-gray-50 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden border border-gray-100">
                    @if($restaurant->logo_url)
                        <img src="{{ $restaurant->logo_url }}"
                            alt="{{ $restaurant->name }}"
                            class="w-full h-full object-contain p-2">
                    @else
                        <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center">
                            <span class="text-3xl font-bold text-primary-600">{{ mb_substr($restaurant->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-1">
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-800 mb-2">{{ $restaurant->name }}</h1>

                    <!-- Categories -->
                    @if($restaurant->categories->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($restaurant->categories as $category)
                                <span class="inline-block bg-primary-50 text-primary-700 text-xs font-medium px-3 py-1 rounded-full">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Details Row -->
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                        @if($restaurant->hotline)
                            <a href="tel:{{ $restaurant->hotline }}"
                                class="flex items-center gap-1.5 text-green-600 hover:text-green-700 font-semibold bg-green-50 px-3 py-1.5 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $restaurant->hotline }}
                            </a>
                        @endif

                        @if($restaurant->cities->isNotEmpty())
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $restaurant->cities->pluck('name')->implode(' • ') }}
                            </div>
                        @endif

                        @if($restaurant->menuImages->isNotEmpty())
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $restaurant->menuImages->count() }} صورة للمنيو
                            </div>
                        @endif

                        @if($restaurant->updated_at_source)
                            <div class="flex items-center gap-1.5 text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                آخر تحديث: {{ $restaurant->updated_at_source->format('Y-m-d') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Images Gallery -->
        @if($restaurant->menuImages->isNotEmpty())
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    المنيو
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($restaurant->menuImages as $index => $image)
                        <div class="group relative bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden cursor-pointer hover:shadow-lg transition-shadow duration-300"
                            onclick="openLightbox(menuImages, {{ $index }})">
                            <!-- Image -->
                            <div class="aspect-[3/4] bg-gray-50 overflow-hidden">
                                <img data-src="{{ $image->image_url }}"
                                    alt="{{ $image->alt_text ?? $restaurant->name . ' menu ' . ($index + 1) }}"
                                    class="lazy-image w-full h-full object-contain hover:scale-105 transition-transform duration-500"
                                    loading="lazy">
                            </div>

                            <!-- Overlay on hover -->
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white/90 rounded-full p-3 shadow-lg">
                                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Image label -->
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/50 to-transparent p-3">
                                <span class="text-white text-sm font-medium">صفحة {{ $index + 1 }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-gray-50 rounded-2xl p-12 text-center mb-10">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="text-lg font-bold text-gray-500 mb-1">لا توجد صور للمنيو</h3>
                <p class="text-sm text-gray-400">لم يتم تحميل صور المنيو لهذا المطعم بعد</p>
            </div>
        @endif

        <!-- Branches Section -->
        @if($restaurant->branches && $restaurant->branches->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8 p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    الفروع ({{ $restaurant->branches->count() }})
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($restaurant->branches as $branch)
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <h3 class="font-bold text-gray-800 text-sm mb-1">{{ $branch->name_ar ?? $branch->name }}</h3>
                            @if($branch->address)
                                <p class="text-xs text-gray-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    {{ $branch->address_ar ?? $branch->address }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Similar Restaurants -->
        @if($similar->isNotEmpty())
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-6">مطاعم مشابهة</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($similar as $sim)
                        @if($sim->slug)
                        <a href="{{ route('restaurant.show', $sim->slug) }}"
                            class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary-200">
                            <div class="aspect-square bg-gray-50 flex items-center justify-center p-4 overflow-hidden">
                                @if($sim->logo_url)
                                    <img data-src="{{ $sim->logo_url }}"
                                        alt="{{ $sim->name }}"
                                        class="lazy-image w-full h-full object-contain group-hover:scale-110 transition-transform duration-300"
                                        loading="lazy">
                                @else
                                    <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center">
                                        <span class="text-xl font-bold text-primary-600">{{ mb_substr($sim->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3 text-center border-t border-gray-50">
                                <h3 class="font-bold text-gray-800 text-sm truncate">{{ $sim->name }}</h3>
                            </div>
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    // Prepare menu images for lightbox
    const menuImages = @json($restaurant->menuImages->pluck('image_url'));
</script>
@endpush
