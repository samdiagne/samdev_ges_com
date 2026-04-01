@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-col sm:flex-row items-center justify-between gap-3">

        {{-- Info résultats --}}
        <p class="text-xs text-gray-400 order-2 sm:order-1">
            @if ($paginator->firstItem())
                <span class="font-semibold text-dark">{{ $paginator->firstItem() }}</span>
                –
                <span class="font-semibold text-dark">{{ $paginator->lastItem() }}</span>
                sur
                <span class="font-semibold text-dark">{{ $paginator->total() }}</span>
                résultat{{ $paginator->total() > 1 ? 's' : '' }}
            @else
                {{ $paginator->count() }} résultat{{ $paginator->count() > 1 ? 's' : '' }}
            @endif
        </p>

        {{-- Boutons --}}
        <div class="flex items-center gap-1 order-1 sm:order-2">

            {{-- Précédent --}}
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center rounded-lg
                             text-gray-300 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="w-8 h-8 flex items-center justify-center rounded-lg
                          text-gray-500 hover:bg-primary/10 hover:text-primary transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                {{-- Séparateur "..." --}}
                @if (is_string($element))
                    <span class="w-8 h-8 flex items-center justify-center text-xs text-gray-400">
                        {{ $element }}
                    </span>
                @endif

                {{-- Numéros de pages --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-8 h-8 flex items-center justify-center rounded-lg
                                         text-xs font-bold bg-primary text-white shadow-lg
                                         shadow-primary/25 cursor-default">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="w-8 h-8 flex items-center justify-center rounded-lg
                                      text-xs font-medium text-gray-600 hover:bg-primary/10
                                      hover:text-primary transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Suivant --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="w-8 h-8 flex items-center justify-center rounded-lg
                          text-gray-500 hover:bg-primary/10 hover:text-primary transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span class="w-8 h-8 flex items-center justify-center rounded-lg
                             text-gray-300 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif

        </div>
    </nav>
@endif
