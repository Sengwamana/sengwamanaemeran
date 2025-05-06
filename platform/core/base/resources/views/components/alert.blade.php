@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
    'icon' => null,
    'important' => false,
])

@php
    $color = match ($type) {
        'success' => 'alert-success',
        'warning' => 'alert-warning',
        'danger' => 'alert-danger',
        default => 'alert-info',
    };

    $icon ??= match ($type) {
        'success' => 'ti ti-circle-check',
        'danger' => 'ti ti-alert-triangle',
        'warning' => 'ti ti-alert-circle',
        default => 'ti ti-info-circle',
    };
@endphp

<div
    role="alert"
    {{ $attributes->class(['alert', $color, 'alert-dismissible' => $dismissible, 'alert-important' => $important]) }}
>
    @if ($icon)
        <div class="d-flex">
            <div>
                <x-core::icon :name="$icon" class="alert-icon" />
            </div>
            <div class="w-100">
    @endif

    @if ($title)
        <h4 @class(['alert-title' => !$important, 'mb-0'])>{!! $title !!}</h4>
    @endif

    {{ $slot }}

    @if ($icon)
        </div>
    </div>
@endif

@if ($dismissible)
    <a
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="close"
    ></a>
@endif

{{ $additional ?? '' }}
</div>
