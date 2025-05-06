@props([
    'class',
])

<span {{ $attributes->merge(['class' => 'badge badge-sm bg-primary text-primary-fg badge-pill menu-item-count ' . $class]) }} data-url="{{ route('menu-items-count') }}" style="display: none"></span>
