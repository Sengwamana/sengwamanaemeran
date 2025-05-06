<x-core::card
    :id="$box['id']"
    class="meta-boxes mb-3"
>
    <x-core::card.header>
        <x-core::card.title>
            {!! BaseHelper::clean($box['title']) !!}
        </x-core::card.title>

        @stack('meta-box-header-' . $box['id'])
    </x-core::card.header>

    <x-core::card.body>
        {!! $callback !!}
    </x-core::card.body>
</x-core::card>
