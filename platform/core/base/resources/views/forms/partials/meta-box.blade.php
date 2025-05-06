@if (Arr::get($metaBox, 'before_wrapper'))
    {!! Arr::get($metaBox, 'before_wrapper') !!}
@endif

@if (Arr::get($metaBox, 'wrap', true))
    <x-core::card
        class="mb-3"
        :attributes="new \Illuminate\View\ComponentAttributeBag(Arr::get($metaBox, 'attributes', []))"
    >
        <x-core::card.header>
            @if(($subtitle = Arr::get($metaBox, 'subtitle')) && ($title = Arr::get($metaBox, 'title')))
                <div>
                    <x-core::card.title>{{ $title }}</x-core::card.title>
                    <x-core::card.subtitle>{{ $subtitle }}</x-core::card.subtitle>
                </div>
            @else
                <x-core::card.title>{{ Arr::get($metaBox, 'title') }}</x-core::card.title>
            @endif

            @if (Arr::get($metaBox, 'header_actions'))
                <x-core::card.actions>
                    {!! Arr::get($metaBox, 'header_actions') !!}
                </x-core::card.actions>
            @endif
        </x-core::card.header>

        @if (!($hasTable = Arr::get($metaBox, 'has_table', false)))
            <x-core::card.body>
                {!! Arr::get($metaBox, 'content') !!}
            </x-core::card.body>
        @else
            {!! Arr::get($metaBox, 'content') !!}
        @endif

        @if(($footer = Arr::get($metaBox, 'footer')))
            <x-core::card.footer>
                {!! $footer !!}
            </x-core::card.footer>
        @endif
    </x-core::card>
@else
    {!! Arr::get($metaBox, 'content') !!}
@endif

@if (Arr::get($metaBox, 'after_wrapper'))
    {!! Arr::get($metaBox, 'after_wrapper') !!}
@endif
