<x-core::datagrid>
    <x-core::datagrid.item>
        <x-slot:title>{{trans('core/base::tables.name') }}</x-slot:title>
        {{ $quote->name }}
    </x-core::datagrid.item>

    <x-core::datagrid.item>
        <x-slot:title>{{ trans('core/base::tables.email') }}</x-slot:title>
        {{ Html::mailto($quote->email) }}
    </x-core::datagrid.item>

    <x-core::datagrid.item>
        <x-slot:title>{{ trans('plugins/portfolio::portfolio.time') }}</x-slot:title>
        {{ $quote->created_at->translatedFormat('d M Y H:i:s') }}
    </x-core::datagrid.item>

    @if ($quote->fields)
        @foreach($quote->fields as $key => $value)
            @continue(blank($value))
            <x-core::datagrid.item>
                <x-slot:title>{{ $key }}</x-slot:title>
                {{ $value }}
            </x-core::datagrid.item>
        @endforeach
    @endif
</x-core::datagrid>

@if ($quote->message)
    <x-core::datagrid.item class="mt-3">
        <x-slot:title>{{ trans('plugins/portfolio::portfolio.message') }}</x-slot:title>
        {{ $quote->message }}
    </x-core::datagrid.item>
@endif
