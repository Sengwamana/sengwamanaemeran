<x-core::card
    id="panel-section-{{ $groupId }}-{{ $id }}"
    data-priority="{{ $priority }}"
    data-id="{{ $id }}"
    data-group-id="{{ $groupId }}"
    class="mb-4 panel-section panel-section-{{ $id }} panel-section-priority-{{ $priority }}"
>
    <x-core::card.header>
        <div>
            <x-core::card.title>{{ $title }}</x-core::card.title>

            @if($description)
                <x-core::card.subtitle>
                    {{ $description }}
                </x-core::card.subtitle>
            @endif
        </div>
    </x-core::card.header>

    <x-core::card.body>
        <div class="row g-3">
            {{ $children }}
        </div>
    </x-core::card.body>
</x-core::card>
