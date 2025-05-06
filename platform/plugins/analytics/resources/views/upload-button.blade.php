<x-core::button
    size="md"
    data-bb-toggle="analytics-trigger-upload-json"
    data-url="{{ route('analytics.settings.json') }}"
>
    <x-core::icon name="ti ti-upload" /> {{ __('Upload Service Account JSON File') }}
</x-core::button>
