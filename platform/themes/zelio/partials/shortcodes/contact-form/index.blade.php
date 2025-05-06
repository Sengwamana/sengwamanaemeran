@php
    $style = in_array($shortcode->style, range(1, 3)) ? $shortcode->style : 1;
    $contactInfo = Shortcode::fields()->getTabsData(['label', 'content', 'icon', 'url'], $shortcode);
@endphp

@include(Theme::getThemeNamespace("partials.shortcodes.contact-form.styles.style-$style"))
