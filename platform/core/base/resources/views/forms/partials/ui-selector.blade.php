@if (count($choices) > 0)
    @php
        $attributes['name'] = Arr::get($attributes, 'name', $name);
        $attributes['class'] = Arr::get($attributes, 'class', '') . ' form-imagecheck-input';
        $attributes = Arr::except($attributes, ['id', 'type', 'value']);
    @endphp

    <x-core::form.image-check
        :options="$choices"
        :value="$value"
        :attributes="new Illuminate\View\ComponentAttributeBag((array) $attributes)"
    />
@endif
