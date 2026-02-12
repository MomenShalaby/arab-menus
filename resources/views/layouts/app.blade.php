<!DOCTYPE html>
<html lang="{{ $currentLocale ?? 'ar' }}" dir="{{ ($isRtl ?? true) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه - دليل منيوهات المطاعم في مصر. ابحث عن منيو أي مطعم واعرف الأسعار والفروع بسهولة' : 'Nakol Eh - Restaurant menu guide in Egypt. Find any restaurant menu, prices and branches easily')">
    <meta name="keywords" content="@yield('meta_keywords', 'ناكل ايه, nakol eh, منيوهات, مطاعم, منيو, اسعار, قوائم طعام, مصر, restaurant menus, menu prices, Egypt restaurants, food delivery')">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="Nakol Eh - ناكل ايه">
    <meta name="application-name" content="ناكل ايه">
    <meta name="apple-mobile-web-app-title" content="ناكل ايه">
    <meta name="theme-color" content="#dc2626">

    <title>@yield('title', ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه - دليل منيوهات المطاعم في مصر' : 'Nakol Eh - Restaurant Menu Guide in Egypt')</title>

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Alternate language URLs -->
    <link rel="alternate" hreflang="ar" href="{{ url()->current() }}?lang=ar">
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه - دليل منيوهات المطاعم في مصر' : 'Nakol Eh - Restaurant Menu Guide in Egypt')">
    <meta property="og:description" content="@yield('meta_description', ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه - دليل منيوهات المطاعم في مصر' : 'Nakol Eh - Restaurant menu guide in Egypt')">
    <meta property="og:image" content="@yield('og_image', asset('images/logo.png'))">
    <meta property="og:site_name" content="ناكل ايه">
    <meta property="og:locale" content="{{ ($currentLocale ?? 'ar') === 'ar' ? 'ar_EG' : 'en_US' }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه - دليل منيوهات المطاعم في مصر' : 'Nakol Eh - Restaurant Menu Guide in Egypt')">
    <meta name="twitter:description" content="@yield('meta_description', ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه - دليل منيوهات المطاعم في مصر' : 'Nakol Eh - Restaurant menu guide in Egypt')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/logo.png'))">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://pagead2.googlesyndication.com">

    <!-- Arabic-friendly font + English font -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- TailwindCSS via CDN (for development - use build process in production) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'arabic': ['Tajawal', 'sans-serif'],
                        'english': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        },
                        accent: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                    },
                },
            },
        }
    </script>

    <style>
        body {
            font-family: {{ ($currentLocale ?? 'ar') === 'ar' ? "'Tajawal', sans-serif" : "'Inter', sans-serif" }};
        }
        [dir="rtl"] .lightbox-prev { right: 20px; left: auto; }
        [dir="rtl"] .lightbox-next { left: 20px; right: auto; }
        [dir="ltr"] .lightbox-prev { left: 20px; right: auto; }
        [dir="ltr"] .lightbox-next { right: 20px; left: auto; }

        /* Lightbox styles */
        .lightbox-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .lightbox-overlay.active {
            display: flex;
        }
        .lightbox-overlay img {
            max-width: 90vw;
            max-height: 90vh;
            object-fit: contain;
        }
        .lightbox-close {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            z-index: 10000;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 2rem;
            cursor: pointer;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Lazy load fade-in */
        .lazy-image {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .lazy-image.loaded {
            opacity: 1;
        }

        /* Cookie banner animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
    </style>

    @stack('styles')

    <!-- Structured Data: WebSite (Sitelinks Search Box) -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "name": "ناكل ايه",
        "alternateName": "Nakol Eh",
        "url": "{{ url('/') }}",
        "description": "{{ ($currentLocale ?? 'ar') === 'ar' ? 'دليلك الشامل لمنيوهات المطاعم في مصر' : 'Your comprehensive guide to restaurant menus in Egypt' }}",
        "inLanguage": ["ar", "en"],
        "potentialAction": {
            "@@type": "SearchAction",
            "target": {
                "@@type": "EntryPoint",
                "urlTemplate": "{{ url('/search') }}?search={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <!-- Structured Data: Organization -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "ناكل ايه - Nakol Eh",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/logo.png') }}",
        "sameAs": []
    }
    </script>

    @stack('structured_data')

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1389148167793942"
     crossorigin="anonymous"></script>
</head>
<body class="bg-gray-50 {{ ($currentLocale ?? 'ar') === 'ar' ? 'font-arabic' : 'font-english' }} text-gray-800 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 flex-shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه - دليل منيوهات المطاعم' : 'Nakol Eh - Restaurant Menu Guide' }}" class="h-10 w-auto sm:h-12" width="48" height="48">
                    <span class="text-lg font-extrabold text-primary-600 hidden sm:block">{{ ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه' : 'Nakol Eh' }}</span>
                </a>

                <!-- Quick Search (Desktop) -->
                <div class="hidden md:block flex-1 max-w-md mx-8 relative">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <label for="nav-live-search" class="sr-only">{{ ($currentLocale ?? 'ar') === 'ar' ? 'ابحث عن مطعم' : 'Search for a restaurant' }}</label>
                        <input type="text" name="search" id="nav-live-search"
                            placeholder="{{ ($currentLocale ?? 'ar') === 'ar' ? 'ابحث عن مطعم...' : 'Search for a restaurant...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                            class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
                        <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600" aria-label="{{ ($currentLocale ?? 'ar') === 'ar' ? 'بحث' : 'Search' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                    <!-- Nav Autocomplete Dropdown -->
                    <div id="nav-search-results" class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 hidden max-h-80 overflow-y-auto"></div>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-primary-600 text-sm font-medium">{{ ($currentLocale ?? 'ar') === 'ar' ? 'الرئيسية' : 'Home' }}</a>
                    <a href="{{ route('search') }}" class="text-gray-600 hover:text-primary-600 text-sm font-medium">{{ ($currentLocale ?? 'ar') === 'ar' ? 'تصفح المطاعم' : 'Browse' }}</a>
                    <a href="{{ route('nakl-eih') }}" class="text-gray-600 hover:text-primary-600 text-sm font-medium">{{ ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه؟' : 'Nakol Eh?' }}</a>
                    <a href="{{ route('picker-wheel') }}" class="text-gray-600 hover:text-primary-600 text-sm font-medium">{{ ($currentLocale ?? 'ar') === 'ar' ? '🎡 العجلة' : '🎡 Wheel' }}</a>
                    <a href="{{ route('about') }}" class="text-gray-600 hover:text-primary-600 text-sm font-medium">{{ ($currentLocale ?? 'ar') === 'ar' ? 'من نحن' : 'About Us' }}</a>
                    <a href="{{ route('contact') }}" class="text-gray-600 hover:text-primary-600 text-sm font-medium">{{ ($currentLocale ?? 'ar') === 'ar' ? 'اتصل بنا' : 'Contact Us' }}</a>
                    <a href="{{ route('privacy') }}" class="text-gray-600 hover:text-primary-600 text-sm font-medium">{{ ($currentLocale ?? 'ar') === 'ar' ? 'الخصوصية' : 'Privacy' }}</a>

                    <!-- Language Toggle -->
                    @if(($currentLocale ?? 'ar') === 'ar')
                        <a href="{{ route('lang.switch', 'en') }}" class="flex items-center gap-1 px-3 py-1.5 rounded-full border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 hover:border-primary-300 transition-colors">
                            EN 🌐
                        </a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}" class="flex items-center gap-1 px-3 py-1.5 rounded-full border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 hover:border-primary-300 transition-colors">
                            عربي 🌐
                        </a>
                    @endif
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500" aria-label="{{ ($currentLocale ?? 'ar') === 'ar' ? 'القائمة' : 'Menu' }}" aria-expanded="false">
                    <svg class="w-6 h-6" id="mobile-menu-icon-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="w-6 h-6 hidden" id="mobile-menu-icon-close" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white shadow-lg">
            <!-- Mobile Search -->
            <div class="px-4 py-3 border-b border-gray-100">
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <label for="mobile-search" class="sr-only">{{ ($currentLocale ?? 'ar') === 'ar' ? 'ابحث عن مطعم' : 'Search for a restaurant' }}</label>
                    <input type="text" name="search" id="mobile-search"
                        placeholder="{{ ($currentLocale ?? 'ar') === 'ar' ? 'ابحث عن مطعم...' : 'Search...' }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
                </form>
            </div>
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-4 py-2.5 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium text-sm transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? '🏠 الرئيسية' : '🏠 Home' }}</a>
                <a href="{{ route('search') }}" class="block px-4 py-2.5 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium text-sm transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? '🍽️ تصفح المطاعم' : '🍽️ Browse Restaurants' }}</a>
                <a href="{{ route('nakl-eih') }}" class="block px-4 py-2.5 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium text-sm transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? '🤔 ناكل ايه؟' : '🤔 Nakol Eh?' }}</a>
                <a href="{{ route('picker-wheel') }}" class="block px-4 py-2.5 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium text-sm transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? '🎡 عجلة الاختيار' : '🎡 Picker Wheel' }}</a>
                <a href="{{ route('about') }}" class="block px-4 py-2.5 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium text-sm transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? 'ℹ️ من نحن' : 'ℹ️ About Us' }}</a>
                <a href="{{ route('contact') }}" class="block px-4 py-2.5 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium text-sm transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? '✉️ اتصل بنا' : '✉️ Contact Us' }}</a>
                <a href="{{ route('privacy') }}" class="block px-4 py-2.5 rounded-xl text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium text-sm transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? '🔒 الخصوصية' : '🔒 Privacy' }}</a>
                <div class="pt-2 border-t border-gray-100 mt-2">
                    @if(($currentLocale ?? 'ar') === 'ar')
                        <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 font-medium text-sm transition-colors">🌐 English</a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}" class="block px-4 py-2.5 rounded-xl text-gray-700 hover:bg-gray-100 font-medium text-sm transition-colors">🌐 عربي</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
        <!-- Header Ad -->
        @if(($adsEnabled ?? false) && !empty($adsHeaderCode ?? ''))
            <div class="ads-container ads-header ad-slot max-w-7xl mx-auto px-4 py-2">
                {!! $adsHeaderCode !!}
            </div>
        @endif

        @yield('content')

        <!-- Footer Ad -->
        @if(($adsEnabled ?? false) && !empty($adsFooterCode ?? ''))
            <div class="ads-container ads-footer ad-slot max-w-7xl mx-auto px-4 py-4">
                {!! $adsFooterCode !!}
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Brand -->
                <div>
                    <h3 class="text-xl font-bold text-white mb-3">{{ ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه' : 'Nakol Eh' }}</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        {{ ($currentLocale ?? 'ar') === 'ar'
                            ? 'دليلك الشامل لمنيوهات المطاعم في مصر. ابحث عن منيو أي مطعم بسهولة تامة.'
                            : 'Your comprehensive guide to restaurant menus in Egypt. Find any restaurant menu easily.' }}
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-3">{{ ($currentLocale ?? 'ar') === 'ar' ? 'روابط سريعة' : 'Quick Links' }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-primary-400 transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? 'الصفحة الرئيسية' : 'Home' }}</a></li>
                        <li><a href="{{ route('search') }}" class="hover:text-primary-400 transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? 'تصفح المطاعم' : 'Browse Restaurants' }}</a></li>
                        <li><a href="{{ route('nakl-eih') }}" class="hover:text-primary-400 transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه؟' : 'Nakol Eh?' }}</a></li>
                        <li><a href="{{ route('picker-wheel') }}" class="hover:text-primary-400 transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? 'عجلة الاختيار' : 'Picker Wheel' }}</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-primary-400 transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? 'من نحن' : 'About Us' }}</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-primary-400 transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? 'اتصل بنا' : 'Contact Us' }}</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-primary-400 transition-colors">{{ ($currentLocale ?? 'ar') === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy' }}</a></li>
                    </ul>
                </div>

                <!-- Stats -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-3">{{ ($currentLocale ?? 'ar') === 'ar' ? 'إحصائيات' : 'Stats' }}</h4>
                    @php
                        $footerStats = cache()->get('site_statistics', [
                            'total_restaurants' => \App\Models\Restaurant::count(),
                            'total_cities' => \App\Models\City::count(),
                            'total_categories' => \App\Models\Category::count(),
                            'scraped_restaurants' => \App\Models\Restaurant::whereNotNull('last_scraped_at')->count(),
                        ]);
                    @endphp
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-800 rounded-lg p-3 text-center">
                            <span class="block text-primary-400 text-lg font-bold">{{ number_format($footerStats['scraped_restaurants'] ?? 0) }}</span>
                            <span class="text-gray-400">{{ ($currentLocale ?? 'ar') === 'ar' ? 'مطعم' : 'Restaurants' }}</span>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-3 text-center">
                            <span class="block text-primary-400 text-lg font-bold">{{ number_format($footerStats['total_cities'] ?? 0) }}</span>
                            <span class="text-gray-400">{{ ($currentLocale ?? 'ar') === 'ar' ? 'مدينة' : 'Cities' }}</span>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-3 text-center">
                            <span class="block text-accent-400 text-lg font-bold">{{ number_format($footerStats['total_categories'] ?? 0) }}</span>
                            <span class="text-gray-400">{{ ($currentLocale ?? 'ar') === 'ar' ? 'فئة' : 'Categories' }}</span>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-3 text-center">
                            <span class="block text-accent-400 text-lg font-bold">{{ number_format(\App\Models\MenuImage::count()) }}</span>
                            <span class="text-gray-400">{{ ($currentLocale ?? 'ar') === 'ar' ? 'صورة منيو' : 'Menu Images' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-sm text-gray-500">
                <p>© {{ date('Y') }} {{ ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه. جميع الحقوق محفوظة.' : 'Nakol Eh. All rights reserved.' }}</p>
                <p class="mt-2 text-xs text-gray-600">
                    {{ ($currentLocale ?? 'ar') === 'ar'
                        ? 'ناكل ايه - أكبر دليل لمنيوهات المطاعم في مصر. ابحث عن منيو أي مطعم واعرف الأسعار والفروع وأرقام التوصيل.'
                        : 'Nakol Eh - The largest restaurant menu directory in Egypt. Find any restaurant menu, prices, branches and delivery numbers.' }}
                </p>
            </div>
        </div>
    </footer>

    <!-- Lightbox -->
    <div class="lightbox-overlay" id="lightbox">
        <div class="lightbox-close" onclick="closeLightbox()">✕</div>
        <div class="lightbox-nav lightbox-prev" onclick="prevImage()">→</div>
        <div class="lightbox-nav lightbox-next" onclick="nextImage()">←</div>
        <img id="lightbox-img" src="" alt="Menu Image">
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white text-sm bg-black/50 px-4 py-1 rounded-full" id="lightbox-counter"></div>
    </div>

    <!-- Cookie Consent Banner -->
    <div id="cookie-consent" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[10000] items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 sm:p-8 animate-fade-in" dir="{{ ($isRtl ?? true) ? 'rtl' : 'ltr' }}">
            <div class="flex items-start gap-4 mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">
                        {{ ($currentLocale ?? 'ar') === 'ar' ? 'ملفات تعريف الارتباط (Cookies)' : 'Cookie Consent' }}
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ ($currentLocale ?? 'ar') === 'ar'
                            ? 'نستخدم ملفات تعريف الارتباط لتحسين تجربتك وعرض إعلانات مخصصة. بالموافقة، تسمح لنا باستخدام هذه الملفات.'
                            : 'We use cookies to improve your experience and show personalized ads. By accepting, you allow us to use these cookies.' }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3">
                <button id="decline-cookies"
                    class="flex-1 px-4 py-3 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-sm">
                    {{ ($currentLocale ?? 'ar') === 'ar' ? 'رفض' : 'Decline' }}
                </button>
                <button id="accept-cookies"
                    class="flex-1 px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-colors text-sm shadow-lg">
                    {{ ($currentLocale ?? 'ar') === 'ar' ? 'موافق' : 'Accept' }}
                </button>
            </div>

            <p class="text-xs text-gray-400 mt-4 text-center">
                {{ ($currentLocale ?? 'ar') === 'ar'
                    ? 'يمكنك تغيير تفضيلاتك في أي وقت من إعدادات المتصفح'
                    : 'You can change your preferences anytime in your browser settings' }}
            </p>
        </div>
    </div>

    <script>
        // ====== NAV LIVE SEARCH ======
        (function() {
            const navSearchInput = document.getElementById('nav-live-search');
            const navSearchResults = document.getElementById('nav-search-results');
            let navSearchTimer = null;

            if (navSearchInput && navSearchResults) {
                navSearchInput.addEventListener('input', () => {
                    clearTimeout(navSearchTimer);
                    const q = navSearchInput.value.trim();
                    if (q.length < 2) {
                        navSearchResults.classList.add('hidden');
                        navSearchResults.innerHTML = '';
                        return;
                    }
                    navSearchTimer = setTimeout(async () => {
                        try {
                            const res = await fetch(`/api/search?q=${encodeURIComponent(q)}`);
                            const data = await res.json();
                            if (data.length === 0) {
                                navSearchResults.innerHTML = '<div class="p-4 text-center text-gray-400 text-sm">لا توجد نتائج</div>';
                                navSearchResults.classList.remove('hidden');
                                return;
                            }
                            navSearchResults.innerHTML = data.map(r => `
                                <a href="${r.url}" class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-b-0">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                                        ${r.logo_url ? `<img src="${r.logo_url}" alt="${r.name}" class="w-full h-full object-contain">` : `<span class="text-xs font-bold text-primary-600">${r.name.charAt(0)}</span>`}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-800 text-sm truncate">${r.name}</div>
                                        <div class="text-xs text-gray-400 truncate">${r.categories.map(c => c.name_ar || c.name).join(' • ')}</div>
                                    </div>
                                </a>
                            `).join('');
                            navSearchResults.classList.remove('hidden');
                        } catch (e) {
                            console.error('Nav search failed:', e);
                        }
                    }, 300);
                });

                document.addEventListener('click', (e) => {
                    if (!navSearchInput.contains(e.target) && !navSearchResults.contains(e.target)) {
                        navSearchResults.classList.add('hidden');
                    }
                });

                navSearchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') navSearchResults.classList.add('hidden');
                });
            }
        })();

        // Lightbox functionality
        let lightboxImages = [];
        let currentImageIndex = 0;

        function openLightbox(images, index) {
            lightboxImages = images;
            currentImageIndex = index;
            showImage();
            document.getElementById('lightbox').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
            document.body.style.overflow = '';
        }

        function showImage() {
            const img = document.getElementById('lightbox-img');
            img.src = lightboxImages[currentImageIndex];
            document.getElementById('lightbox-counter').textContent =
                `${currentImageIndex + 1} / ${lightboxImages.length}`;
        }

        function nextImage() {
            currentImageIndex = (currentImageIndex + 1) % lightboxImages.length;
            showImage();
        }

        function prevImage() {
            currentImageIndex = (currentImageIndex - 1 + lightboxImages.length) % lightboxImages.length;
            showImage();
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (!document.getElementById('lightbox').classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') nextImage();
            if (e.key === 'ArrowRight') prevImage();
        });

        // Close on overlay click
        document.getElementById('lightbox').addEventListener('click', (e) => {
            if (e.target === document.getElementById('lightbox')) closeLightbox();
        });

        // Lazy loading images
        document.addEventListener('DOMContentLoaded', () => {
            const lazyImages = document.querySelectorAll('img[data-src]');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.onload = () => img.classList.add('loaded');
                        img.onerror = () => {
                            img.src = 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" fill="%23f3f4f6"><rect width="400" height="300"/><text x="50%" y="50%" text-anchor="middle" fill="%239ca3af" font-size="14">الصورة غير متاحة</text></svg>';
                            img.classList.add('loaded');
                        };
                        observer.unobserve(img);
                    }
                });
            }, { rootMargin: '200px' });

            lazyImages.forEach(img => observer.observe(img));
        });

        // ====== COOKIE CONSENT ======
        (function() {
            const CONSENT_KEY = 'nakol_eh_cookie_consent';
            const consentModal = document.getElementById('cookie-consent');
            const adSlots = document.querySelectorAll('.ad-slot');

            // Check if consent has been given
            const consent = localStorage.getItem(CONSENT_KEY);

            // Hide ads if consent was declined
            if (consent === 'declined') {
                adSlots.forEach(slot => slot.style.display = 'none');
            }

            if (!consent) {
                // Hide ads until consent is given
                adSlots.forEach(slot => slot.style.display = 'none');

                // Show consent banner after a short delay
                setTimeout(() => {
                    consentModal.classList.remove('hidden');
                    consentModal.classList.add('flex');
                }, 1000);
            }

            // Accept button
            document.getElementById('accept-cookies').addEventListener('click', () => {
                localStorage.setItem(CONSENT_KEY, 'accepted');
                consentModal.classList.add('hidden');
                consentModal.classList.remove('flex');
                // Show ads
                adSlots.forEach(slot => slot.style.display = '');
                // Reload to initialize AdSense if needed
                const adsEnabled = {{ ($adsEnabled ?? false) ? 'true' : 'false' }};
                if (adsEnabled) {
                    setTimeout(() => location.reload(), 300);
                }
            });

            // Decline button
            document.getElementById('decline-cookies').addEventListener('click', () => {
                localStorage.setItem(CONSENT_KEY, 'declined');
                consentModal.classList.add('hidden');
                consentModal.classList.remove('flex');
                // Keep ads hidden
                adSlots.forEach(slot => slot.style.display = 'none');
            });
        })();
    </script>

    @stack('scripts')

    <!-- Mobile menu toggle -->
    <script>
        (function() {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const iconOpen = document.getElementById('mobile-menu-icon-open');
            const iconClose = document.getElementById('mobile-menu-icon-close');
            if (btn && menu) {
                btn.addEventListener('click', () => {
                    const isOpen = !menu.classList.contains('hidden');
                    menu.classList.toggle('hidden');
                    iconOpen.classList.toggle('hidden');
                    iconClose.classList.toggle('hidden');
                    btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                });
            }
        })();
    </script>
</body>
</html>
