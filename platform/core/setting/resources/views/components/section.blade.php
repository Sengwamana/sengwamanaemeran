@props([
    'title' => null,
    'description' => null,
    'footer' => null,
    'extraDescription' => null,
    'card' => true,
])

<div class="row mb-5 d-block d-md-flex">
    <div class="col-12 col-md-3">
        @if ($title)
            <h2>{{ $title }}</h2>
        @endif

        @if ($description)
            <p class="text-muted">{!! BaseHelper::clean($description) !!}</p>
        @endif

        {!! BaseHelper::clean($extraDescription) !!}
    </div>

    <div class="col-12 col-md-9">
        @if ($card)
            <x-core::card {{ $attributes }}>
                <x-core::card.body>
                    {{ $slot }}
                </x-core::card.body>
            </x-core::card>
        @else
            {{ $slot }}
        @endif

        {{ $footer }}
    </div>
</div>
