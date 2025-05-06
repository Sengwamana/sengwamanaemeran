@if (count($browsers) > 0)
    <div class="table-responsive">
        <x-core::table>
            <x-core::table.header>
                <x-core::table.header.cell>
                    #
                </x-core::table.header.cell>
                <x-core::table.header.cell>
                    {{ trans('plugins/analytics::analytics.browser') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-end">
                    {{ trans('plugins/analytics::analytics.sessions') }}
                </x-core::table.header.cell>
            </x-core::table.header>

            <x-core::table.body>
                @foreach ($browsers as $browser)
                    <x-core::table.body.row>
                        <x-core::table.body.cell>
                            {{ $loop->index + 1 }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell>
                            {{ $browser['browser'] }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-end">
                            {{ number_format($browser['sessions']) }}
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                @endforeach
            </x-core::table.body>
        </x-core::table>
    </div>
@else
    <x-core::empty-state :title="__('No results found')" />
@endif
