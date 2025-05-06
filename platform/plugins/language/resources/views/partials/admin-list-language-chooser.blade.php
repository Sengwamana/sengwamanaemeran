@php
    $languages ??= Language::getActiveLanguage();
@endphp

<div class="text-end d-flex gap-2 justify-content-start justify-content-lg-end align-items-center">
    <h4 class="mb-0">{{ trans('plugins/language::language.translations') }}:</h4>
    @if (count($languages) <= 3)
        <div class="d-flex gap-3 align-items-center">
            @foreach ($languages as $language)
                @continue($language->lang_code === Language::getCurrentAdminLocaleCode())

                <a
                    href="{{ route(Route::currentRouteName(), array_merge($params ?? [], $language->lang_code === Language::getDefaultLocaleCode() ? [] : [Language::refLangKey() => $language->lang_code])) }}"
                    class="text-decoration-none small"
                >
                    {!! language_flag($language->lang_flag, $language->lang_name) !!}
                    {{ $language->lang_name }}
                </a>
            @endforeach
        </div>
    @else
        <x-core::dropdown>
            <x-slot:trigger>
                <a
                    class="d-flex align-items-center gap-2 dropdown-toggle text-muted text-decoration-none"
                    href="#"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    {!! language_flag(
                        Arr::get(Language::getCurrentAdminLanguage(), 'lang_flag'),
                        Arr::get(Language::getCurrentAdminLanguage(), 'lang_name'),
                    ) !!}
                    {{ Arr::get(Language::getCurrentAdminLanguage(), 'lang_name') }}
                </a>
            </x-slot:trigger>

            @foreach ($languages as $language)
                @continue($language->lang_code === Language::getCurrentAdminLocaleCode())

                <x-core::dropdown.item
                    :href="route(
                        Route::currentRouteName(),
                        array_merge(
                            $params ?? [],
                            $language->lang_code === Language::getDefaultLocaleCode()
                                ? []
                                : [Language::refLangKey() => $language->lang_code],
                        ),
                    )"
                    class="d-flex gap-2 align-items-center"
                >
                    {!! language_flag($language->lang_flag, $language->lang_name) !!}
                    {{ $language->lang_name }}
                </x-core::dropdown.item>
            @endforeach
        </x-core::dropdown>
    @endif
    <input
        name="{{ Language::refLangKey() }}"
        type="hidden"
        value="{{ Language::getRefLang() }}"
    >
</div>
