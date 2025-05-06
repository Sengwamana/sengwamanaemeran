<section class="section-service-list">
    <div class="row">
        <div class="col-lg-8 mx-lg-auto">
            <div class="card-scroll mt-8">
                <div class="cards">
                    @foreach($services as $service)
                        <div class="card-custom" data-index="0">
                            <div class="card__inner bg-6 px-md-5 py-md-6 px-3 py-4">
                                <div class="card__title d-flex align-items-center mb-md-4 mb-3">
                                    <a href="{{ $service->url }}" class="card_title_link">
                                        <h3 class="fw-semibold mb-2">{{ $service->name }}</h3>
                                        <p class="mb-0">{!! BaseHelper::clean($service->description) !!}</p>
                                    </a>
                                    <a href="{{ $service->url }}" class="card-icon border text-dark border-dark icon-shape ms-auto icon-md rounded-circle">
                                        <i class="ri-arrow-right-up-line"></i>
                                    </a>
                                </div>
                                @if ($service->image)
                                    <div class="card__image-container zoom-img position-relative">
                                        {{ RvMedia::image($service->image, $service->name, attributes: ['class' => 'card_image']) }}
                                        <a href="{{ $service->url }}" class="card-image-overlay position-absolute start-0 end-0 w-100 h-100"></a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>
