@php
    $footerStyle = get_footer_style();
@endphp

@if($footerStyle != 3)
    <a class="d-flex main-logo align-items-center d-inline-flex" href="{{ BaseHelper::getHomepageUrl() }}">
        {{ Theme::getLogoImage(logoKey: 'logo_dark', maxHeight: 40) }}
        @if($siteName = theme_option('site_name'))
            <span class="fs-4 ms-2 site-name-text text-white-keep">{{ $siteName }}</span>
        @endif
    </a>
@else
    <a class="d-flex main-logo align-items-center justify-content-center ms-lg-0 ms-md-5 ms-3" href="{{ BaseHelper::getHomepageUrl() }}">
        @if($siteName = theme_option('site_name'))
            <h1 class="fs-28 mb-0 me-2">{{ $siteName }}</h1>
        @endif
        {{ Theme::getLogoImage(logoKey: 'logo_dark', maxHeight: 32) }}
    </a>
@endif

