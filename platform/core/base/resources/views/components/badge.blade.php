@props([
    'label' => null,
    'color' => 'primary',
    'lite' => false,
    'outline' => false,
    'icon' => null,
])

@php
    $classes = Arr::toCssClasses([
        'badge',
        "bg-$color text-$color-fg" => !$lite && !$outline,
        "bg-$color-lt" => $lite,
        "badge-outline text-$color" => $outline,
        'd-inline-flex align-items-center gap-1' => $icon,
    ]);
@endphp

<span {{ $attributes->class($classes) }}>
    @if($icon)
        <x-core::icon :name="$icon" />
    @endif

    {{ $label ?? $slot }}
</span>
