@props([
    'id' => null,
    'type' => 'text',
    'label' => null,
    'labelSrOnly' => false,
    'name' => null,
    'value' => old($name),
    'wrapperClass' => null,
    'wrapperClassDefault' => 'mb-3 position-relative',
    'helperText' => null,
    'labelDescription' => null,
    'rounded' => false,
    'errorKey' => $name,
    'inputGroup' => false,
    'inputIcon' => false,
    'groupFlat' => false,
    'required' => false,
])

@php
    $id ??= $name ?? Str::random(8);
    $wrapperClass = Arr::toCssClasses([$wrapperClass, 'input-icon' => $inputIcon]);
    $classes = Arr::toCssClasses(['form-control', 'is-invalid' => $errors->has($errorKey), 'form-control-rounded' => $rounded]);
    $inputGroup = !$inputIcon && ($inputGroup || isset($prepend) || isset($append));
@endphp

<x-core::form-group :class="$wrapperClass" :default-class="$wrapperClassDefault">
    @if ($label)
        <x-core::form.label
            :label="$label"
            :for="$id"
            :description="$labelDescription"
            @class(['required' => $required, 'sr-only' => $labelSrOnly])
        />
    @endif

    @if ($inputGroup || $inputIcon)
        <div @class([
            'input-group' => $inputGroup,
            'input-icon' => $inputIcon,
            'input-group-flat' => $groupFlat,
        ])>
    @endif

    @isset($prepend)
        {!! $prepend !!}
    @endisset

    <input {{ $attributes->merge(['type' => $type, 'name' => $name, 'id' => $id, 'value' => $value, 'required' => $required])->class($classes) }} />

    @if ($helperText && ! $inputGroup)
        <x-core::form.helper-text>{!! $helperText !!}</x-core::form.helper-text>
    @endif

    @isset($append)
        {!! $append !!}
    @endisset

    @if ($inputGroup || $inputIcon)
        </div>
    @endif

    @if ($helperText && $inputGroup)
        <x-core::form.helper-text>{!! $helperText !!}</x-core::form.helper-text>
    @endif

    <x-core::form.error :key="$errorKey" />

    {{ $slot }}
</x-core::form-group>
