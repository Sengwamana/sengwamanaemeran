<div class="table-actions">
    {!! $extra !!}
    @if (!empty($edit))
        @if (Auth::guard()->user()->hasPermission($edit))
            <a
                class="btn btn-icon btn-sm btn-primary"
                data-bs-toggle="tooltip"
                data-bs-original-title="{{ trans('core/base::tables.edit') }}"
                href="{{ route($edit, $item->id) }}"
            >
                <x-core::icon name="ti ti-edit" />
            </a>
        @endif
    @endif

    @if (!empty($delete))
        @if (Auth::guard()->user()->hasPermission($delete))
            <a
                class="btn btn-icon btn-sm btn-danger deleteDialog"
                data-bs-toggle="tooltip"
                data-section="{{ route($delete, $item->id) }}"
                data-bs-original-title="{{ trans('core/base::tables.delete_entry') }}"
                href="#"
                role="button"
            >
                <x-core::icon name="ti ti-trash" />
            </a>
        @endif
    @endif
</div>
