@if ($requests->isNotEmpty())
    <div class="table-responsive">
        <x-core::table>
            <x-core::table.header>
                <x-core::table.header.cell>
                    #
                </x-core::table.header.cell>
                <x-core::table.header.cell>
                    {{ trans('core/base::tables.url') }}
                </x-core::table.header.cell>
                <x-core::table.header.cell class="text-end">
                    {{ trans('plugins/request-log::request-log.status_code') }}
                </x-core::table.header.cell>
            </x-core::table.header>

            <x-core::table.body>
                @foreach ($requests as $request)
                    <x-core::table.body.row>
                        <x-core::table.body.cell>
                            {{ $loop->index + 1 }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell>
                            <a
                                href="{{ $request->url }}"
                                target="_blank"
                                title="{{ $request->url }}"
                            >{{ Str::limit($request->url, 50) }} <x-core::icon
                                    name="ti ti-external-link"
                                    size="sm"
                                /></a>
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-end">
                            {{ $request->status_code }}
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                @endforeach
            </x-core::table.body>
        </x-core::table>
    </div>

    @if ($requests instanceof Illuminate\Pagination\LengthAwarePaginator)
        <x-core::card.footer>
            {{ $requests->links('core/base::components.simple-pagination') }}
        </x-core::card.footer>
    @endif
@else
    <x-core::empty-state
        :title="__('No results found')"
        :subtitle="__('It looks as through there are no request errors here.')"
    />
@endif
