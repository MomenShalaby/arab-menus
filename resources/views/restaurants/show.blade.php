@extends('layouts.app')

@section('title', (($currentLocale ?? 'ar') === 'ar' ? 'منيو ' . ($restaurant->name_ar ?? $restaurant->name) . ' | منيو مطعم ' . ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name . ' Menu') . ' | ' . (($currentLocale ?? 'ar') === 'ar' ? 'الأسعار والفروع' : 'Prices & Branches') . ' - ناكل ايه')
@section('meta_description', ($currentLocale ?? 'ar') === 'ar' ? 'منيو ' . ($restaurant->name_ar ?? $restaurant->name) . ' | منيو مطعم ' . ($restaurant->name_ar ?? $restaurant->name) . ' - اطلع على قائمة الطعام والأسعار والفروع وأرقام التوصيل. ' . ($restaurant->categories->pluck('name_ar')->filter()->implode(' و ') ?: '') : $restaurant->name . ' menu - View food menu, prices, branches and delivery numbers')
@section('meta_keywords', ($restaurant->name_ar ?? $restaurant->name) . ', ' . $restaurant->name . ', منيو ' . ($restaurant->name_ar ?? $restaurant->name) . ', منيو مطعم ' . ($restaurant->name_ar ?? $restaurant->name) . ', اسعار ' . ($restaurant->name_ar ?? $restaurant->name) . ', فروع ' . ($restaurant->name_ar ?? $restaurant->name) . ', ' . ($restaurant->categories->pluck('name_ar')->filter()->implode(', ') ?: ''))
@section('og_type', 'restaurant')
@section('og_image', $restaurant->logo_url ?? asset('images/logo.png'))

@push('structured_data')
<!-- Structured Data: Restaurant -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Restaurant",
    "name": "{{ $restaurant->name_ar ?? $restaurant->name }}",
    "alternateName": "{{ $restaurant->name }}",
    "url": "{{ route('restaurant.show', $restaurant->slug) }}",
    @if($restaurant->logo_url)
    "image": "{{ $restaurant->logo_url }}",
    @endif
    @if($restaurant->hotline)
    "telephone": "{{ $restaurant->hotline }}",
    @endif
    "servesCuisine": {!! json_encode($restaurant->categories->pluck('name_ar')->filter()->values()) !!},
    @if($restaurant->cities->isNotEmpty())
    "address": {
        "@@type": "PostalAddress",
        "addressLocality": "{{ $restaurant->cities->first()->name_ar ?? $restaurant->cities->first()->name }}",
        "addressCountry": "EG"
    },
    @endif
    "aggregateRating": {
        "@@type": "AggregateRating",
        "ratingValue": "4.5",
        "bestRating": "5",
        "ratingCount": "{{ max($restaurant->total_views, 1) }}"
    },
    @if($restaurant->menuImages->isNotEmpty())
    "hasMenu": {
        "@@type": "Menu",
        "name": "{{ ($currentLocale ?? 'ar') === 'ar' ? 'منيو ' . ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name . ' Menu' }}",
        "url": "{{ route('restaurant.show', $restaurant->slug) }}"
    },
    @endif
    "numberOfEmployees": {
        "@@type": "QuantitativeValue"
    }
}
</script>

<!-- Structured Data: BreadcrumbList -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "{{ ($currentLocale ?? 'ar') === 'ar' ? 'الرئيسية' : 'Home' }}",
            "item": "{{ route('home') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "{{ ($currentLocale ?? 'ar') === 'ar' ? 'المطاعم' : 'Restaurants' }}",
            "item": "{{ route('search') }}"
        },
        {
            "@@type": "ListItem",
            "position": 3,
            "name": "{{ ($currentLocale ?? 'ar') === 'ar' ? ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name }}"
        }
    ]
}
</script>

<!-- Structured Data: WebPage -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "{{ ($currentLocale ?? 'ar') === 'ar' ? 'منيو مطعم ' . ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name . ' Menu' }}",
    "url": "{{ route('restaurant.show', $restaurant->slug) }}",
    "description": "{{ ($currentLocale ?? 'ar') === 'ar' ? 'منيو ' . ($restaurant->name_ar ?? $restaurant->name) . ' مع الأسعار والفروع' : $restaurant->name . ' menu with prices and branches' }}",
    "isPartOf": {
        "@@type": "WebSite",
        "name": "ناكل ايه",
        "url": "{{ url('/') }}"
    },
    "about": {
        "@@type": "Restaurant",
        "name": "{{ $restaurant->name_ar ?? $restaurant->name }}"
    }
}
</script>

