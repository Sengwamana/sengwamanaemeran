@props(['defaultClass' => 'mb-3 position-relative'])

<div {{ $attributes->merge(['class' => $defaultClass]) }}>
    {{ $slot }}
</div>
