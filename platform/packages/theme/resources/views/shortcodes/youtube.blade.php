@if (!empty($url))
    <div
        @if(! $width && ! $height)
            style="position: relative; display: block; height: 0; padding-bottom: 56.25%; overflow: hidden;"
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
@endif
