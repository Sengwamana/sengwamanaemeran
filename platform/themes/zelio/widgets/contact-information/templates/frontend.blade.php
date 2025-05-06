@if($sidebar === 'sidebar_panel_sidebar')
    <div class="contact-list mb-30">
        @if($config['bio'])
            <p class="fs-6 fw-medium text-200 mb-5">
                {!! BaseHelper::clean(nl2br($config['bio'])) !!}
            </p>
        @endif
        @foreach($details as $detail)
            <div class="mb-3">
                <span class="text-400 fs-5">{{ $detail['label'] }}</span>
                <p class="mb-0">
                    @if(! empty($detail['url']))
                        <a href="{{ $detail['url'] }}" title="{{ $detail['value'] }}">{{ $detail['value'] }}</a>
                    @else
                        {{ $detail['value'] }}
                    @endif
                </p>
            </div>
        @endforeach
    </div>
@elseif($sidebar === 'primary_sidebar')
    <div class="position-relative d-inline-block">
        @if($config['image'])
            {{ RvMedia::image($config['image'], 'image', attributes: ['class' => 'rounded-5']) }}
        @endif
        @if($config['signature'])
            {{ RvMedia::image($config['signature'], 'image', attributes: ['class' => 'position-absolute top-100 start-50 translate-middle pt-8 z-0']) }}
        @endif
    </div>
    <div class="contact-information-sidebar d-flex flex-column gap-2 mt-9 position-relative z-1">
        @foreach($details as $detail)
            @if(! empty($detail['url']))
                <a href="{{ $detail['url'] }}" title="{{ $detail['value'] }}">
            @else
                <div>
            @endif

            @if($detail['icon'])
                <x-core::icon :name="$detail['icon']" class="text-primary-3" />
            @endif

            <span class="text-300 fs-6 ms-2">{{ $detail['value'] }}</span>

            @if($detail['url'])
                </a>
            @else
                </div>
            @endif
        @endforeach
    </div>
@endif
