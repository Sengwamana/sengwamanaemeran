@props([
    'type' => 'info',
    'title' => null,
    'description' => null,
    'isActionModal' => 'false',
    'url' => null,
    'method' => null,
    'payload' => null,
    'confirmText' => trans('core/base::base.yes'),
    'cancelText' => trans('core/base::base.no'),
])

<div
    {{ $attributes->merge([
        'data-bb-toggle' => 'modal',
        'data-type' => $type,
        'data-action-modal' => $isActionModal,
        'data-url' => $url,
        'data-method' => $method,
        'data-payload' => json_encode($payload),
        'data-confirm-text' => $confirmText,
        'data-cancel-text' => $cancelText,
    ]) }}
    class="d-inline-block"
>
    <x-core::button
        type="button"
        :color="$type"
    >
        {{ $slot }}
    </x-core::button>

    @if ($title)
        <div class="modal-replace-title d-none">
            {{ $title }}
        </div>
    @endif

    @if ($description)
        <div class="modal-replace-description d-none">
            {{ $description }}
        </div>
    @endif
</div>

<x-core::modal.push-once :type="$type" />
