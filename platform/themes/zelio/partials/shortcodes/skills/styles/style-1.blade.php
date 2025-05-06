@if(! empty($tabs))
    <section class="section-skills-1 position-relative section-padding bg-900">
        <div class="container">
            <div class="row">
                @if($shortcode->title || $shortcode->subtitle)
                    <div class="text-center mb-7">
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
                <div class="d-flex flex-wrap flex-lg-nowrap justify-content-center gap-0 gap-lg-3 mb-7 px-4 px-lg-6">
                    @foreach($tabs as $tab)
                        <div class="skills">
                            <div class="skills-icon mb-5">
                                {{ RvMedia::image($tab['image'], $tab['name'], 'thumb') }}
                            </div>
                            <div class="skills-ratio text-center">
                                @php
                                    preg_match('/(\d+)(\D+)/', $tab['level'], $matches);
                                    $number = $matches[1] ?? 0;
                                    $unit = $matches[2] ?? '';
                                @endphp
                                <h3 class="count fw-semibold my-0"><span class="odometer fw-semibold" data-count="{{ $number }}"></span>{{ $unit }}</h3>
                                <p class="text-400 fw-medium text-uppercase">{!! BaseHelper::clean($tab['name']) !!}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($shortcode->extra_content)
                    <div class="text-center">
                        <p class="fs-5 text-200 mb-0">
                            {!! BaseHelper::clean(nl2br($shortcode->extra_content)) !!}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
