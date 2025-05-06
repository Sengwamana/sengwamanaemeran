<section class="section-hero-1 position-relative pt-200 pb-120 overflow-hidden">
    <div class="shape-1 position-relative position-md-absolute bottom-0 start-0 start-md-50 z-1 ms-0 ms-md-10 mb-30 mb-md-0">
        @if($shortcode->right_image)
            {{ RvMedia::image($shortcode->right_image, $shortcode->title, attributes: ['class' => 'position-relative z-1 filter-gray']) }}
        @endif
        @if($shortcode->right_image_shape)
            <div class="position-absolute top-50 start-0 translate-middle z-0 mt-8 ms-10 ps-8">
                {{ RvMedia::image($shortcode->right_image_shape, $shortcode->title, attributes: ['class' => 'ribbonRotate']) }}
            </div>
        @endif
    </div>
    @if($shortcode->background_image)
        <div class="position-absolute top-0 start-0 w-100 h-100 filter-invert" data-background="{{ RvMedia::getImageUrl($shortcode->background_image) }}"></div>
    @endif

    <div class="container position-relative z-3">
        <div class="row">
            <div class="col-lg-6 col-md-12">
                @if($shortcode->subtitle)
                    <span class="text-dark">{!! BaseHelper::clean($shortcode->subtitle) !!}</span>
                @endif
                @if($shortcode->title)
                    <h1 class="ds-2 mb-3">{!! BaseHelper::clean($shortcode->title) !!}</h1>
                @endif
                @if($shortcode->description)
                    <p class="text-300 mb-6">{!! BaseHelper::clean($shortcode->description) !!}</p>
                @endif
                @if($shortcode->primary_button_text)
                    <a href="{{ $shortcode->primary_button_link }}" class="btn btn-gradient me-2"  @if (($shortcode->open_primary_link_in_the_new_tab ?? 'yes') == 'yes') target="_blank" @endif>
                        {!! BaseHelper::clean($shortcode->primary_button_text) !!}
                        @if($shortcode->primary_button_icon)
                            <x-core::icon :name="$shortcode->primary_button_icon" class="ms-2" />
                        @endif
                    </a>
                @endif
                @if($shortcode->secondary_button_text)
                    <a href="{{ $shortcode->secondary_button_link }}" class="btn btn-outline-secondary d-inline-flex align-items-center" @if (($shortcode->open_secondary_link_in_the_new_tab ?? 'yes') == 'yes') target="_blank" @endif>
                        <span>{!! BaseHelper::clean($shortcode->secondary_button_text) !!}</span>
                        @if($shortcode->secondary_button_icon)
                            <x-core::icon :name="$shortcode->secondary_button_icon" class="ms-2" />
                        @endif
                    </a>
                @endif
                @if($shortcode->below_button_text)
                    <p class="text-400 mt-6 mb-3">{!! BaseHelper::clean($shortcode->below_button_text) !!}</p>
                @endif
                <div class="d-flex gap-3">
                    @foreach($skills as $skill)
                        <div class="brand-logo icon-xl icon-shape rounded-3 bg-900">
                            {{ RvMedia::image($skill['image'], $skill['name']) }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
