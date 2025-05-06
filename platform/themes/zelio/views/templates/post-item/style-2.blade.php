<div class="col">
    <div class="blog-card rounded-top-2 mb-lg-3 mb-md-5 mb-3">
        <div class="blog-card__image position-relative">
            <div class="zoom-img rounded-2 overflow-hidden">
                {{ RvMedia::image($post->image, $post->name, 'post-thumbnail', attributes: ['class' => 'w-100']) }}
                @if($post->firstCategory)
                    <a class="position-absolute bottom-0 start-0 m-3 text-white-keep border border-white fw-medium px-3 py-1 fs-7 bg-white rounded-2" href="{{ $post->firstCategory->url }}">
                        {{ $post->firstCategory->name }}
                    </a>
                @endif
                <a href="{{ $post->url }}" title="{{ $post->name }}" class="blog-card__link position-absolute top-50 start-50 translate-middle icon-md icon-shape rounded-circle">
                    <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>
        </div>
        <div class="blog-card__content position-relative text-center mt-4">
            <span class="blog-card__date fs-7">{{ Theme::formatDate($post->created_at) }} • {{ __(':count min read', ['count' => $post->time_reading]) }}</span>
            <div class="h6 blog-card__title mt-2">{!! BaseHelper::clean($post->name) !!}</div>
            @if($post->description)
                <p class="blog-card__description fs-7">{!! Str::limit(BaseHelper::clean($post->description), 70) !!}</p>
            @endif
            <a href="{{ $post->url }}" class="link-overlay position-absolute top-0 start-0 w-100 h-100" title="{{ $post->name }}"></a>
        </div>
    </div>
</div>
