@props([
    'label' => null,
    'icon' => null,
    'href' => null,
    'active' => false,
    'iconPlacement' => 'left',
    'iconClass' => null,
])

@php
    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }} {{ $attributes->merge(['href' => $href])->class(['dropdown-item', 'active' => $active]) }}>
    @if ($icon && $iconPlacement === 'left')
        <x-core::icon
            :name="$icon"
            @class(['dropdown-item-icon', $iconClass])
        />
    @endif

    {{ $label ?? $slot }}

    @if ($icon && $iconPlacement === 'right')
        <x-core::icon
            :name="$icon"
            @class(['dropdown-item-icon', $iconClass])
        />
    @endif
    </{{ $tag }}>
