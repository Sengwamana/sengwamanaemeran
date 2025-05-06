<section class="section-coporation">
    <div class="container pt-5">
        <div class="row">
            <div @class(['col-lg-8' => ! empty($journeys), 'col-lg-12' => empty($journeys)])>
                <div class="rounded-3 border border-1 position-relative overflow-hidden">
                    <div class="box-linear-animation">
                        <div class="p-lg-8 p-md-6 p-3">
                            @if($shortcode->subtitle)
                                <div class="d-flex align-items-center">
                                    <svg class="text-primary-2 me-2" xmlns="http://www.w3.org/2000/svg" width="5" height="6" viewBox="0 0 5 6" fill="none">
                                        <circle cx="2.5" cy="3" r="2.5" fill="var(--primary-color)" />
                                    </svg>
                                    <span class="text-linear-4 d-flex align-items-center">{!! BaseHelper::clean($shortcode->subtitle) !!}</span>
                                </div>
                            @endif
                            @if($shortcode->title)
                                <h3 class="fw-medium">
                                    {!! BaseHelper::clean($shortcode->title) !!}
                                </h3>
                            @endif
                            @if(! empty($companies))
                                <div class="my-5 border border-1 rounded-2 p-3">
                                    @foreach(collect($companies)->split(2) as $row)
                                        <div @class(['carouselTicker position-relative z-1', 'carouselTicker-left' => $loop->first, 'carouselTicker-right' => ! $loop->first])>
                                            <ul class="carouselTicker__list m-0">
                                                @foreach($row as $company)
                                                    <li @class(['list-inline-item' => $loop->parent->first && $loop->last, 'carouselTicker__item' => ! $loop->parent->first || ! $loop->last])>
                                                        {{ RvMedia::image($company['image'], $company['name']) }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if(! empty($contacts) || $shortcode->contact_avatar)
                                <div class="d-flex flex-column flex-md-row align-items-center gap-3">
                                    @if($shortcode->contact_avatar)
                                        <div class="circle-1 position-relative z-0">
                                            <div class="circle-2 position-absolute top-50 start-50 translate-middle z-1">
                                                <div class="position-absolute top-50 start-50 translate-middle z-2">
                                                    {{ RvMedia::image($shortcode->contact_avatar, $shortcode->title, attributes: ['class' => 'w-100 h-100 rounded-circle']) }}
                                                    <svg class="text-primary-2 position-absolute bottom-0 end-0" xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 5 6" fill="none">
                                                        <circle cx="2.5" cy="3" r="2.5" fill="var(--primary-color)" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(! empty($contacts))
                                        <div class="d-flex flex-column gap-2">
                                            @foreach($contacts as $contact)
                                                <a href="{{ $contact['url'] }}">
                                                    <x-core::icon :name="$contact['icon']" />
                                                    <span class="text-300">[{{ $contact['label'] }}] <span class="text-secondary-2">{{ $contact['content'] }}</span></span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="position-absolute d-none d-md-block decorate">
                            <div class="rotateme">
                                <div class="circle-1-1"></div>
                                <div class="circle-1-2 position-absolute top-50 start-50 translate-middle">
                                    <svg class="mb-5 position-absolute bottom-0 start-0" xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none">
                                        <circle cx="4.5" cy="4.5" r="4.5" fill="#636366" />
                                    </svg>
                                </div>
                                <div class="circle-1-3 position-absolute top-50 start-50 translate-middle">
                                    <svg class="mb-3 position-absolute bottom-0 end-0" xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none">
                                        <circle cx="4.5" cy="4.5" r="4.5" fill="#636366" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(! empty($journeys))
                <div class="col-lg-4 pt-lg-0 pt-5">
                    <div class="bg-3 rounded-3 border border-1 p-md-6 p-3 position-relative h-100 overflow-hidden">
                        <div class="d-flex align-items-center">
                            <svg class="text-primary-2 me-2" xmlns="http://www.w3.org/2000/svg" width="5" height="6" viewBox="0 0 5 6" fill="none">
                                <circle cx="2.5" cy="3" r="2.5" fill="var(--primary-color)" />
                            </svg>
                            <span class="text-linear-4 d-flex align-items-center">{!! BaseHelper::clean($shortcode->journey_title) !!}</span>
                        </div>
                        <div class="h-100 position-relative">
                            <ul class="ps-3 d-flex flex-column justify-content-around h-100 position-relative">
                                @foreach($journeys as $journey)
                                    <li class="position-relative z-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="text-300 text-nowrap">{{ $journey['date'] }}</p>
                                            <span class="text-dark">{{ $journey['title'] }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="line-left position-absolute border-start z-0"></div>
                        </div>
                        <div class="bg-overlay position-absolute bottom-0 start-0 z-1"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
