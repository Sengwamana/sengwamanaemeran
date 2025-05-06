@if ($paginator->hasPages())
    <nav class="d-flex justify-content-center align-items-center">
        <div>
            <ul class="pagination gap-2">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="icon-xl fs-5 page-link pagination_item border-0 rounded-circle icon-shape fw-bold bg-600" aria-hidden="true">
                            <i class="ri-arrow-left-line"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="icon-xl fs-5 page-link pagination_item border-0 rounded-circle icon-shape fw-bold bg-600" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                            <i class="ri-arrow-left-line"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="icon-xl fs-5 page-link pagination_item border-0 rounded-circle icon-shape fw-bold bg-600">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span class="icon-xl fs-5 page-link pagination_item border-0 rounded-circle icon-shape fw-bold bg-600">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="icon-xl fs-5 page-link pagination_item border-0 rounded-circle icon-shape fw-bold bg-600" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="icon-xl fs-5 page-link pagination_item border-0 rounded-circle icon-shape fw-bold bg-600" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                            <i class="ri-arrow-right-line"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                        <span class="icon-xl fs-5 page-link pagination_item border-0 rounded-circle icon-shape fw-bold bg-600" aria-hidden="true">
                            <i class="ri-arrow-right-line"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
