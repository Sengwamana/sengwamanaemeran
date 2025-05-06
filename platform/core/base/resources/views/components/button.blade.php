@props([
    'type' => 'button',
    'tag' => 'button',
    'disabled' => false,
    'color' => null,
    'icon' => null,
    'iconOnly' => false,
    'square' => false,
    'pill' => false,
    'iconPosition' => 'left',
    'outlined' => false,
    'size' => null,
    'loading' => false,
    'loadingOverlay' => false,
    'tooltip' => null,
    'tooltipPlacement' => 'top',
    'ghost' => false,
])

@php
    $class = Arr::toCssClasses([
        'btn',
        'disabled' => $disabled,
        'btn-square' => $square,
        'btn-pill' => $pill,
        'btn-icon' => $iconOnly,
        'btn-loading' => $loading && $loadingOverlay,
        ...$outlined ? [$color ? "btn-outline-$color" : null] : [$color ? "btn-$color" : null],
        match ($size) {
            'sm' => 'btn-sm',
            'lg' => 'btn-lg',
            default => null,
        },
        "btn-ghost-$color" => $ghost && $color,
    ]);
    
    $spinnerClasses = Arr::toCssClasses(['spinner-border', 'spinner-border-sm', 'me-2' => $iconPosition === 'left', 'ms-2' => $iconPosition === 'right']);
@endphp

<{{ $tag }}
    {{ $attributes->merge([
            'type' => $type,
            'disabled' => $disabled,
        ])->class($class) }}
    @if ($tooltip) data-bs-toggle="tooltip"
        data-bs-placement="{{ $tooltipPlacement }}"
        title="{{ $tooltip }}" @endif
>
    @if ($icon && $iconPosition === 'left')
        @if ($loading)
            <span
                class="{{ $spinnerClasses }}"
                role="status"
            ></span>
        @else
            <x-core::icon
                :name="$icon"
                :size="$size"
                class="icon-left"
            />
        @endif
    @endif

    @if ($slot->isNotEmpty())
        @if ($iconOnly)
            <span class="sr-only">{{ $slot }}</span>
        @else
            {{ $slot }}
        @endif
    @endif

    @if ($icon && $iconPosition === 'right')
        @if ($loading)
            <span
                class="{{ $spinnerClasses }}"
                role="status"
            ></span>
        @else
            <x-core::icon
                :name="$icon"
                :size="$size"
                class="icon-right"
            />
        @endif
    @endif
    </{{ $tag }}>
