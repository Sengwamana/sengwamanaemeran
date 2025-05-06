@php
    Arr::set($selectAttributes, 'class', Arr::get($selectAttributes, 'class') . ' select-autocomplete');
@endphp

@include('core/base::forms.partials.custom-select')
