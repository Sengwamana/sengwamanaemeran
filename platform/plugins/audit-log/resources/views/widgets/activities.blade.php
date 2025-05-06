@if ($histories->isNotEmpty())
    <div class="table-responsive">
        <x-core::table>
            <x-core::table.body>
                @foreach ($histories as $history)
                    <x-core::table.body.row>
                        <x-core::table.body.cell>
                            @include('plugins/audit-log::activity-line', compact('history'))
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                @endforeach
            </x-core::table.body>
        </x-core::table>
    </div>

    @if ($histories instanceof Illuminate\Pagination\LengthAwarePaginator)
        <x-core::card.footer>
            {{ $histories->links('core/base::components.simple-pagination') }}
        </x-core::card.footer>
    @endif
@else
    <x-core::empty-state
        :title="__('No results found')"
        :subtitle="__('It looks as through there are no activities here.')"
    />
@endif
