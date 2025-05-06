@php
    $style = in_array($shortcode->style, range(1, 3)) ? $shortcode->style : 1;
@endphp

@include(Theme::getThemeNamespace("partials.shortcodes.resume.styles.style-$style"))
