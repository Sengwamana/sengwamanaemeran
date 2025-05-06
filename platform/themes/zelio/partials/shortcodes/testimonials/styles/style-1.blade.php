<section class="section-testimonials-1 position-relative pt-120 pb-120 bg-900 overflow-hidden mb-8">
    <div class="container">
        <div class="row">
            <div @class(['col-lg-8' => $shortcode->shape_image, 'col-lg-12' => ! $shortcode->shape_image])>
                @if($shortcode->title)
                    <h3 class="ds-3 mt-3 mb-3 text-primary">{!! BaseHelper::clean($shortcode->title) !!}</h3>
                @endif
                @if($shortcode->subtitle)
                    <span class="fs-5 fw-medium text-200">
                        {!! BaseHelper::clean(nl2br($shortcode->subtitle)) !!}
                    </span>
                @endif
                <div class="row mt-8">
                    <div class="swiper slider-2 pt-2 pb-3" data-slides-per-view="{{ $shortcode->shape_image ? 2 : 3 }}">
                        <div class="swiper-wrapper">
                            @foreach($testimonials as $testimonial)
                                <div class="swiper-slide">
                                    <div class="bg-white card-testimonial-1 p-lg-7 p-md-5 mx-3 mx-md-0 p-4 border-2 rounded-4 position-relative">
                                        <div class="d-flex mb-5">
                                            <i class="ri-star-fill fs-7 text-primary"></i>
                                            <i class="ri-star-fill fs-7 text-primary"></i>
                                            <i class="ri-star-fill fs-7 text-primary"></i>
                                            <i class="ri-star-fill fs-7 text-primary"></i>
                                            <i class="ri-star-fill fs-7 text-primary"></i>
                                        </div>
                                        <div class="h6 mb-7 testimonials-content">{!! BaseHelper::clean($testimonial->content) !!}</div>
                                        <a href="#" class="d-flex align-items-center">
                                            {{ RvMedia::image($testimonial->image, $testimonial->name, 'thumb', attributes: ['class' => 'icon_65 avatar']) }}
                                            <h6 class="ms-2 mb-0">
                                                {{ $testimonial->name }}
                                                @if($testimonial->company)
                                                    <span class="fs-6 fw-regular"> - {{ $testimonial->company }}</span>
                                                @endif
                                            </h6>
                                        </a>
                                        <div class="position-absolute top-0 end-0 m-5">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 52 52" fill="none">
                                                <g clip-path="url(#clip0_551_13914)">
                                                    <path d="M0 29.7144H11.1428L3.71422 44.5715H14.8571L22.2857 29.7144V7.42871H0V29.7144Z" fill="#D1D5DB" />
                                                    <path d="M29.7148 7.42871V29.7144H40.8577L33.4291 44.5715H44.5719L52.0005 29.7144V7.42871H29.7148Z" fill="#D1D5DB" />
                                                </g>
                                                <defs>
                                                    <clipPath>
                                                        <rect width="52" height="52" fill="white" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="text-center mt-8 position-relative z-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($shortcode->shape_image)
        <div class="shape-1 position-absolute bottom-0 start-50 z-1 ms-10 ps-10 d-none d-md-block">
            {{ RvMedia::image($shortcode->shape_image, $shortcode->title, attributes: ['class' => 'position-relative z-1']) }}
        </div>
    @endif
</section>
