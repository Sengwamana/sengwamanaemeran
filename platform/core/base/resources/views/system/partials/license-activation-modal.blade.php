<x-core::modal
    id="quick-activation-license-modal"
    :title="__('License Activation')"
>
    <form
        action="{{ route('settings.license.activate') }}"
        method="POST"
        data-bb-toggle="activate-license"
        data-reload="true"
    >
        <x-core::license.form :reset="false" :is-vue="false" />
    </form>
</x-core::modal>

@if (Request::ajax())
    <script src="{{ asset('vendor/core/core/base/js/license-activation.js') }}"></script>
@else
    @push('footer')
        <script src="{{ asset('vendor/core/core/base/js/license-activation.js') }}"></script>
    @endpush
@endif
