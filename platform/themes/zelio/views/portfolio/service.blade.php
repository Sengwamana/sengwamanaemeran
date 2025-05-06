@php
    Theme::set('pageTitle', $service->name);
@endphp

<section class="section-work-single section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-lg-auto mb-lg-0 pb-8">
                <div class="text-center">
                    <div class="btn btn-gradient d-inline-block text-uppercase">{{ __('Service') }}</div>
                    <h3 class="ds-3 mt-3 mb-4 text-dark">{!! BaseHelper::clean($service->name) !!}</h3>
                    <p class="text-300 fs-5 mb-0">{!! BaseHelper::clean(nl2br($service->description)) !!}</p>
                </div>
            </div>
            @if($service->image)
                {{ RvMedia::image($service->image, $service->name) }}
            @endif
            <div class="col-lg-8 mx-lg-auto mt-8">
                <div class="ck-content">{!! BaseHelper::clean($service->content) !!}</div>
                <div class="border-linear-3 rounded-4 p-lg-6 p-md-4 p-3 my-4">
                    <span class="text-uppercase fs-7">{{ __('Share') }}</span> <br />
                    {!! Theme::renderSocialSharing($service->url, SeoHelper::getDescription(), $service->image) !!}
                </div>
                {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $service) !!}
            </div>
        </div>
    </div>
</section>
