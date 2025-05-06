@props([
    'id' => null,
    'type' => 'info',
    'title' => null,
    'description' => null,
    'icon' => null,
    'formAction' => null,
    'formMethod' => 'POST',
    'formAttrs' => [],
    'hasForm' => false,
])

@php
    $icon ??= match ($type) {
        'success' => 'ti ti-circle-check',
        'danger' => 'ti ti-alert-triangle',
        'warning' => 'ti ti-alert-circle',
        default => 'ti ti-info-circle',
    };
@endphp

<x-core::modal
    :type="$type"
    :id="$id"
    {{ $attributes->merge(['size' => 'sm', 'closeButton' => false]) }}
    :body-attrs="['class' => 'text-center py-4']"
    :form-action="$formAction"
    :form-method="$formMethod"
    :form-attrs="$formAttrs"
    :has-form="$hasForm"
>
    <x-core::modal.close-button />

    <div class="mb-2">
        <x-core::icon
            :name="$icon"
            class="text-{{ $type }}"
            size="lg"
        />
    </div>

    @if ($title)
        <h3>{!! $title !!}</h3>
    @endif

    {{ $slot }}

    <x-slot:footer>{{ $footer ?? null }}</x-slot:footer>
</x-core::modal>
