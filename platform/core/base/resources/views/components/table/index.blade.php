@props([
    'nowrap' => false,
    'hover' => true,
    'striped' => true,
])

@php
    $classes = Arr::toCssClasses(['table table-vcenter card-table', 'table-nowrap' => $nowrap, 'table-hover' => $hover, 'table-striped' => $striped]);
@endphp

<table {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</table>
