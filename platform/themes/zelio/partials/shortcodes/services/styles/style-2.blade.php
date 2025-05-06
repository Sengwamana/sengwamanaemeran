<section id="services" class="section-service-2 pt-5">
    <div class="container">
        <div class="rounded-3 border border-1 position-relative overflow-hidden">
            <div class="box-linear-animation position-relative z-1 p-lg-5 p-1 p-md-4">
                <div class="position-relative z-1">
                    @if($shortcode->title || $shortcode->subtitle)
                        <div class="text-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <svg class="text-primary-2 me-2" xmlns="http://www.w3.org/2000/svg" width="5" height="6" viewBox="0 0 5 6" fill="none">
                                    <circle cx="2.5" cy="3" r="2.5" fill="var(--primary-color)"></circle>
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
                    <div class="container mt-5">
                        <div class="row g-4">
                            @foreach($services as $service)
                                <div class="col-lg-4 col-md-6">
                                    <div class="card-servies-2 rounded-2 h-100 hover-up">
                                        @if($icon = $service->getMetaData('icon', true))
                                            <x-core::icon :name="$icon" />
                                        @endif
                                        <h6 class="my-3 fw-medium">{{ $service->name }}</h6>
                                        @if($service->description)
                                            <p class="fs-7 text-300 fw-regular">{!! BaseHelper::clean(nl2br($service->description)) !!}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($shortcode->bottom_text)
                            <div class="text-center pt-60">
                                <p class="text-300">
                                    {!! BaseHelper::clean($shortcode->bottom_text) !!}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
