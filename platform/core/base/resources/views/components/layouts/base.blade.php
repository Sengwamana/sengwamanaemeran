<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    >
    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge"
    >
    <title>{{ PageTitle::getTitle() }}</title>
    @if ($csrfToken = csrf_token())
        <meta
            name="csrf-token"
            content="{{ $csrfToken }}"
        >
    @endif

    @if (setting('admin_favicon') || config('core.base.general.favicon'))
        <link
            href="{{ $favicon = setting('admin_favicon') ? RvMedia::getImageUrl(setting('admin_favicon')) : url(config('core.base.general.favicon')) }}"
            rel="icon shortcut"
        >
        <meta
            property="og:image"
            content="{{ $favicon }}"
        >
    @endif

    <meta
        name="description"
        content="{{ $copyright = strip_tags(trans('core/base::layouts.copyright', ['year' => Carbon\Carbon::now()->year, 'company' => setting('admin_title', config('core.base.general.base_name')), 'version' => get_cms_version()])) }}"
    >
    <meta
        property="og:description"
        content="{{ $copyright }}"
    >

    @include('core/base::components.layouts.header')

    @yield('head')

    <script>
        window.siteUrl = "{{ url('') }}";

        @if (Auth::check())
            window.siteEditorLocale = "{{ apply_filters('cms_site_editor_locale', App::getLocale()) }}";
            window.siteAuthorizedUrl = "{{ rescue(fn() => route('settings.license.verify.index')) }}";
            window.isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};
        @endif
    </script>

    {{ $header ?? null }}

    @stack('header')

    {!! AdminAppearance::getCustomCSS() !!}

    {!! AdminAppearance::getCustomJs('header') !!}

    {!! apply_filters(BASE_FILTER_HEAD_LAYOUT_TEMPLATE, null) !!}
</head>

<body
    class="@yield('body-class', $bodyClass ?? 'page-sidebar-closed-hide-logo page-content-white page-container-bg-solid') {{ session()->get('sidebar-menu-toggle') ? 'page-sidebar-closed' : '' }}"
    style="@yield('body-style', $bodyStyle ?? null)"
    @if (BaseHelper::adminLanguageDirection() === 'rtl') dir="rtl" @endif
    {!! Html::attributes($bodyAttributes ?? []) !!}
    @if(AdminHelper::themeMode() === 'dark')
        data-bs-theme="dark"
    @endif
>
    {!! AdminAppearance::getCustomJs('body') !!}

    {{ $headerLayout ?? null }}

    {!! apply_filters(BASE_FILTER_HEADER_LAYOUT_TEMPLATE, null) !!}

    <div id="app">
        {{ $slot }}
    </div>

    @include('core/base::elements.common')

    {!! Assets::renderFooter() !!}

    @yield('javascript')

    <div id="stack-footer">
        {{ $footer ?? null }}
        @stack('footer')
    </div>

    {!! AdminAppearance::getCustomJs('footer') !!}

    {!! apply_filters(BASE_FILTER_FOOTER_LAYOUT_TEMPLATE, null) !!}
</body>

</html>
