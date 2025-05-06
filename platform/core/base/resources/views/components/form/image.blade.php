@props([
    'name',
    'value',
    'defaultImage' => RvMedia::getDefaultImage(),
    'allowAddFromUrl' => $isInAdmin = is_in_admin(true) && auth()->guard()->check(),
])

@php
    $value = BaseHelper::stringify($value);
    $allowThumb = $attributes->get('allow_thumb', $attributes->get('allow-thumb', true));

    $defaultImage = $attributes->get('preview_image') ?: RvMedia::getDefaultImage();
@endphp

<div {{ $attributes->merge(['class' => "image-box image-box-$name"]) }}>
    <input
        class="image-data"
        name="{{ $name }}"
        type="hidden"
        value="{{ $value }}"
        {{ $attributes->except('action') }}
    />

    @if (! $isInAdmin)
        @php
            $name = str_replace(['[', ']'], ['___', ''], $name);
        @endphp

        <input
            class="media-image-input"
            type="file"
            style="display: none;"
            @if ($name) name="{{ $name }}_input" @endif
            @if (!isset($attributes['action']) || $attributes['action'] == 'select-image') accept="image/*" @endif
            {{ $attributes->except('action') }}
        />
    @endif

    <div
        style="width: 8rem"
        @class([
            'preview-image-wrapper mb-1',
            'preview-image-wrapper-not-allow-thumb' => ! $allowThumb
        ])
    >
        <div class="preview-image-inner">
            <a
                data-bb-toggle="image-picker-choose"
                @if ($isInAdmin) data-target="popup" @else data-target="direct" @endif
                class="image-box-actions"
                data-result="{{ $name }}"
                data-action="{{ $attributes['action'] ?? 'select-image' }}"
                data-allow-thumb="{{ $allowThumb == true }}"
                href="#"
            >
                <x-core::image
                    @class(['preview-image', 'default-image' => !$value])
                    data-default="{{ $defaultImage = $defaultImage ?: RvMedia::getDefaultImage() }}"
                    src="{{ RvMedia::getImageUrl($value, $allowThumb ? 'thumb' : null, false, $defaultImage) }}"
                    alt="{{ trans('core/base::base.preview_image') }}"
                />
                <span class="image-picker-backdrop"></span>
            </a>
            <x-core::button
                @style(['display: none' => empty($value), '--bb-btn-font-size: 0.5rem'])
                class="image-picker-remove-button p-0"
                :pill="true"
                data-bb-toggle="image-picker-remove"
                size="sm"
                icon="ti ti-x"
                :icon-only="true"
                :tooltip="trans('core/base::forms.remove_image')"
            />
        </div>
    </div>

    <a
        data-bb-toggle="image-picker-choose"
        @if ($isInAdmin) data-target="popup" @else data-target="direct" @endif
        data-result="{{ $name }}"
        data-action="{{ $attributes['action'] ?? 'select-image' }}"
        data-allow-thumb="{{ $allowThumb == true }}"
        href="#"
    >
        {{ trans('core/base::forms.choose_image') }}
    </a>

    @if($allowAddFromUrl)
        <div data-bb-toggle="upload-from-url">
            <span class="text-muted">{{ trans('core/media::media.or') }}</span>
            <a
                href="javascript:void(0)"
                class="mt-1"
                data-bs-toggle="modal"
                data-bs-target="#image-picker-add-from-url"
                data-bb-target=".image-box-{{ $name }}"
            >
                {{ trans('core/media::media.add_from_url') }}
            </a>
        </div>
    @endif
</div>
