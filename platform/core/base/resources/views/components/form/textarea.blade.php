@props([
    'id' => null,
    'label' => null,
    'name' => null,
    'value' => old($name),
    'helperText' => null,
    'errorKey' => $name,
])

@php
    $id = $attributes->get('id', $name) ?? Str::random(8);

    $classes = Arr::toCssClasses(['form-control', 'is-invalid' => $errors->has($errorKey)]);
@endphp

<x-core::form-group>
    @if ($label)
        <x-core::form.label
            :label="$label"
            :for="$id"
        />
    @endif

    <textarea {{ $attributes->merge(['name' => $name, 'id' => $id])->class($classes) }}>{{ $value ?: $slot }}</textarea>

    @if ($helperText)
        <x-core::form.helper-text>{!! $helperText !!}</x-core::form.helper-text>
    @endif

    <x-core::form.error :key="$errorKey" />
</x-core::form-group>
