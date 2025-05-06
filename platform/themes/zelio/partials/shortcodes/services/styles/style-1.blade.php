<section class="section-service-1 pt-120 pb-120">
    <div class="container">
        <div class="row align-items-end">
            @if($shortcode->title || $shortcode->subtitle)
                <div class="col-lg-7 me-auto">
                    @if($shortcode->title)
                        <h3 class="ds-3 mt-3 mb-3 text-primary">{!! BaseHelper::clean($shortcode->title) !!}</h3>
                    @endif
                    @if($shortcode->subtitle)
                        <span class="fs-5 fw-medium text-200">
                            {!! BaseHelper::clean(nl2br($shortcode->subtitle)) !!}
                        </span>
                    @endif
                </div>
            @endif
            @if($shortcode->action_text)
                <div class="col-lg-auto">
                    <a href="{{ $shortcode->action_link }}" class="btn btn-gradient mt-lg-0 mt-5 ms-lg-auto">
                        {{ $shortcode->action_text }}
                        <i class="ri-arrow-right-up-line"></i>
                    </a>
                </div>
            @endif
        </div>
        <div class="row mt-6 justify-content-between">
            @foreach($services as $service)
                <div class="col-12">
                    <div
                        class="single-service-card-1 @if ($shortcode->show_image_on_hover != 'no') tg-img-reveal-item @endif w-100 border-top border-900 p-3"
                        data-fx="1"
                        @if ($service->image && ($shortcode->show_image_on_hover != 'no')) data-img="{{ RvMedia::getImageUrl($service->image) }}" @endif
                    >
                        <div class="service-card-details d-lg-flex align-items-center">
                            <h3 class="service-card-title w-lg-50 w-100 mb-0">
                                <a href="{{ $service->url }}">
                                    <span class="service-number">
                                        {{ Str::padLeft($loop->iteration, 2, 0) }}.
                                    </span>
                                    {{ $service->name }}
                                </a>
                            </h3>
                            <a href="{{ $service->url }}" class="d-md-flex d-block ps-lg-10 align-items-center justify-content-end w-100">
                                @if ($service->description)
                                    <p class="service-card-text my-3">
                                        {!! BaseHelper::clean(nl2br($service->description)) !!}
                                    </p>
                                @endif
                                <div class="service-card-icon icon-shape ms-auto icon-md rounded-circle border">
                                    <i class="ri-arrow-right-up-line"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
