<span class="dt-length-records">
    <x-core::icon name="ti ti-world" />
    <span class="d-none d-sm-inline">{{ trans('core/base::tables.show_from') }}</span>
    _START_
    {{ trans('core/base::tables.to') }} _END_ {{ trans('core/base::tables.in') }}
    <x-core::badge color="secondary" label="_TOTAL_" />
    <span class="hidden-xs">{{ trans('core/base::tables.records') }}</span>
</span>
