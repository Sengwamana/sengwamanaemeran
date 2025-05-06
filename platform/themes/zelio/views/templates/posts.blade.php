@php
    $style = in_array($shortcode->style, range(1, 3)) ? $shortcode->style : 1;
@endphp

@include(Theme::getThemeNamespace("views.templates.posts-styles.style-$style"))
