
@php
    $list = Shortcode::fields()->getTabsData(['label', 'content'], $shortcode, 'list');
@endphp

<section id="skills" class="section-skills-2 pt-5">
    <div class="container">
        <div class="rounded-3 bg-3 border border-1 position-relative overflow-hidden">
            <div class="position-relative z-1 py-60">
                <div class="position-relative z-1">
                    @if($shortcode->title || $shortcode->subtitle)
                        <div class="text-center">
                            @if($shortcode->subtitle)
                                <div class="d-flex align-items-center justify-content-center">
                                    <svg class="text-primary-2 me-2" xmlns="http://www.w3.org/2000/svg" width="5" height="6" viewBox="0 0 5 6" fill="none">
                                        <circle cx="2.5" cy="3" r="2.5" fill="var(--primary-color)" />
                                    </svg>
                                    <span class="text-linear-4 d-flex align-items-center">{!! BaseHelper::clean(nl2br($shortcode->subtitle)) !!}</span>
                                </div>
                            @endif
                            @if($shortcode->title)
                                <h3>{!! BaseHelper::clean($shortcode->title) !!}</h3>
                            @endif
                        </div>
                    @endif
                    <div class="container mt-8">
                        <div class="row">
                            @if($tabs)
                                <div @class(['col-lg-6' => $list, 'col-lg-12' => ! $list])>
                                    <div class="row">
                                        @foreach(collect($tabs)->split(2) as $row)
                                            <div @class(['mx-auto overflow-hidden', 'col-lg-10 col-md-9' => $loop->first, 'col-lg-8 col-md-7 col-10' => ! $loop->first])>
                                                <div class="carouselTicker carouselTicker-right position-relative z-1">
                                                    <ul class="carouselTicker__list m-0">
                                                        @foreach($row as $tab)
                                                            <li class="carouselTicker__item mt-6">
                                                                <div class="brand-logo icon_80 icon-shape rounded-3">
                                                                    {{ RvMedia::image($tab['image'], $tab['name']) }}
                                                                </div>
                                                                <span class="tool-tip">{{ $tab['name'] }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if($list)
                                <div @class(['border-start-md mt-lg-0 mt-5', 'col-lg-6' => $tabs, 'col-lg-12' => ! $tabs])>
                                    <div class="row">
                                        <div class="col-md-10 mx-auto">
                                            <div class="h-100 position-relative">
                                                <ul class="ps-3 d-flex flex-column justify-content-between h-100 position-relative">
                                                    @foreach($list as $item)
                                                        <li class="mb-3">
                                                            <div class="d-flex flex-column flex-md-row gap-2">
                                                                <p class="text-dark text-nowrap mb-0">{{ $item['label'] }}</p>
                                                                <span class="text-300">{{ $item['content'] }}</span>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
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
</section>
