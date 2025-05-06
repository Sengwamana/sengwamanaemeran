@php
    $values = $values == '[null]' ? '[]' : $values;
    $attributes = $attributes ?? [];
    $allowThumb = Arr::get($attributes, 'allow_thumb', true);
    $images = array_filter((array) old($name, !is_array($values) ? json_decode($values ?: '', true) : $values));
@endphp

<x-core::form.images
    :name="$name"
    :allow-thumb="true"
    :images="$images"
/>