@if($restaurant->menuImages->isNotEmpty())
<!-- Structured Data: ImageGallery -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ImageGallery",
    "name": "{{ ($currentLocale ?? 'ar') === 'ar' ? 'منيو ' . ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name . ' Menu' }}",
    "about": {
        "@@type": "Restaurant",
        "name": "{{ $restaurant->name_ar ?? $restaurant->name }}"
    },
    "image": {!! json_encode($restaurant->menuImages->pluck('image_url')->values()) !!}
}
</script>
@endif
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6 sm:py-8">
        <!-- Breadcrumb (SEO-friendly with structured data) -->
        <nav class="text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6 flex items-center gap-1 sm:gap-2 overflow-x-auto" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-primary-600 whitespace-nowrap">{{ ($currentLocale ?? 'ar') === 'ar' ? 'الرئيسية' : 'Home' }}</a>
            <span>/</span>
            <a href="{{ route('search') }}" class="hover:text-primary-600 whitespace-nowrap">{{ ($currentLocale ?? 'ar') === 'ar' ? 'المطاعم' : 'Restaurants' }}</a>
            <span>/</span>
            <span class="text-gray-800 font-medium truncate">{{ ($currentLocale ?? 'ar') === 'ar' ? ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name }}</span>
        </nav>

        <!-- Restaurant Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
            <div class="p-4 sm:p-6 md:p-8 flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6">
                <!-- Logo -->
                <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-32 md:h-32 bg-gray-50 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden border border-gray-100">
                    @if($restaurant->logo_url)
                        <img src="{{ $restaurant->logo_url }}"
                            alt="{{ ($currentLocale ?? 'ar') === 'ar' ? 'لوجو ' . ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name . ' logo' }}"
                            class="w-full h-full object-contain p-2"
                            width="128" height="128">
                    @else
                        <div class="w-14 h-14 sm:w-20 sm:h-20 bg-primary-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl sm:text-3xl font-bold text-primary-600">{{ mb_substr($restaurant->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-1 text-center sm:text-start">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-800 mb-2">{{ ($currentLocale ?? 'ar') === 'ar' ? 'منيو مطعم ' . ($restaurant->name_ar ?? $restaurant->name) : $restaurant->name . ' Menu' }}</h1>
                    @if(($currentLocale ?? 'ar') === 'ar')
                        <p class="text-sm text-gray-500 mb-3">{{ 'منيو ' . ($restaurant->name_ar ?? $restaurant->name) . ' • الأسعار • الفروع • أرقام التوصيل' }}</p>
                    @endif

                    <!-- Categories -->
                    @if($restaurant->categories->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($restaurant->categories as $category)
                                <span class="inline-block bg-primary-50 text-primary-700 text-xs font-medium px-3 py-1 rounded-full">
                                    {{ ($currentLocale ?? 'ar') === 'ar' ? ($category->name_ar ?? $category->name) : $category->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Phone Numbers (each individually clickable) -->
                    @if($restaurant->hotline)
                        @php
                            $phones = preg_split('/[\s]*[\-\/,،]+[\s]*/', trim($restaurant->hotline));
                            $phones = array_values(array_filter(array_map('trim', $phones), fn($p) => preg_match('/\d{4,}/', $p)));
                        @endphp
                        @if(count($phones) > 0)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($phones as $phone)
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}"
                                        class="inline-flex items-center gap-1.5 text-green-600 hover:text-green-700 font-semibold bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-full transition-colors text-sm" dir="ltr">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ $phone }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    @endif

                    <!-- Details Row -->
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                        @if($restaurant->cities->isNotEmpty())
                            @php
                                $cityNames = $restaurant->cities
                                    ->map(fn($city) => ($currentLocale ?? 'ar') === 'ar' ? ($city->name_ar ?: $city->name) : $city->name)
                                    ->implode(' • ');
                            @endphp
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $cityNames }}
                            </div>
                        @endif

                        @if($restaurant->menuImages->isNotEmpty())
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $restaurant->menuImages->count() }} {{ ($currentLocale ?? 'ar') === 'ar' ? 'صورة للمنيو' : 'menu images' }}
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

        <!-- Ad: Under Restaurant Header (native-looking, labeled) -->
        @if(($adsEnabled ?? false) && !empty($adsRestaurantHeaderCode ?? ''))
            <div class="ad-slot mb-8 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 p-2 text-center">
                <div class="text-[10px] text-gray-300 uppercase tracking-wider mb-0.5">{{ ($currentLocale ?? 'ar') === 'ar' ? 'إعلان' : 'Ad' }}</div>
                {!! $adsRestaurantHeaderCode !!}
            </div>
        @else
            <div class="mb-4"></div>
        @endif

        <!-- Menu Images Gallery -->
        @if($restaurant->menuImages->isNotEmpty())
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    {{ ($currentLocale ?? 'ar') === 'ar' ? 'المنيو' : 'Menu' }}
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    @foreach($restaurant->menuImages as $index => $image)
                        <div class="group relative bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden cursor-pointer hover:shadow-lg transition-shadow duration-300"
                            onclick="openLightbox(menuImages, {{ $index }})">
                            <!-- Image -->
                            <div class="aspect-[3/4] bg-gray-50 overflow-hidden">
                                <img data-src="{{ $image->image_url }}"
                                    alt="{{ ($currentLocale ?? 'ar') === 'ar' ? 'منيو ' . ($restaurant->name_ar ?? $restaurant->name) . ' صفحة ' . ($index + 1) . ' - الأسعار' : $restaurant->name . ' menu page ' . ($index + 1) . ' - prices' }}"
                                    class="lazy-image w-full h-full object-contain hover:scale-105 transition-transform duration-500"
                                    loading="lazy"
                                    width="400" height="533">
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
                                <span class="text-white text-sm font-medium">{{ ($currentLocale ?? 'ar') === 'ar' ? 'صفحة' : 'Page' }} {{ $index + 1 }}</span>
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
                <h3 class="text-lg font-bold text-gray-500 mb-1">{{ ($currentLocale ?? 'ar') === 'ar' ? 'لا توجد صور للمنيو' : 'No menu images' }}</h3>
                <p class="text-sm text-gray-400">{{ ($currentLocale ?? 'ar') === 'ar' ? 'لم يتم تحميل صور المنيو لهذا المطعم بعد' : 'Menu images have not been uploaded yet for this restaurant' }}</p>
            </div>
        @endif

        <!-- Ad: After Menu Images -->
        @if(($adsEnabled ?? false) && !empty($adsAfterMenuCode ?? ''))
            <div class="ad-slot mb-8 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 p-2 text-center">
                <div class="text-[10px] text-gray-300 uppercase tracking-wider mb-0.5">{{ ($currentLocale ?? 'ar') === 'ar' ? 'إعلان' : 'Ad' }}</div>
                {!! $adsAfterMenuCode !!}
            </div>
        @endif

        <!-- Branches Section -->
        @if($restaurant->branches && $restaurant->branches->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6 sm:mb-8 p-4 sm:p-6 md:p-8">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ ($currentLocale ?? 'ar') === 'ar' ? 'الفروع' : 'Branches' }} ({{ $restaurant->branches->count() }})
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

        <!-- Similar Restaurants + Sidebar Ad -->
        <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">
            @if($similar->isNotEmpty())
                <div class="flex-1">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">{{ ($currentLocale ?? 'ar') === 'ar' ? 'مطاعم مشابهة' : 'Similar Restaurants' }}</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                        @foreach($similar as $sim)
                            @if($sim->slug)
                            <a href="{{ route('restaurant.show', $sim->slug) }}"
                                class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary-200"
                                title="{{ ($currentLocale ?? 'ar') === 'ar' ? 'منيو ' . ($sim->name_ar ?? $sim->name) : $sim->name . ' menu' }}">
                                <div class="aspect-square bg-gray-50 flex items-center justify-center p-3 sm:p-4 overflow-hidden">
                                    @if($sim->logo_url)
                                        <img data-src="{{ $sim->logo_url }}"
                                            alt="{{ ($currentLocale ?? 'ar') === 'ar' ? 'منيو ' . ($sim->name_ar ?? $sim->name) : $sim->name . ' menu' }}"
                                            class="lazy-image w-full h-full object-contain group-hover:scale-110 transition-transform duration-300"
                                            loading="lazy"
                                            width="200" height="200">
                                    @else
                                        <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center">
                                            <span class="text-xl font-bold text-primary-600">{{ mb_substr($sim->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3 text-center border-t border-gray-50">
                                    <h3 class="font-bold text-gray-800 text-sm truncate">{{ ($currentLocale ?? 'ar') === 'ar' ? ($sim->name_ar ?? $sim->name) : $sim->name }}</h3>
                                </div>
                            </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Sidebar Ad (sticky on desktop) -->
            @if(($adsEnabled ?? false) && !empty($adsSidebarCode ?? ''))
                <div class="lg:w-72 flex-shrink-0">
                    <div class="lg:sticky lg:top-24">
                        <div class="ad-slot rounded-xl overflow-hidden bg-gray-50 border border-gray-100 p-2 text-center">
                            <div class="text-[10px] text-gray-300 uppercase tracking-wider mb-0.5">{{ ($currentLocale ?? 'ar') === 'ar' ? 'إعلان' : 'Ad' }}</div>
                            {!! $adsSidebarCode !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Prepare menu images for lightbox
    const menuImages = @json($restaurant->menuImages->pluck('image_url'));
</script>
@endpush
