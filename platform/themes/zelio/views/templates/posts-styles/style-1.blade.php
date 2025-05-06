<section class="section-blog-1 position-relative pt-60 pb-60">
    <div class="container">
        <div class="row align-items-end">
            @if($shortcode->title || $shortcode->subtitle)
                <div class="col-lg-7 me-auto">
                    @if($shortcode->title)
                        <h3 class="ds-3 mt-3 mb-3 text-primary">{!! BaseHelper::clean($shortcode->title) !!}</h3>
                    @endif
                    @if($shortcode->subtitle)
                        <span class="fs-5 fw-medium text-200">{!! BaseHelper::clean($shortcode->subtitle) !!}</span>
                    @endif
                </div>
            @endif
            @if($shortcode->action_text)
                <div class="col-lg-auto">
                    <a href="{{ $shortcode->action_link }}" class="btn btn-gradient mt-lg-0 mt-5 ms-lg-auto">
                        {!! BaseHelper::clean($shortcode->action_text) !!}
                        <i class="ri-arrow-right-up-line"></i>
                    </a>
                </div>
            @endif
        </div>
        @php
            $itemsPerRow = $shortcode->number_posts_per_row ?: theme_option('post_item_per_row', 3);
        @endphp

        <div @class(['row mt-8', "row-cols-1 row-cols-md-{$itemsPerRow}"])>
            @foreach($posts as $post)
                @include(Theme::getThemeNamespace('views.templates.post-item.index'))
            @endforeach
        </div>
    </div>
</section>
