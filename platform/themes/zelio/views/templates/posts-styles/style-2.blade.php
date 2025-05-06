<section id="blog" class="section-blog-2 position-relative pt-60 pb-60">
    <div class="container">
        @if($shortcode->title || $shortcode->subtitle)
            <div class="text-center">
                <div class="d-flex align-items-center justify-content-center">
                    <svg class="text-primary-2 me-2" xmlns="http://www.w3.org/2000/svg" width="5" height="6" viewBox="0 0 5 6" fill="none">
                        <circle cx="2.5" cy="3" r="2.5" fill="var(--primary-color)" />
                    </svg>
                    @if($shortcode->subtitle)
                        <span class="text-linear-4 d-flex align-items-center">
                        {!! BaseHelper::clean($shortcode->subtitle) !!}
                    </span>
                    @endif
                </div>
                @if($shortcode->title)
                    <h3>{!! BaseHelper::clean($shortcode->title) !!}</h3>
                @endif
            </div>
        @endif
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
