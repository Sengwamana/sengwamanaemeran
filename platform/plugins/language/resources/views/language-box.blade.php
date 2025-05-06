<div
    id="select-post-language"
    class="gap-2 mb-4 d-flex align-items-center"
>
    {!! language_flag($currentLanguage->lang_flag, $currentLanguage->lang_name, 24) !!}

    <select
        name="language"
        id="post_lang_choice"
        class="form-select"
    >
        @foreach ($languages as $language)
            @if (!array_key_exists(json_encode([$language->lang_code]), $related))
                <option
                    value="{{ $language->lang_code }}"
                    @if ($language->lang_code == $currentLanguage->lang_code) selected="selected" @endif
                    data-flag="{{ $language->lang_flag }}"
                >{{ $language->lang_name }}</option>
            @endif
        @endforeach
    </select>
</div>

@if (count($languages) > 1)
    <div>
        <h4>{{ trans('plugins/language::language.translations') }}</h4>
        <div id="list-others-language">
            @foreach ($languages as $language)
                @continue($language->lang_code === $currentLanguage->lang_code)

                @if (array_key_exists($language->lang_code, $related))
                    <a
                        href="{{ Route::has($route['edit']) ? route($route['edit'], $related[$language->lang_code]) : '#' }}"
                        class="gap-2 d-flex align-items-center text-decoration-none"
                    >
                        {!! language_flag($language->lang_flag, $language->lang_name) !!}
                        {{ $language->lang_name }} <x-core::icon name="ti ti-edit" />
                    </a>
                @else
                    <a
                        href="{{ Route::has($route['create']) ? route($route['create']) : '#' }}?{{ http_build_query(array_merge($queryParams, [Language::refLangKey() => $language->lang_code])) }}"
                        class="gap-2 d-flex align-items-center text-decoration-none"
                    >
                        {!! language_flag($language->lang_flag, $language->lang_name) !!}
                        {{ $language->lang_name }} <x-core::icon name="ti ti-plus" />
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <input
        type="hidden"
        id="lang_meta_created_from"
        name="ref_from"
        value="{{ Language::getRefFrom() }}"
    >
    <input
        type="hidden"
        id="reference_id"
        value="{{ $queryParams['ref_from'] }}"
    >
    <input
        type="hidden"
        id="reference_type"
        value="{{ $args[1] }}"
    >
    <input
        type="hidden"
        id="route_create"
        value="{{ Route::has($route['create']) ? route($route['create']) : '#' }}"
    >
    <input
        type="hidden"
        id="route_edit"
        value="{{ Route::has($route['edit']) ? route($route['edit'], $queryParams['ref_from']) : '#' }}"
    >
    <input
        type="hidden"
        id="language_flag_path"
        value="{{ BASE_LANGUAGE_FLAG_PATH }}"
    >

    <div data-change-language-route="{{ route('languages.change.item.language') }}"></div>

    <x-core::modal.action
        id="confirm-change-language-modal"
        type="warning"
        :title="trans('plugins/language::language.confirm-change-language')"
        :description="BaseHelper::clean(trans('plugins/language::language.confirm-change-language-message'))"
        :submit-button-attrs="['id' => 'confirm-change-language-button']"
        :submit-button-label="trans('plugins/language::language.confirm-change-language-btn')"
    />
@endif

@push('header')
    <meta
        name="{{ Language::refFromKey() }}"
        content="{{ $queryParams['ref_from'] }}"
    >
    <meta
        name="{{ Language::refLangKey() }}"
        content="{{ $currentLanguage->lang_code }}"
    >
@endpush
