<div @class(['d-flex widget-item', 'col-md-' . $columns => $columns])>
    <x-core::card class="flex-fill">
        <x-core::card.header>
            <x-core::card.title>
                {{ $label }}
            </x-core::card.title>
        </x-core::card.header>
        <div class="table-responsive table-widget">
            {!! $table !!}
        </div>
    </x-core::card>
</div>
