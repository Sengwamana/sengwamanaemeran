<div class="typical pt-70">
    @if($shortcode->title)
        <h3>{!! BaseHelper::clean($shortcode->title) !!}</h3>
    @endif
    <div class="container px-0 pt-4">
        <div class="row">
            <div class="card-scroll">
                <div class="cards">
                    @foreach($projects as $project)
                        <div class="card-custom pt-0" data-index="{{ $loop->index }}">
                            <div class="card__inner rounded-4 border border-secondary-3 bg-white p-lg-5 p-md-4 p-3">
                                @if ($project->image)
                                    <div class="card__image-container rounded-0 zoom-img position-relative">
                                        {{ RvMedia::image($project->image, $project->name, attributes: ['class' => 'card__image']) }}
                                        <a href="{{ $project->url }}" title="{{ $project->name }}" class="card-image-overlay position-absolute start-0 end-0 w-100 h-100"></a>
                                    </div>
                                @endif
                                <div class="card__content px-md-4 px-3 pt-lg-0 pb-lg-8 pb-5">
                                    <div class="card__title mb-0 mb-lg-2">
                                        @if ($project->client)
                                            <p class="text-300 fs-5 mb-0">{{ $project->client }}</p>
                                        @endif
                                        <a href="{{ $project->url }}">
                                            <p class="fs-3 text-dark">{!! BaseHelper::clean($project->name) !!}</p>
                                        </a>
                                    </div>
                                    @if($project->description)
                                        <p class="text-300 mb-lg-auto mb-md-4 mb-3">
                                            {!! BaseHelper::clean($project->description) !!}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
