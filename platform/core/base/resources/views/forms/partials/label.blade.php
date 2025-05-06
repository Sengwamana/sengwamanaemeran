@php
    $for = $name;

    if (isset($attributes['for'])) {
        $for = $attributes['for'];
    }

    // Trigger the label method from the Form class. It will add this label to $this->labels array.
    Form::label($for, $label, $attributes, $escapeHtml);
@endphp

<x-core::form.label
    :for="$for"
    :label="$label"
    :attributes="new Illuminate\View\ComponentAttributeBag($attributes)"
/>
