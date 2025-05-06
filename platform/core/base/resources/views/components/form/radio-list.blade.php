@props([
    'id' => null,
    'label' => null,
    'labelHelperText' => null,
    'name' => null,
    'value' => null,
    'options' => [],
    'helperText' => null,
    'wrapperClass' => null,
])

<x-core::form-group :class="$wrapperClass">
    @if ($label)
        <x-core::form.label
            :label="$label"
            :for="$id"
            :helperText="$labelHelperText"
        />
    @endif
    <div class="position-relative form-check-group">
        @foreach ($options as $key => $option)
            <x-core::form.radio
                :name="$name"
                :value="$key"
                :checked="$key == $value"
                {{ $attributes }}
            >
                {{ $option }}
            </x-core::form.radio>
        @endforeach
    </div>
    @if ($helperText)
        <x-core::form.helper-text>{!! $helperText !!}</x-core::form.helper-text>
    @endif
</x-core::form-group>
