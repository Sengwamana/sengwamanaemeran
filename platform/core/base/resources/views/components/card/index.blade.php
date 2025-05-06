@props(['size' => null])

@php
    $class = Arr::toCssClasses(['card', "card-$size" => $size]);
@endphp

<div {{ $attributes->class($class) }}>
    {{ $slot }}
</div>
