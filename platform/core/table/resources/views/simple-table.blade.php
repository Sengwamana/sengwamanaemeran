<div class="table-wrapper">
    <x-core::card>
        <x-core::card.header>
            <div class="btn-list">
                <div class="table-search-input">
                    <label><input type="search" class="form-control input-sm" placeholder="{{ trans('core/table::table.search') }}"></label>
                </div>
            </div>
        </x-core::card.header>

        <div class="card-table">
            <div class="table-responsive">
                {!! $dataTable->table(compact('id', 'class'), false) !!}
            </div>
        </div>
    </x-core::card>
</div>

@push('footer')
    {!! $dataTable->scripts() !!}
@endpush
