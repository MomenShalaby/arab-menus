@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ ($currentLocale ?? 'ar') === 'ar' ? 'التنقل بين الصفحات' : 'Pagination Navigation' }}" class="flex items-center justify-center gap-1 flex-wrap">
        {{-- Previous Page --}}
        @if ($paginator->onFirstPage())
            <span class="px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                {{ ($currentLocale ?? 'ar') === 'ar' ? 'السابق' : 'Previous' }}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                class="px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 transition-colors">
                {{ ($currentLocale ?? 'ar') === 'ar' ? 'السابق' : 'Previous' }}
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white bg-primary-600 rounded-lg shadow-sm" aria-current="page">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                            class="px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 transition-colors"
                            aria-label="{{ ($currentLocale ?? 'ar') === 'ar' ? 'الصفحة ' . $page : 'Page ' . $page }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                class="px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 transition-colors">
                {{ ($currentLocale ?? 'ar') === 'ar' ? 'التالي' : 'Next' }}
            </a>
        @else
            <span class="px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                {{ ($currentLocale ?? 'ar') === 'ar' ? 'التالي' : 'Next' }}
            </span>
        @endif
    </nav>
@endif
