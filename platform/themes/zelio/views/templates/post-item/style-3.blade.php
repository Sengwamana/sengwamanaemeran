<div @class(['card-services rounded-4 border border-secondary-3 bg-white p-lg-4 p-md-4 p-3', 'mb-3' => ! $loop->last])>
    @if($post->firstCategory)
        <a class="fs-18 text-primary-3" href="{{ $post->firstCategory->url }}">
            {{ $post->firstCategory->name }}
        </a>
    @endif
    <div class="d-flex align-items-center gap-5">
        <div>
            <a href="{{ $post->url }}">
                <p class="fs-26 text-dark">{!! BaseHelper::clean($post->name) !!}</p>
            </a>
            @if($post->description)
                <p class="mb-0">{!! BaseHelper::clean($post->description) !!}</p>
            @endif
        </div>
        <div class="image-right">
            {{ RvMedia::image($post->image, $post->name, 'thumb', attributes: ['class' => 'rounded-3 w-100 h-100']) }}
        </div>
    </div>
</div>
