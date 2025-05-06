@php
    Theme::set('pageTitle', $tag->name);
@endphp

@include(Theme::getThemeNamespace('views.loop'))
