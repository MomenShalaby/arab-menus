<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'منيوهات العرب - ابحث عن منيوهات المطاعم بسهولة وبدون إعلانات')">
    <meta name="keywords" content="منيوهات, مطاعم, توصيل, قوائم طعام, مصر">
    <meta name="robots" content="index, follow">

    <title>@yield('title', 'منيوهات العرب - دليل منيوهات المطاعم')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Arabic-friendly font -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    <!-- TailwindCSS via CDN (for development - use build process in production) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'arabic': ['Tajawal', 'sans-serif'],
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
            font-family: 'Tajawal', sans-serif;
        }

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
        .lightbox-prev { right: 20px; }
        .lightbox-next { left: 20px; }

        /* Lazy load fade-in */
        .lazy-image {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .lazy-image.loaded {
            opacity: 1;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-arabic text-gray-800 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="منيوهات العرب" class="h-12 w-auto">
                </a>

                <!-- Quick Search (Desktop) -->
                <div class="hidden md:block flex-1 max-w-md mx-8 relative">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <input type="text" name="search" id="nav-live-search"
                            placeholder="ابحث عن مطعم..."
                            value="{{ request('search') }}"
                            autocomplete="off"
                            class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
                        <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                    <!-- Nav Autocomplete Dropdown -->
                    <div id="nav-search-results" class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 hidden max-h-80 overflow-y-auto"></div>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-primary-600 text-sm font-medium">الرئيسية</a>
                    <a href="{{ route('search') }}" class="text-gray-600 hover:text-primary-600 text-sm font-medium">تصفح المطاعم</a>
                    <a href="{{ route('nakl-eih') }}" class="text-gray-600 hover:text-primary-600 text-sm font-medium">ناكل ايه؟</a>
                    <a href="{{ route('picker-wheel') }}" class="text-gray-600 hover:text-primary-600 text-sm font-medium">عجلة الاختيار</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Brand -->
                <div>
                    <h3 class="text-xl font-bold text-white mb-3">منيوهات العرب</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        دليلك الشامل لمنيوهات المطاعم في مصر.
                        ابحث عن منيو أي مطعم بسهولة تامة وبدون إعلانات مزعجة.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-3">روابط سريعة</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-primary-400 transition-colors">الصفحة الرئيسية</a></li>
                        <li><a href="{{ route('search') }}" class="hover:text-primary-400 transition-colors">تصفح المطاعم</a></li>
                        <li><a href="{{ route('nakl-eih') }}" class="hover:text-primary-400 transition-colors">ناكل ايه؟</a></li>
                        <li><a href="{{ route('picker-wheel') }}" class="hover:text-primary-400 transition-colors">عجلة الاختيار</a></li>
                    </ul>
                </div>

                <!-- Stats -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-3">إحصائيات</h4>
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
                            <span class="text-gray-400">مطعم</span>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-3 text-center">
                            <span class="block text-primary-400 text-lg font-bold">{{ number_format($footerStats['total_cities'] ?? 0) }}</span>
                            <span class="text-gray-400">مدينة</span>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-3 text-center">
                            <span class="block text-accent-400 text-lg font-bold">{{ number_format($footerStats['total_categories'] ?? 0) }}</span>
                            <span class="text-gray-400">فئة</span>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-3 text-center">
                            <span class="block text-accent-400 text-lg font-bold">{{ number_format(\App\Models\MenuImage::count()) }}</span>
                            <span class="text-gray-400">صورة منيو</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-sm text-gray-500">
                <p>© {{ date('Y') }} منيوهات العرب. جميع الحقوق محفوظة.</p>
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
    </script>

    @stack('scripts')
</body>
</html>
