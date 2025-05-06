<link
    href="{{ Language::getLocalizedURL(Language::getDefaultLocale(), url()->current(), [], false) }}"
    hreflang="x-default"
    rel="alternate"
/>

@if (!empty($urls))
    @foreach ($urls as $item)
        <link
            href="{{ $item['url'] }}"
            hreflang="{{ $item['lang_code'] }}"
            rel="alternate"
        />
    @endforeach
@else
    @foreach (Language::getSupportedLocales() as $localeCode => $properties)
        <link
            href="{{ Language::getLocalizedURL($localeCode, url()->current(), [], false) }}"
            hreflang="{{ $localeCode }}"
            rel="alternate"
        />
    @endforeach
@endif
