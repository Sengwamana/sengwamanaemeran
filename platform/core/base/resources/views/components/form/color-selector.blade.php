@props([
    'id' => null,
    'label' => null,
    'name' => null,
    'choices' => [],
    'selected' => null,
    'wrapperClass' => null,
    'helperText' => null,
    'required' => false,
])

@php
    $id ??= $name ?? Str::random(8);
@endphp

<x-core::form-group :class="$wrapperClass">
    @if ($label)
        <x-core::form.label
            :label="$label"
            :for="$id"
            @class(['required' => $required])
        />
   @endif

    <div class="row g-2">
        @foreach($choices as $key => $item)
            <div class="col-auto">
                <label class="form-colorinput form-colorinput-light">
                    @php
                        $checkboxValue = is_string($key) && strlen($key) > 1 ? $key : $item;
                    @endphp
                    <input name="{{ $name }}" type="radio" value="{{ $checkboxValue }}" class="form-colorinput-input" @checked($checkboxValue == $selected)>
                    <span class="form-colorinput-color" style="background-color:{{ $item }};"></span>
                </label>
            </div>
        @endforeach
    </div>

    @if ($helperText)
        <x-core::form.helper-text>{!! $helperText !!}</x-core::form.helper-text>
    @endif
</x-core::form-group>
