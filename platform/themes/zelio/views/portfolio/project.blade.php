@php
    Theme::set('pageTitle', $project->name);
@endphp

<section class="section-work-single section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-lg-auto mb-lg-0">
                <div class="text-center">
                    <div class="btn btn-gradient d-inline-block text-uppercase">{{ __('Project') }}</div>
                    <h3 class="ds-3 mt-3 mb-4 text-dark">{!! BaseHelper::clean($project->name) !!}</h3>
                    <p class="text-300 fs-5 mb-0">{!! BaseHelper::clean(nl2br($project->description)) !!}</p>
                </div>
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-4 py-8">
                <div class="bg-6 px-5 py-3 rounded-2">
                    <p class="text-300 mb-0">{{ __('Client') }}</p>
                    <h6>{{ $project->client }}</h6>
                </div>
                @if($project->start_date)
                    <div class="bg-6 px-5 py-3 rounded-2">
                        <p class="text-300 mb-0">{{ __('Start Date') }}</p>
                        <h6>{{ Theme::formatDate($project->start_date) }}</h6>
                    </div>
                @endif
                @if($project->category)
                    <div class="bg-6 px-5 py-3 rounded-2">
                        <p class="text-300 mb-0">{{ __('Category') }}</p>
                        <h6><a href="{{ $project->category->url }}">{{ $project->category->name }}</a></h6>
                    </div>
                @endif
                @if($project->author)
                    <div class="bg-6 px-5 py-3 rounded-2">
                        <p class="text-300 mb-0">{{ __('Author') }}</p>
                        <h6>{{ $project->author }}</h6>
                    </div>
                @endif
                @if($project->place)
                    <div class="bg-6 px-5 py-3 rounded-2">
                        <p class="text-300 mb-0">{{ __('Place') }}</p>
                        <h6>{{ $project->place }}</h6>
                    </div>
                @endif
                @if($link = $project->getMetaData('link', true))
                    <div class="bg-6 px-5 py-3 rounded-2">
                        <p class="text-300 mb-0">{{ __('Website') }}</p>
                        <h6>
                            <a href="{{ $link }}" target="_blank"  class="d-flex align-items-center gap-2">
                                {{ __('Visit Website') }}
                                <x-core::icon name="ti ti-external-link" />
                            </a>
                        </h6>
                    </div>
                @endif
            </div>
            @if($project->image)
                {{ RvMedia::image($project->image, $project->name) }}
            @endif
            <div class="col-lg-8 mx-lg-auto mt-8">
                <div class="ck-content">{!! BaseHelper::clean($project->content) !!}</div>
                <div class="border-linear-3 rounded-4 p-lg-6 p-md-4 p-3 my-4">
                    <span class="text-uppercase fs-7">{{ __('Share') }}</span> <br />
                    {!! Theme::renderSocialSharing($project->url, SeoHelper::getDescription(), $project->image) !!}
                </div>
                {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $project) !!}
            </div>
        </div>
    </div>
</section>
