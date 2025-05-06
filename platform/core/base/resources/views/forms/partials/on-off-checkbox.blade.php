<x-core::form.on-off.checkbox
    :id="$attributes['id'] ?? $name . '_' . md5($name)"
    :name="$name"
    :checked="$value"
    :wrapper="false"
    :attributes="new Illuminate\View\ComponentAttributeBag((array) $attributes)"
/>
