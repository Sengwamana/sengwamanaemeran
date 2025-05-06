@props(['name', 'allowThumb' => true, 'images' => [], 'addImagesLabel' => trans('core/base::forms.add_images'), 'resetLabel' => trans('core/base::forms.reset')])

<div {{ $attributes->merge(['class' => 'gallery-images-wrapper list-images form-fieldset']) }}>
    <div class="images-wrapper mb-2">
        <div
            data-bb-toggle="gallery-add"
            @class([
                'text-center cursor-pointer default-placeholder-gallery-image',
                'hidden' => !empty($images),
            ])
            data-name="{{ $name }}"
        >
            <div class="mb-3">
                <x-core::icon
                    name="ti ti-photo-plus"
                    size="md"
                    class="text-secondary"
                />
            </div>
            <p class="mb-0 text-body">
                {{ trans('core/base::base.click_here') }}
                {{ trans('core/base::base.to_add_more_image') }}.
            </p>
        </div>
        <input
            name="{{ $name }}"
            type="hidden"
        >
        <div
            class="row w-100 list-gallery-media-images @if (empty($images)) hidden @endif"
            data-name="{{ $name }}"
            data-allow-thumb="{{ $allowThumb }}"
        >
            @if (!empty($images))
                @foreach ($images as $image)
                    @if (!empty($image))
                        <div class="col-lg-2 col-md-3 col-4 gallery-image-item-handler mb-2">
                            <div class="custom-image-box image-box">
                                <input
                                    class="image-data"
                                    name="{{ $name }}"
                                    type="hidden"
                                    value="{{ $image }}"
                                >
                                <div @class([
                                    'preview-image-wrapper w-100 mb-1',
                                    'preview-image-wrapper-not-allow-thumb' => !($allowThumb = Arr::get(
                                        $attributes,
                                        'allow_thumb',
                                        true)),
                                ])>
                                    <div class="preview-image-inner">
                                        <x-core::image
                                            class="preview-image"
                                            data-default="{{ $defaultImage = RvMedia::getDefaultImage() }}"
                                            src="{{ RvMedia::getImageUrl($image, $allowThumb ? 'thumb' : null, false, $defaultImage) }}"
                                        />
                                        <div class="image-picker-backdrop"></div>

                                        <span class="image-picker-remove-button">
                                            <x-core::button
                                                class="p-0"
                                                @style(['display: none' => empty($image)])
                                                :pill="true"
                                                data-bb-toggle="image-picker-remove"
                                                size="sm"
                                                icon="ti ti-x"
                                                :icon-only="true"
                                            >
                                                {{ __('Remove image') }}
                                            </x-core::button>
                                        </span>
                                        <div
                                            data-bb-toggle="image-picker-edit"
                                            class="image-box-actions cursor-pointer"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
    <div
        @style(['display: none' => empty($image)])
        class="footer-action"
    >
        <a
            data-bb-toggle="gallery-add"
            href="#"
            class="me-2 cursor-pointer"
        >{{ $addImagesLabel }}</a>
        <a
            href="#"
            class="text-danger"
            data-bb-toggle="gallery-reset"
        >
            {{ $resetLabel }}
        </a>
    </div>
</div>
