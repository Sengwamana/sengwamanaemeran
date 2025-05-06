<div id="about" class="hero-3 pe-lg-5 border-bottom pb-7">
    @if($shortcode->subtitle)
        <span class="text-primary-3">{!! BaseHelper::clean($shortcode->subtitle) !!}</span>
    @endif
    @if($shortcode->title)
        <h2 class="text-300 my-3">{!! BaseHelper::clean($shortcode->title) !!}</h2>
    @endif
    @if($shortcode->description)
        <p class="mb-8">{!! BaseHelper::clean($shortcode->description) !!}</p>
    @endif
    @if($shortcode->primary_button_text)
        <a href="{{ ($shortcode->primary_button_link ?: $shortcode->primary_button_url) }}" class="btn btn-secondary-3 me-2"  @if (($shortcode->open_primary_link_in_the_new_tab ?? 'yes') == 'yes') target="_blank" @endif>
            {!! BaseHelper::clean($shortcode->primary_button_text) !!}
            @if($shortcode->primary_button_icon)
                <x-core::icon :name="$shortcode->primary_button_icon" class="ms-2" />
            @endif
        </a>
    @endif
    @if($shortcode->secondary_button_text)
        <a href="{{ ($shortcode->secondary_button_link ?: $shortcode->secondary_button_url) }}" class="btn btn-outline-secondary-3 d-inline-flex align-items-center" @if (($shortcode->open_secondary_link_in_the_new_tab ?? 'yes') == 'yes') target="_blank" @endif>
            <span>{!! BaseHelper::clean($shortcode->secondary_button_text) !!}</span>
            @if($shortcode->secondary_button_icon)
                <x-core::icon :name="$shortcode->secondary_button_icon" class="ms-2" />
            @endif
        </a>
    @endif
</div>
