<div class="dropdown-submenu">
    <x-core::dropdown.item
        :label="trans('core/table::table.bulk_change')"
        icon="ti ti-chevron-right"
        icon-placement="right"
        icon-class="ms-auto me-0"
    />
    <div class="dropdown-menu">
        @foreach ($bulk_changes as $key => $bulk_change)
            <x-core::dropdown.item
                :label="$bulk_change['title']"
                data-key="{{ $key }}"
                data-class-item="{{ $class }}"
                data-save-url="{{ $url }}"
                class="bulk-change-item"
            />
        @endforeach
    </div>
</div>
