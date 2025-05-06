<!DOCTYPE html>
<html {!! Theme::htmlAttributes() !!}>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport" />

        {!! Theme::header() !!}

        <style>
            :root {
                --primary-color: {{ $primaryColor = theme_option('primary_color', '#6e4ef2') }};
                --gradient-color: {{ theme_option('gradient_color', '#8c71ff') }};
                --bs-primary-rgb: {{ implode(', ', BaseHelper::hexToRgb($primaryColor)) }};
            }
        </style>
    </head>

    <body {!! Theme::bodyAttributes() !!}>
        {!! apply_filters(THEME_FRONT_BODY, null) !!}

        {!! Theme::partial('header') !!}

        {!! Theme::partial('preloader') !!}

        @yield('content')

        {!! Theme::partial('footer') !!}

        {!! Theme::footer() !!}
    </body>
</html>
