@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center gap-1">
        {{-- Previous Page --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                السابق
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
                class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 transition-colors">
                السابق
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-2 text-sm text-gray-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-2 text-sm font-bold text-white bg-primary-600 rounded-lg shadow-sm">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                            class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 transition-colors">
                التالي
            </a>
        @else
            <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                التالي
            </span>
        @endif
    </nav>
@endif
