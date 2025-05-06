@php
    $values = Arr::wrap($values ?? []);

    $attributes = (array) $attributes;

    $multiple = count($values) > 1;
@endphp

<div class="position-relative form-check-group">
    @foreach ($values as $key => $option)
        @php
            if ($multiple && isset($attributes['id'])) {
                $attributes['id'] = $attributes['id'] . '_' . $key;
            }
        @endphp

        <x-core::form.radio
            :name="$name"
            :value="$key"
            :checked="$key == $selected"
            :attributes="new Illuminate\View\ComponentAttributeBag($attributes)"
        >
            {{ $option }}
        </x-core::form.radio>
    @endforeach
</div>

