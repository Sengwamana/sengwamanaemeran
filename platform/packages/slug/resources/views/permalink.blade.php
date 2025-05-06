@php
    $prefix = apply_filters(FILTER_SLUG_PREFIX, SlugHelper::getPrefix($model::class), $model);
    $value = $value ?: old('slug');
    $endingURL = SlugHelper::getPublicSingleEndingURL();
    $canBeReviewed = apply_filters('core_slug_can_be_reviewed', Auth::user() && is_in_admin(true), $model);

    $previewURL = str_replace('--slug--', (string) $value, url($prefix) . '/' . config('packages.slug.general.pattern')) . $endingURL . ($canBeReviewed && $preview ? '?preview=true' : '');
@endphp

<div
    class="slug-field-wrapper"
    data-field-name="{{ SlugHelper::getColumnNameToGenerateSlug($model) }}"
>
    @if (in_array(
        Route::currentRouteName(), ['pages.create', 'pages.edit'])
        && BaseHelper::isHomepage(Route::current()->parameter('page.id'))
    )
        <x-core::form.text-input
            :label="trans('core/base::forms.permalink')"
            name="slug"
            :group-flat="true"
            :value="BaseHelper::getHomepageUrl()"
            :readonly="true"
        />
    @else
        <x-core::form.text-input
            :label="trans('core/base::forms.permalink')"
            :required="true"
            name="slug"
            :group-flat="true"
            class="ps-0"
            :value="$value"
        >
            <x-slot:prepend>
                <span class="input-group-text">
                    {{ url($prefix) }}/
                </span>
            </x-slot:prepend>

            <x-slot:append>
                <span class="input-group-text slug-actions">
                    <a
                        href="#"
                        @class(['link-secondary', 'd-none' => ! $value])
                        data-bs-toggle="tooltip"
                        aria-label="{{ trans('packages/slug::slug.generate_url') }}"
                        data-bs-original-title="{{ trans('packages/slug::slug.generate_url') }}"
                        data-bb-toggle="generate-slug"
                    >
                        <x-core::icon name="ti ti-wand" />
                    </a>
                </span>
            </x-slot:append>
        </x-core::form.text-input>
        @if ($canBeReviewed)
            <x-core::form.helper-text class="mt-n2 text-truncate">
                {{ trans('packages/slug::slug.preview') }}: <a href="{{ $previewURL }}" target="_blank">{{ $previewURL }}</a>
            </x-core::form.helper-text>
        @endif
        @if ($editable)
            <input
                class="slug-current"
                name="{{ $name }}"
                type="hidden"
                value="{{ $value }}"
            >
            <div
                class="slug-data"
                data-url="{{ route('slug.create') }}"
                data-view="{{ rtrim(str_replace('--slug--', '', url($prefix) . '/' . config('packages.slug.general.pattern')), '/') . '/' }}"
                data-id="{{ $id ?: 0 }}"
            ></div>
            <input
                name="slug_id"
                type="hidden"
                value="{{ $id ?: 0 }}"
            >
            <input
                name="is_slug_editable"
                type="hidden"
                value="1"
            >
        @endif
    @endif
</div>
