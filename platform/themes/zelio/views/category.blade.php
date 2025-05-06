@php
    Theme::set('pageTitle', $category->name);
@endphp

@include(Theme::getThemeNamespace('views.loop'))
