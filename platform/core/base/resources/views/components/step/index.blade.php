@props(['color' => null, 'vertical' => false, 'counter' => false])

@php
    $classes = Arr::toCssClasses([
        'steps' => true,
        "steps-$color" => $color,
        'steps-vertical' => $vertical,
        'steps-counter' => $counter,
    ]);
@endphp

<ul {{ $attributes->class($classes) }}>{{ $slot }}</ul>
