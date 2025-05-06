@props(['level' => 4])
@php
$tag = match ($level) {
    1 => 'h1',
    2 => 'h2',
    3 => 'h3',
    5 => 'h5',
    6 => 'h6',
    default => 'h4'
}
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => 'card-title']) }}>
    {{ $slot }}
</{{ $tag }}>
