<section class="section-projects-2 pt-5">
    <div class="container">
        <div class="rounded-3 border border-1 position-relative overflow-hidden">
            <div class="box-linear-animation position-relative z-1">
                <div class="p-lg-8 p-md-6 p-3 position-relative z-1">
                    @if($shortcode->subtitle)
                        <div class="d-flex align-items-center">
                            <svg class="text-primary-2 me-2" xmlns="http://www.w3.org/2000/svg" width="5" height="6" viewBox="0 0 5 6" fill="none">
                                <circle cx="2.5" cy="3" r="2.5" fill="var(--primary-color)" />
                            </svg>
                            <span class="text-linear-4 d-flex align-items-center">{!! BaseHelper::clean($shortcode->subtitle) !!}</span>
                        </div>
                    @endif
                    @if($shortcode->title)
                        <h3>{!! BaseHelper::clean($shortcode->title) !!}</h3>
                    @endif
                    <div class="position-relative">
                        <div class="swiper slider-two pb-3 position-relative">
                            <div class="swiper-wrapper">
                                @foreach($projects as $project)
                                    <div class="swiper-slide">
                                        <div class="p-lg-5 p-md-4 p-3 border border-1 mt-5 bg-3">
                                            <div class="row">
                                                @if ($project->image)
                                                    <div class="col-lg-5">
                                                        {{ RvMedia::image($project->image, $project->name, attributes: ['class' => 'w-100']) }}
                                                    </div>
                                                @endif
                                                <div @class(['ps-lg-5 mt-5 mt-lg-0', 'col-lg-7' => $project->image, 'col-lg-12' => ! $project->image])>
                                                    <h4 class="text-linear-4">
                                                        <a href="{{ $project->url }}">{!! BaseHelper::clean($project->name) !!}</a>
                                                    </h4>
                                                    @if($project->description)
                                                        <p>{!! BaseHelper::clean(Str::limit($project->description)) !!}</p>
                                                    @endif
                                                    <ul class="mt-4 list-unstyled">
                                                        <li class="text-secondary-2 mb-3 border-bottom pb-3">{{ __('Project Info') }}</li>
                                                        @if($project->categories->isNotEmpty())
                                                            <li class="text-dark mb-3 border-bottom pb-3">
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-dark mb-0 text-end">{{ __('Category') }}</p>
                                                                    <p class="text-300 mb-0 text-end">{{ $project->categories->map(fn ($item) => $item->name)->join(', ') }}</p>
                                                                </div>
                                                            </li>
                                                        @endif
                                                        @if ($project->client)
                                                            <li class="text-dark mb-3 border-bottom pb-3">
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-dark mb-0 text-end">{{ __('Client') }}</p>
                                                                    <p class="text-300 mb-0 text-end">{{ $project->client }}</p>
                                                                </div>
                                                            </li>
                                                        @endif
                                                        @if ($project->start_date)
                                                            <li class="text-dark mb-3 border-bottom pb-3">
                                                                <div class="d-flex justify-content-between">
                                                                    <p class="text-dark mb-0 text-end">{{ __('Start Date') }}</p>
                                                                    <p class="text-300 mb-0 text-end">{{ Theme::formatDate($project->start_date) }}</p>
                                                                </div>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                    <div class="d-flex flex-wrap align-items-center gap-3 mt-7">
                                                        <a href="{{ $project->getMetaData('link', true) ?: $project->url }}" class="text-300 border-bottom border-1 px-2 pb-2 link-hover">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none">
                                                                <path d="M11.0037 3.91421L2.39712 12.5208L0.98291 11.1066L9.5895 2.5H2.00373V0.5H13.0037V11.5H11.0037V3.91421Z" fill="#8F8F92" />
                                                            </svg>
                                                            {{ $project->getMetaData('link', true) ? __('Live Demo') : __('View Project') }}
                                                        </a>
                                                        @if($githubUrl = $project->getMetaData('github_url', true))
                                                            <a href="{{ $githubUrl }}" class="text-300 border-bottom border-1 px-2 pb-2 link-hover" target="_blank">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                                                    <path
                                                                        d="M12.001 2.5C6.47598 2.5 2.00098 6.975 2.00098 12.5C2.00098 16.925 4.86348 20.6625 8.83848 21.9875C9.33848 22.075 9.52598 21.775 9.52598 21.5125C9.52598 21.275 9.51348 20.4875 9.51348 19.65C7.00098 20.1125 6.35098 19.0375 6.15098 18.475C6.03848 18.1875 5.55098 17.3 5.12598 17.0625C4.77598 16.875 4.27598 16.4125 5.11348 16.4C5.90098 16.3875 6.46348 17.125 6.65098 17.425C7.55098 18.9375 8.98848 18.5125 9.56348 18.25C9.65098 17.6 9.91348 17.1625 10.201 16.9125C7.97598 16.6625 5.65098 15.8 5.65098 11.975C5.65098 10.8875 6.03848 9.9875 6.67598 9.2875C6.57598 9.0375 6.22598 8.0125 6.77598 6.6375C6.77598 6.6375 7.61348 6.375 9.52598 7.6625C10.326 7.4375 11.176 7.325 12.026 7.325C12.876 7.325 13.726 7.4375 14.526 7.6625C16.4385 6.3625 17.276 6.6375 17.276 6.6375C17.826 8.0125 17.476 9.0375 17.376 9.2875C18.0135 9.9875 18.401 10.875 18.401 11.975C18.401 15.8125 16.0635 16.6625 13.8385 16.9125C14.201 17.225 14.5135 17.825 14.5135 18.7625C14.5135 20.1 14.501 21.175 14.501 21.5125C14.501 21.775 14.6885 22.0875 15.1885 21.9875C19.259 20.6133 21.9999 16.7963 22.001 12.5C22.001 6.975 17.526 2.5 12.001 2.5Z"
                                                                        fill="#8F8F92"
                                                                    />
                                                                </svg>
                                                                {{ __('View on GitHub') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if($projects->count() > 1)
                            <div class="position-absolute bottom-0 end-0 gap-2 pb-7 pe-5 d-none d-md-flex">
                                <div class="swiper-button-prev end-0 shadow position-relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M7.82843 10.9999H20V12.9999H7.82843L13.1924 18.3638L11.7782 19.778L4 11.9999L11.7782 4.22168L13.1924 5.63589L7.82843 10.9999Z" fill="white" />
                                    </svg>
                                </div>
                                <div class="swiper-button-next end-0 shadow position-relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z" fill="var(--primary-color)" />
                                    </svg>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @if($shortcode->background_image)
                    {{ RvMedia::image($shortcode->background_image, $shortcode->title, attributes: ['class' => 'position-absolute top-0 start-0 z-0']) }}
                @endif
            </div>
        </div>
    </div>
</section>
