@php
    $options = [
        1 => trans('core/base::base.yes'),
        0 => trans('core/base::base.no'),
    ];
@endphp

@include('core/base::components.form.radio-list')
