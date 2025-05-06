<section
    id="resume"
    class="section-resume-1 position-relative pt-150 overflow-hidden"
    @if($shortcode->background_image)
        data-background="{{ RvMedia::getImageUrl($shortcode->background_image) }}"
    @endif
>
    <div class="container">
        @if($shortcode->title || $shortcode->subtitle || $shortcode->action_text)
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
                            {!! BaseHelper::clean($shortcode->action_text) !!}
                            <i class="ri-arrow-right-up-line"></i>
                        </a>
                    </div>
                @endif
            </div>
        @endif
        <div @class(['row mt-6 row-cols-1', 'row-cols-lg-1' => count($resumes) === 1, 'row-cols-lg-2' => count($resumes) > 1])>
            @foreach($resumes as $resume)
                @php
                    $iconKey = "resume_{$loop->iteration}_title_icon";
                    $titleKey = "resume_{$loop->iteration}_title";
                @endphp
                <div class="col">
                    <div class="resume-card p-lg-6 p-4 mb-lg-0 mb-6">
                        <div class="resume-card-header d-flex align-items-end">
                            <x-core::icon :name="$shortcode->{$iconKey}" class="border-linear-1 border-3 pb-2 pe-2" />
                            <h3 class="fw-semibold mb-0 border-bottom border-600 border-3 pb-2 w-100">
                                {!! BaseHelper::clean($shortcode->{$titleKey}) !!}
                            </h3>
                        </div>
                        <div class="resume-card-body">
                            @foreach($resume as $item)
                                <div class="resume-card-item px-4 py-3 mt-5">
                                    <div class="d-flex align-items-end">
                                        <div>
                                            @if($item['time'])
                                                <p class="fw-extra-bold text-linear-1 mb-2">{{ $item['time'] }}</p>
                                            @endif
                                            @if($item['title'])
                                                <h5>{{ $item['title'] }}</h5>
                                            @endif
                                            @if($item['description'])
                                                <p class="text-300 mb-0">{{ $item['description'] }}</p>
                                            @endif
                                        </div>
                                        @php
                                            $rating = explode('/', $item['subtitle']);

                                            $total = count($rating) > 1 ? $rating[1] : null;
                                            $rating = $rating[0];
                                        @endphp
                                        <h3 class="text-linear-1 ms-auto fw-semibold">
                                            {{ $rating }}@if($total)<span class="fs-4 fw-bold">/{{ $total }}</span>@endif
                                        </h3>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div @class(['scroll-move-right position-relative', 'pb-160 pt-lg-150' => $shortcode->bottom_text, 'pt-150' => ! $shortcode->bottom_text])>
        @if($shortcode->bottom_text)
            <div class="d-flex align-items-center gap-5 wow img-custom-anim-top position-absolute top-50 start-50 translate-middle">
                <h3 class="stroke fs-150 text-uppercase text-white">{!! BaseHelper::clean($shortcode->bottom_text) !!}</h3>
            </div>
        @endif
    </div>
</section>
