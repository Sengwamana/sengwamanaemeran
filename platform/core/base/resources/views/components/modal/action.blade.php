@props([
    'id' => null,
    'type' => 'info',
    'title' => null,
    'description' => null,
    'icon' => null,
    'submitButtonLabel' => 'Submit',
    'submitButtonColor' => null,
    'submitButtonAttrs' => [],
    'closeButtonLabel' => trans('core/base::base.close'),
    'closeButtonColor' => null,
    'formAction' => null,
    'formMethod' => 'POST',
    'formAttrs' => [],
    'hasForm' => false,
])

@php
    $submitButtonColor ??= $type;
@endphp

<x-core::modal.alert
    :id="$id"
    :type="$type"
    :title="$title"
    :icon="$icon"
    {{ $attributes->merge(['size' => 'sm', 'closeButton' => false]) }}
    :body-attrs="['class' => 'text-center py-4']"
    :form-action="$formAction"
    :form-method="$formMethod"
    :form-attrs="$formAttrs"
    :has-form="$hasForm"
>
    @if (!empty($description))
        <div class="text-muted text-break">
            {!! $description !!}
        </div>
    @else
        {{ $slot }}
    @endif

    <x-slot:footer>
        <div class="w-100">
            <div class="row">
                @if (!isset($footer))
                    <div class="col">
                        <button
                            type="button"
                            @class([
                                'w-100 btn',
                                "btn-$submitButtonColor",
                                Arr::get($submitButtonAttrs, 'class'),
                            ])
                            {!! Html::attributes(Arr::except($submitButtonAttrs, 'class')) !!}
                        >
                            {{ $submitButtonLabel }}
                        </button>
                    </div>
                    <div class="col">
                        <button
                            type="button"
                            @class(['w-100 btn', "btn-$closeButtonColor" => !$closeButtonColor])
                            data-bs-dismiss="modal"
                        >
                            {{ $closeButtonLabel }}
                        </button>
                    </div>
                @else
                    {{ $footer }}
                @endif
            </div>
        </div>
    </x-slot:footer>
</x-core::modal.alert>
