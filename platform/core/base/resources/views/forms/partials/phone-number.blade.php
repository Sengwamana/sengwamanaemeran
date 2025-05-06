@php
    Assets::addStylesDirectly('vendor/core/core/base/libraries/intl-tel-input/css/intlTelInput.min.css')
        ->addScriptsDirectly([
            'vendor/core/core/base/libraries/intl-tel-input/js/intlTelInput.min.js',
            'vendor/core/core/base/js/phone-number-field.js',
        ]);
@endphp

{!! Form::text(
    $name,
    $value,
    array_merge_recursive($attributes, ['class' => 'js-phone-number-mask form-control']),
) !!}
