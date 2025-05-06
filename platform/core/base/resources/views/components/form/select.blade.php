@props([
    'id' => null,
    'label' => null,
    'labelHelperText' => null,
    'name' => null,
    'options' => [],
    'value' => null,
    'wrapperClass' => null,
    'inputGroup' => false,
    'helperText' => null,
    'searchable' => false,
    'required' => false,
])

@php
    $id ??= $name ?? Str::random(8);

    $classes = Arr::toCssClasses(['form-select', 'is-invalid' => $name && $errors->has($name), 'select-search-full' => $searchable]);
@endphp

<x-core::form-group :class="$wrapperClass">
    @if ($label)
        <x-core::form.label
            :label="$label"
            :for="$id"
            :helper-text="$labelHelperText"
            @class(['required' => $required])
        />
    @endif

    @if ($inputGroup)
        <div class="input-group">
    @endif

    @isset($prepend)
        {!! $prepend !!}
    @endisset

    <select {{ $attributes->merge(['name' => $name, 'id' => $id, 'required' => $required])->class($classes) }}>
        @if (empty($options) && $slot->isNotEmpty())
            {{ $slot }}
        @else
            @foreach ($options as $key => $item)
                <option
                    value="{{ $key }}"
                    @selected(is_array($value) ? in_array($key, $value) : old($name, $value) == $key)
                >{{ $item }}</option>
            @endforeach
        @endif
    </select>

    @isset($append)
        {!! $append !!}
    @endisset

    @if ($inputGroup)
        </div>
    @endif

    @if ($helperText)
        <x-core::form.helper-text>{!! $helperText !!}</x-core::form.helper-text>
    @endif
</x-core::form-group>
