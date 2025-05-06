@props([
    'tag' => 'a',
    'action' => false,
    'active' => false,
])

@php
    $classes = Arr::toCssClasses([
        'list-group-item',
        'list-group-item-action' => $action,
        'active' => $active,
    ])
@endphp

<{{ $tag }} {{ $attributes->class($classes) }}>
    {{ $slot }}
</{{ $tag }}>
