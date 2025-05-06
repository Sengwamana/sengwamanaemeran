<section id="portfolio" class="section-experience pt-5">
    <div class="container">
        <div class="rounded-3 border border-1 position-relative overflow-hidden">
            <div class="box-linear-animation position-relative z-1">
                <div class="p-lg-8 p-md-6 p-3 position-relative z-1">
                    @if($shortcode->subtitle)
                        <div class="d-flex align-items-center">
                            <svg class="text-primary-2 me-2" xmlns="http://www.w3.org/2000/svg" width="5" height="6" viewBox="0 0 5 6" fill="none">
                                <circle cx="2.5" cy="3" r="2.5" fill="var(--primary-color)" />
                            </svg>
                            <span class="text-linear-4 d-flex align-items-center">
                                {!! BaseHelper::clean($shortcode->subtitle) !!}
                            </span>
                        </div>
                    @endif
                    @if($shortcode->title)
                        <h3>{!! BaseHelper::clean($shortcode->title) !!}</h3>
                    @endif
                    <div class="row mt-5">
                        @if($experiences)
                            <div class="col-lg-4">
                                <div class="d-flex flex-column gap-2">
                                    @foreach($experiences as $experience)
                                        <div class="technology border border-1 rounded-3 p-3">
                                            <div class="d-flex align-items-center gap-2">
                                                {{ RvMedia::image($experience['logo'], $experience['title']) }}
                                                <div class="d-flex flex-column ms-2">
                                                    <h5 class="mb-1">{{ $experience['title'] }}</h5>
                                                    <span class="text-300">{{ $experience['date'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div @class(['mt-5 ps-lg-5 mt-lg-0', 'col-lg-8' => $experiences, 'col-lg-12' => ! $experiences])>
                            <h6 class="text-linear-4">{!! BaseHelper::clean($shortcode->role_title) !!}</h6>
                            @php
                                $descriptionItems = array_filter(explode("\n", $shortcode->role_description));
                            @endphp
                            @if($descriptionItems)
                                <ul class="mt-4">
                                    @foreach($descriptionItems as $descriptionItem)
                                        <li class="text-dark mb-3">{!! BaseHelper::clean($descriptionItem) !!}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if($skills)
                                <div class="d-flex flex-wrap align-items-center gap-3 mt-7">
                                    @foreach($skills as $skill)
                                        <div class="text-300 border border-1 px-3 py-1">{{ $skill['name'] }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if($shortcode->background_image)
                    {{ RvMedia::image($shortcode->background_image, $shortcode->title, attributes: ['class' => 'position-absolute top-0 start-0 z-0']) }}
                @endif
            </div>
        </div>
    </div>
</section>
