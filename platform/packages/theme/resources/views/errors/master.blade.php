<!DOCTYPE html>
<html {!! Theme::htmlAttributes() !!}>
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
    <title>@yield('title')</title>

    {!! BaseHelper::googleFonts('https://fonts.googleapis.com/' . sprintf(
        'css2?family=%s:wght@300;400;500;600;700&display=swap',
            urlencode(setting('admin_primary_font', 'Inter')),
    )) !!}

    <style>
        :root {
            --primary-font: "{{ setting('admin_primary_font', 'Inter') }}";
            --primary-color: {{ $primaryColor = setting('admin_primary_color', '#206bc4') }};
            --primary-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($primaryColor)) }};
            --secondary-color: {{ $secondaryColor = setting('admin_secondary_color', '#6c7a91') }};
            --secondary-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($secondaryColor)) }};
            --heading-color: {{ setting('admin_heading_color', 'inherit') }};
            --text-color: {{ $textColor = setting('admin_text_color', '#182433') }};
            --text-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($textColor)) }};
            --link-color: {{ $linkColor = setting('admin_link_color', '#206bc4') }};
            --link-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($linkColor)) }};
            --link-hover-color: {{ $linkHoverColor = setting('admin_link_hover_color', '#206bc4') }};
            --link-hover-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($linkHoverColor)) }};
        }
    </style>

    {!! Assets::styleToHtml('core') !!}
</head>

<body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container py-4 container-tight">
            @yield('content')
        </div>
    </div>
</body>
</html>
