@php
    $style = theme_option('header_style', 1);
    $style = in_array($style, range(1, 3)) ? $style : 1;
@endphp

{!! Theme::partial("header/style-$style") !!}
