@props(['isActive' => false])

@php
    $classes = Arr::toCssClasses([
        'step-item' => true,
        'active' => $isActive,
    ]);
@endphp

<li {{ $attributes->class($classes) }}>{{ $slot }}</li>
