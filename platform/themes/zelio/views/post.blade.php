@php
    Theme::set('pageTitle', $post->name);
@endphp

<section @class(['section-details pb-100', 'pt-80' => get_header_style() != 2, 'pt-160' => get_header_style() == 2])>
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-lg-auto mb-8">
                <div class="text-center">
                    @if($post->firstCategory)
                        <a href="{{ $post->firstCategory->url }}" class="btn btn-gradient d-inline-block text-uppercase">{{ $post->firstCategory->name }}</a>
                    @endif
                    <h3 class="ds-3 mt-3 mb-4 text-dark">{!! BaseHelper::clean($post->name) !!}</h3>
                    <p class="text-300 fs-5 mb-0">{!! BaseHelper::clean(nl2br($post->description)) !!}</p>
                </div>
            </div>
            @if($post->image)
                {{ RvMedia::image($post->image, $post->name) }}
            @endif
            <div class="col-xl-10 mx-lg-auto mt-8">
                <div class="row">
                    <div class="col-xl-9">
                        <div class="ck-content">
                            {!! BaseHelper::clean($post->content) !!}
                        </div>

                        @if ($post->tags->isNotEmpty())
                            @php
                                if (is_plugin_active('language') && is_plugin_active('language-advanced')) {
                                    $post->tags->loadMissing('translations');
                                }
                            @endphp
                            <span class="d-inline-block mb-4">
                                {!! BaseHelper::renderIcon('ti ti-tags') !!}
                                @foreach ($post->tags as $tag)
                                    <a href="{{ $tag->url }}" class="me-0">{{ $tag->name }}</a>@if (!$loop->last), @endif
                                @endforeach
                            </span>
                        @endif
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="border-linear-3 rounded-4 p-lg-6 p-md-4 p-3 mt-lg-0 mt-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ri-time-line fs-6"></i>
                                <span class="ms-2 fs-6">{{ __(':time min read', ['time' => $post->time_reading]) }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="ri-calendar-schedule-line fs-6"></i>
                                <span class="ms-2 fs-6">{{ Theme::formatDate($post->created_at) }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="ri-user-line fs-6"></i>
                                <span class="ms-2 fs-6">{!! BaseHelper::clean(__('By :author', ['author' => "<span class='fw-bold'>{$post->author->name}</span<"])) !!}</span>
                            </div>
                        </div>
                        <div class="border-linear-3 rounded-4 p-lg-6 p-md-4 p-3 mt-4 mb-3">
                            <span class="text-uppercase fs-7">{{ __('Share') }}</span> <br />
                            {!! Theme::renderSocialSharing($post->url, SeoHelper::getDescription(), $post->image) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-9">
                        <br>
                        {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $post) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $relatedPosts = get_related_posts($post->getKey(), 3);
@endphp

@if($relatedPosts->isNotEmpty())
    <section class="section-blog-list">
        <div class="container border-top pt-80 pb-80">
            <h1 class="text-300">{{ __('Related posts') }}</h1>

            @php
                $itemsPerRow = theme_option('post_item_per_row', 3);
            @endphp

            <div @class(['row mt-8', "row-cols-1 row-cols-md-{$itemsPerRow}"])>
                @foreach($relatedPosts as $post)
                    @include(Theme::getThemeNamespace('views.templates.post-item.index'))
                @endforeach
            </div>
        </div>
    </section>
@endif
