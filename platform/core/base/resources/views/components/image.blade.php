@props(['src', 'alt' => trans('core/base::base.preview_image')])

<img
    {{ $attributes }}
    src="{{ $src }}"
    alt="{{ $alt }}"
/>
