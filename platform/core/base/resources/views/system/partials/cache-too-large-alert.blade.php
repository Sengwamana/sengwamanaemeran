<x-core::alert
    type="warning"
    icon="ti ti-alert-circle"
>
    {{ trans('core/base::system.cache_too_large_alert', ['size' => $size]) }}
</x-core::alert>
