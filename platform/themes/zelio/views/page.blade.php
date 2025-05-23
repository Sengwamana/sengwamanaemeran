@php
    Theme::set('pageTitle', $page->name);
@endphp

{!! apply_filters(
    PAGE_FILTER_FRONT_PAGE_CONTENT,
    Html::tag('div', BaseHelper::clean($page->content), ['class' => 'ck-content']),
    $page
) !!}
