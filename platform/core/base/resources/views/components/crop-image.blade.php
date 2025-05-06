@props(['label',
    'name',
    'action',
    'deleteAction' => null,
    'canDelete' => false,
    'value' => null,
    'size' => '2xl',
    'rounded' => null,
    'showLabel' => true,
    'showChooseImageLink' => true,
    'hiddenCropper' => false,
])

@php
    Assets::addStyles('cropper')
        ->addScripts('cropper')
        ->addStylesDirectly('vendor/core/core/base/css/crop-image.css')
        ->addScriptsDirectly('vendor/core/core/base/js/crop-image.js');

    $imageClasses = Arr::toCssClasses([
        'image-preview crop-image-original',
        "avatar avatar-$size",
        "rounded-$rounded" => $rounded,
    ]);
@endphp

<div class="crop-image-container">
    <x-core::form-group>
        @if($showLabel)
            <x-core::form.label>{{ $label }}</x-core::form.label>
        @endif
        <div class="avatar-view rounded-{{ $rounded }} overflow-hidden">
            <img {{ $attributes->merge(['class' => $imageClasses, 'src' => $value, 'alt' => $label]) }} />

            @if(!$hiddenCropper || $canDelete)
                <div class="backdrop"></div>
                <div class="action">
                    @if(!$hiddenCropper)
                        <a
                            href="javascript:void(0);"
                            class="text-decoration-none text-white"
                            data-bs-toggle="modal"
                            data-bs-target="#{{ $name }}-modal"
                        >
                            <x-core::icon name="ti ti-edit" />
                        </a>
                    @endif

                    @if($canDelete)
                        <a
                            data-bb-toggle="delete-avatar"
                            href="{{ $deleteAction }}"
                            class="text-decoration-none text-white"
                        >
                            <x-core::icon name="ti ti-trash" />
                        </a>
                    @endif
                </div>
            @endif
        </div>

        @if($showChooseImageLink && !$hiddenCropper)
            <a
                href="javascript:void(0);"
                data-bs-toggle="modal"
                data-bs-target="#{{ $name }}-modal"
                class="d-block mt-1"
            >
                {{ trans('core/base::forms.choose_image') }}
            </a>
        @endif
    </x-core::form-group>

    @if(!$hiddenCropper)
        <x-core::modal
            id="{{ $name }}-modal"
            :title="__('Update :name', ['name' => $label])"
            size="lg"
            class="crop-image-modal"
        >
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ $action }}">
                        <x-core::form.text-input
                            :label="$label"
                            :name="$name"
                            type="file"
                            accept="image/*"
                        />
                    </form>

                    <div class="cropper-image-wrap">
                        <img src="" class="cropper-image" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="img-preview preview-lg"></div>
                    <div class="img-preview preview-md"></div>
                    <div class="img-preview preview-sm"></div>
                </div>
            </div>

            <x-slot:footer>
                <x-core::button data-bs-dismiss="modal" type="button">
                    {{ trans('core/base::base.close') }}
                </x-core::button>
                <x-core::button type="submit" color="primary" class="ms-auto">
                    {{ trans('core/base::forms.save_and_continue') }}
                </x-core::button>
            </x-slot:footer>
        </x-core::modal>
    @endif
</div>
