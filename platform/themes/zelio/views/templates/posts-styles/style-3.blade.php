<div id="blog" class="blog pt-70">
    @if($shortcode->title)
        <h3>{!! BaseHelper::clean($shortcode->title) !!}</h3>
    @endif
    <div class="position-relative pt-4">
        <div class="swiper slider-two pb-6 position-relative">
            <div class="swiper-wrapper">
                @foreach($posts->chunk(2) as $row)
                    <div class="swiper-slide">
                        @foreach($row as $post)
                            @include(Theme::getThemeNamespace('views.templates.post-item.index'))
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>
