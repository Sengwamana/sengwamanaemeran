@php
    $style = in_array($shortcode->style, [1, 2]) ? $shortcode->style : 1;
@endphp

@include(Theme::getThemeNamespace("partials.shortcodes.testimonials.styles.style-$style"))
