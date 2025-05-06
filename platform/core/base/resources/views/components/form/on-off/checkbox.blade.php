@php
    $value = 1;
    $wrapper = $wrapper ?? true;
@endphp

@if($wrapper)
    <x-core::form-group class="{{ $wrapperClass ?? null }}">
        <input type="hidden" name="{{ $name }}" value="0">

        @include('core/base::components.form.checkbox')
    </x-core::form-group>
@else
    <input type="hidden" name="{{ $name }}" value="0">

    @include('core/base::components.form.checkbox')
@endif
