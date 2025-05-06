<input
    name="language"
    type="hidden"
    value="{{ $currentLanguage?->lang_code }}"
>
<div id="list-others-language">
    @foreach ($languages as $language)
        @continue(!$currentLanguage || $language->lang_code === $currentLanguage->lang_code)
        <a
            class="gap-2 d-flex align-items-center text-decoration-none"
            href="{{ Route::has($route['edit']) ? Request::url() . ($language->lang_code != Language::getDefaultLocaleCode() ? '?' . Language::refLangKey() . '=' . $language->lang_code : null) : '#' }}"
            target="_blank"
        >
            {!! language_flag($language->lang_flag, $language->lang_name) !!}
            <span>{{ $language->lang_name }} <x-core::icon name="ti ti-external-link" /></span>
        </a>
    @endforeach
</div>

@push('header')
    <meta
        name="{{ Language::refFromKey() }}"
        content="{{ !empty($args[0]) && $args[0]->id ? $args[0]->id : 0 }}"
    >
    <meta
        name="{{ Language::refLangKey() }}"
        content="{{ $currentLanguage?->lang_code }}"
    >
@endpush
