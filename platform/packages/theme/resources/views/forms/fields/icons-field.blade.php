@php
    Arr::set($attributes, 'class', Arr::get($attributes, 'class') . ' icon-select');
    Arr::set($attributes, 'data-empty-value', __('-- None --'));
    Arr::set($attributes, 'data-check-initialized', true);
@endphp

{!! Form::customSelect($name, [$value => $value], $value, $attributes) !!}

@once
    @if (request()->ajax())
        @include('packages/theme::forms.fields.includes.icon-fields-script')

        <script src="{{ asset('vendor/core/packages/theme/js/icons-field.js') }}?v=1.1.0"></script>
    @else
        @include('packages/theme::forms.fields.includes.icon-fields-script')

        @push('footer')
            <script src="{{ asset('vendor/core/packages/theme/js/icons-field.js') }}?v=1.1.0"></script>
        @endpush
    @endif
@endonce
