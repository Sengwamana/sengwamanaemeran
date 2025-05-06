<div
    id="{{ $id . '-parent' }}"
    @class(['d-flex widget-item', 'col-md-' . $columns => $columns])
>
    <x-core::card class="flex-fill">
        <x-core::card.header>
            <x-core::card.title>{{ $label }}</x-core::card.title>
        </x-core::card.header>
        <x-core::card.body
            id="{{ $id }}"
            class="p-0"
        />
    </x-core::card>
    @include('core/base::widgets.partials.chart-script')
</div>
