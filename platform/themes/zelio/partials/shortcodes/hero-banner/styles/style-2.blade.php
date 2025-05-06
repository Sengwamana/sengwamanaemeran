<section id="about" class="section-hero-2 position-relative pt-80 pb-3">
    <div class="container hero-2">
        <div class="border border-1 rounded-3">
            <div class="box-linear-animation position-relative z-1">
                <div class="row align-items-end py-60">
                    @if($hasLeftCol = ($shortcode->right_image || $shortcode->right_image_shape))
                        <div class="col-lg-5 ps-lg-5 text-lg-start text-center">
                            <div class="position-relative mb-lg-0 mb-5">
                                @if($shortcode->right_image)
                                    {{ RvMedia::image($shortcode->right_image, $shortcode->title) }}
                                @endif
                                @if($shortcode->right_image_shape)
                                    <div class="position-absolute end-0 top-100 translate-middle-y icon-decorate">
                                        {{ RvMedia::image($shortcode->right_image_shape, $shortcode->title, attributes: ['width' => '80px']) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    <div @class(['mx-lg-auto col-md-12', 'col-lg-6' => $hasLeftCol, 'col-lg-10' => ! $hasLeftCol])>
                        <div class="p-lg-0 p-md-8 p-3">
                            @if($shortcode->subtitle)
                                <div class="text-secondary-2 d-flex align-items-center">
                                    &lt;span&gt;
                                    <div class="text-dark">
                                        <div class="typewriter">
                                            <h1 class="fs-6 fw-medium">{!! BaseHelper::clean($shortcode->subtitle) !!}</h1>
                                        </div>
                                    </div>
                                    &lt;/span&gt;
                                </div>
                            @endif
                            @if($shortcode->title)
                                <h1 class="fs-50 my-3 section-hero-title">{!! BaseHelper::clean($shortcode->title) !!}<span class="flicker">_</span></h1>
                            @endif
                            @if($shortcode->description)
                                <p class="mb-6 text-secondary-2">
                                    &lt;p&gt;<span class="text-dark section-hero-description">{!! BaseHelper::clean($shortcode->description) !!}</span>&lt;/p&gt;
                                </p>
                            @endif
                            <div class="row">
                                <div class="col-7">
                                    <div class="carouselTicker carouselTicker-left position-relative z-1 mt-lg-0 mt-8">
                                        <ul class="carouselTicker__list">
                                            @foreach($skills as $skill)
                                                <li class="carouselTicker__item">
                                                    <div class="brand-logo icon_60 icon-shape rounded-3">
                                                        {{ RvMedia::image($skill['image'], $skill['name']) }}
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-5 d-flex align-items-end">
                                    @if($shortcode->below_button_text)
                                        <span class="fs-6 text-300 mb-2">{{ $shortcode->below_button_text }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($shortcode->primary_button_text)
                                <a href="{{ $shortcode->primary_button_link }}" class="btn me-2 text-300 ps-0 mt-4"  @if (($shortcode->open_primary_link_in_the_new_tab ?? 'yes') == 'yes') target="_blank" @endif>
                                    @if($shortcode->primary_button_icon)
                                        <x-core::icon :name="$shortcode->primary_button_icon" class="text-primary-2" />
                                    @endif
                                    {!! BaseHelper::clean($shortcode->primary_button_text) !!}
                                </a>
                            @endif
                            @if($shortcode->secondary_button_text)
                                <a href="{{ $shortcode->secondary_button_link }}" class="btn me-2 text-300 ps-0 mt-4"  @if (($shortcode->open_secondary_link_in_the_new_tab ?? 'yes') == 'yes') target="_blank" @endif>
                                    @if($shortcode->secondary_button_icon)
                                        <x-core::icon :name="$shortcode->secondary_button_icon" class="text-secondary-2" />
                                    @endif
                                    {!! BaseHelper::clean($shortcode->secondary_button_text) !!}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($shortcode->background_image || $shortcode->background_image_dark)
        <div class="background position-absolute top-0 start-0 w-100 h-100">
            @if($shortcode->background_image)
                {{ RvMedia::image($shortcode->background_image, $shortcode->title, attributes: ['class' => 'bg-w']) }}
            @endif
            @if($shortcode->background_image_dark)
                {{ RvMedia::image($shortcode->background_image_dark, $shortcode->title, attributes: ['class' => 'bg-d']) }}
            @endif
        </div>
    @endif
</section>
