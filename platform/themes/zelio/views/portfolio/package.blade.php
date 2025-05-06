@php
    Theme::set('pageTitle', $package->name);
    Theme::set('headerStyle', theme_option('header_style', 'style-3'));
    Theme::set('headerTopStyle',theme_option('header_top_sidebar_style', 'style-2'));
@endphp

<section class="packages-details-area">
    <div class="packages-details-wrap">
        @if ($image = $package->image)
            <div class="packages-details-thumb">
                {{ RvMedia::image($image, $package->name) }}
            </div>
        @endif
        <div class="packages-details-content">
            @if ($description = $package->description)
                <h2 class="title">{{ $package->description }}</h2>
            @endif

            <div class="ck-content">
                {!! BaseHelper::clean($package->content) !!}
            </div>

            {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $package) !!}
        </div>
    </div>
</section>
