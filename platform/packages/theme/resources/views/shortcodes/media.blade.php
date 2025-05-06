@php
    $url = $data['url'];
    $width = $data['width'] ?? null;
    $height = $data['height'] ?? null;
@endphp

@switch($type)
    @case('youtube')
    @case('vimeo')
        <div
            @if(! $width && ! $height)
                style="position: relative; display: block; height: 0; padding-bottom: 56.25%; overflow: hidden; margin-bottom: 20px;"
            @else
                style="margin-bottom: 20px;"
            @endif
        >
            <iframe
                src="{{ $url }}"
                @if(! $width && ! $height)
                    style="position: absolute; top: 0; bottom: 0; left: 0; width: 100%; height: 100%; border: 0;"
                @endif
                allowfullscreen
                frameborder="0"
                @if ($height)
                    height="{{ $height }}"
                @endif

                @if ($width)
                    width="{{ $width }}"
                @endif

                title="Video"
            ></iframe>
        </div>
        @break
    @case('tiktok')
        <blockquote
            class="tiktok-embed"
            cite="{{ $data['url'] }}"
            data-video-id="{{ $data['video_id'] }}"
            style="max-width: 605px; min-width: 325px; margin-bottom: 20px; border: none !important;">
            <section></section>
        </blockquote>
        @break
    @case('twitter')
        <div style="margin-bottom: 20px; !important; display: flex; justify-content: center">
            <blockquote class="twitter-tweet" style="border: none !important;"><a href="{{ $data['url'] }}"></a></blockquote>
        </div>
        @break
    @case('video')
        <video @if ($width) width="{{ $width }}" @endif @if ($height) height="{{ $height }}" @endif controls>
            <source src="{{ $data['url'] }}" type="video/{{ $data['extension'] }}">
        </video>
@endswitch
