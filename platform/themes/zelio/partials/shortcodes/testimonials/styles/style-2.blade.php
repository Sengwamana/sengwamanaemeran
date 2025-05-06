<div id="portfolio" class="testimonials pt-60 border-bottom pb-80 mb-8">
    @if($shortcode->title)
        <h3>{!! BaseHelper::clean($shortcode->title) !!}</h3>
    @endif
    <div class="position-relative pt-4">
        <div class="swiper slider-one pb-3 position-relative">
            <div class="swiper-wrapper">
                @foreach($testimonials as $testimonial)
                    <div class="swiper-slide">
                        <div class="testimonials-block pe-5">
                            {{ RvMedia::image($testimonial->image, $testimonial->name, 'thumb', attributes: ['class' => 'rounded-circle mb-2']) }}
                            <div class="h6 fs-5 text-dark testimonials-content">{!! BaseHelper::clean($testimonial->content) !!}</div>
                            <div class="information">
                                <p class="fs-6 text-primary-3">
                                    {{ $testimonial->name }}@if($testimonial->company)<span class="text-300">, {{ $testimonial->company }}</span>@endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
