@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Pagination">
        <ul class="pagination__list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="pagination__item pagination__item--disabled">
                    <span class="pagination__link pagination__link--prev" aria-hidden="true">
                        <svg class="pagination__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="pagination__text">Previous</span>
                    </span>
                </li>
            @else
                <li class="pagination__item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="pagination__link pagination__link--prev" rel="prev" aria-label="Previous page">
                        <svg class="pagination__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="pagination__text">Previous</span>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="pagination__item pagination__item--ellipsis">
                        <span class="pagination__link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination__item pagination__item--active">
                                <span class="pagination__link" aria-current="page">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination__item">
                                <a href="{{ $url }}" class="pagination__link">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="pagination__item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="pagination__link pagination__link--next" rel="next" aria-label="Next page">
                        <span class="pagination__text">Next</span>
                        <svg class="pagination__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </li>
            @else
                <li class="pagination__item pagination__item--disabled">
                    <span class="pagination__link pagination__link--next" aria-hidden="true">
                        <span class="pagination__text">Next</span>
                        <svg class="pagination__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
