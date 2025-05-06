<section class="section-brands-1 section-padding">
    @if($shortcode->title || $shortcode->subtitle)
        <div class="container">
            <div class="text-center">
                @if($shortcode->title)
                    <h2>{!! BaseHelper::clean($shortcode->title) !!}</h2>
                @endif
                @if($shortcode->subtitle)
                    <p class="text-300">
                        {!! BaseHelper::clean(nl2br($shortcode->subtitle)) !!}
                    </p>
                @endif
            </div>
        </div>
    @endif
    <div class="container-fluid">
        <div class="slick-slider mt-5 position-relative z-1" data-items="8">
            @foreach($tabs as $tab)
                <div class="slick-slider-item">
                    @if($tab['url'])
                        <a href="{{ $tab['url'] }}" @if($tab['open_in_new_tab']) target="_blank" @endif>
                            {{ RvMedia::image($tab['image'], $tab['name']) }}
                        </a>
                    @else
                        {{ RvMedia::image($tab['image'], $tab['name']) }}
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
