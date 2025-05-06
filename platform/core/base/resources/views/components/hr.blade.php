@props([
    'text' => null,
    'textAlignment' => null,
])

@php
    $classes = Arr::toCssClasses([
        'hr-text' => $text,
        match ($textAlignment) {
            'left' => 'hr-text-left',
            'right' => 'hr-text-right',
            default => null,
        },
    ]);
    
    $tag = $text ? 'div' : 'hr';
@endphp

<{{ $tag }} {{ $attributes->class($classes) }}>{{ $text }}</{{ $tag }}>
