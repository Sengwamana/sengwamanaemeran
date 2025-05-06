@if (count($pages) > 0)
    <div class="table-responsive">
        <x-core::table>
            <x-core::table.header>
                <x-core::table.header.cell>
                    #
                </x-core::table.header.cell>
                <x-core::table.header.cell>
                    {{ trans('plugins/analytics::analytics.url') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-end">
                    {{ trans('plugins/analytics::analytics.views') }}
                </x-core::table.header.cell>
            </x-core::table.header>

            <x-core::table.body>
                @foreach ($pages as $page)
                    <x-core::table.body.row>
                        <x-core::table.body.cell>
                            {{ $loop->index + 1 }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell>
                            <a
                                href="{{ $page['url'] }}"
                                target="_blank"
                            >{{ Str::limit($page['pageTitle']) }} <x-core::icon
                                    name="ti ti-external-link"
                                    size="sm"
                                /></a>
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-end">
                            {{ number_format($page['pageViews']) }}
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                @endforeach
            </x-core::table.body>
        </x-core::table>
    </div>
@else
    <x-core::empty-state :title="__('No results found')" />
@endif
