@php
    $style = theme_option('post_item_style', 1);
    $style = in_array($style, range(1, 3)) ? $style : 1;
@endphp

@include(Theme::getThemeNamespace("views.templates.post-item.style-$style"))
