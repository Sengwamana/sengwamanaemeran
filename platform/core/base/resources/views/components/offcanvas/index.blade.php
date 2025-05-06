@php
    $id = Str::camel($attributes->get('id', \Illuminate\Support\Str::random(8)));
@endphp

<div {{ $attributes->merge(['class' => 'offcanvas offcanvas-start', 'tabindex' => '-1', 'aria-labelledby' => "{$id}Label"]) }}>
    {{ $slot }}
</div>
