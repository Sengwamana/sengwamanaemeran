@if ($data instanceof Illuminate\Pagination\LengthAwarePaginator && $data->withQueryString())
    <div class="number_record">
        <input
            type="number"
            class="numb"
            value="{{ $limit }}"
            step="5"
            min="5"
            max="{{ $data->total() }}"
        >
        <div class="btn_grey btn_change_paginate btn_up"></div>
        <div class="btn_grey btn_change_paginate btn_down"></div>
    </div>

    <div class="pagination d-flex align-items-center m-0 ms-auto">
        @php
            $info = $data->total() > 0 ? ($data->currentPage() - 1) * $limit + 1 : 0;
            $info .= '- ' . ($limit < $data->total() ? $data->currentPage() * $limit : $data->total()) . ' ';
            $info .= trans('core/base::tables.in') . ' ' . $data->total() . ' ' . trans('core/base::tables.records');
        @endphp
        <p class="m-0 me-2 text-muted">{{ $info }}</p>
        <li class="page-item">
            <a
                class="page-link page_previous @if ($data->onFirstPage()) disabled @endif"
                href="{{ $data->previousPageUrl() }}"
            >
                <x-core::icon name="ti ti-chevron-left" />
            </a>
        </li>
        <li class="page-item">
            <a
                class="page-link page_next @if (!$data->hasMorePages()) disabled @endif"
                href="{{ $data->nextPageUrl() }}"
            >
                <x-core::icon name="ti ti-chevron-right" />
            </a>
        </li>
    </div>
@endif
