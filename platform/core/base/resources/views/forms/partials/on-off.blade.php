<x-core::form.toggle
    :id="$attributes['id'] ?? $name . '_' . md5($name)"
    :name="$name"
    :checked="$value"
    :attributes="new Illuminate\View\ComponentAttributeBag((array) $attributes)"
/>
