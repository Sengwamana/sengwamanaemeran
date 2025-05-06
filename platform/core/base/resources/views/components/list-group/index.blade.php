@props([
    'flush' => false,
])

@php
    $classes = Arr::toCssClasses([
        'list-group',
        'list-group-flush' => $flush,
    ])
@endphp

<div {{ $attributes->class($classes) }}>
    {{ $slot }}
</div>
