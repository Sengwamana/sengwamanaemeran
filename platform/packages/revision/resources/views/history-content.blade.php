<x-core::table>
    <x-core::table.header>
        <x-core::table.header.cell>
            {{ trans('core/base::tables.author') }}
        </x-core::table.header.cell>
        <x-core::table.header.cell>
            {{ trans('core/base::tables.column') }}
        </x-core::table.header.cell>
        <x-core::table.header.cell>
            {{ trans('core/base::tables.origin') }}
        </x-core::table.header.cell>
        <x-core::table.header.cell>
            {{ trans('core/base::tables.after_change') }}
        </x-core::table.header.cell>
        <x-core::table.header.cell>
            {{ trans('core/base::tables.created_at') }}
        </x-core::table.header.cell>
    </x-core::table.header>
    <x-core::table.body>
        @forelse($model->revisionHistory as $history)
            <x-core::table.body.row>
                <x-core::table.body.cell style="min-width: 145px;">
                    {{ $history->userResponsible() ? $history->userResponsible()->name : 'N/A' }}
                </x-core::table.body.cell>
                <x-core::table.body.cell style="min-width: 145px;">
                    {{ $history->fieldName() }}
                </x-core::table.body.cell>
                <x-core::table.body.cell>
                    {{ $history->oldValue() }}
                </x-core::table.body.cell>
                <x-core::table.body.cell
                    class="html-diff-content"
                    data-original="{{ $history->oldValue() }}"
                >
                    {{ $history->newValue() }}
                </x-core::table.body.cell>
                <x-core::table.body.cell style="min-width: 145px;">
                    {{ BaseHelper::formatDateTime($history->created_at) }}
                </x-core::table.body.cell>
            </x-core::table.body.row>
        @empty
            <x-core::table.body.row class="text-center">
                <x-core::table.body.cell colspan="5">
                    {{ trans('core/base::tables.no_record') }}
                </x-core::table.body.cell>
            </x-core::table.body.row>
        @endforelse
    </x-core::table.body>
</x-core::table>
