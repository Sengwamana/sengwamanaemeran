@php
    /** @var Botble\Table\Abstracts\TableAbstract $table */
@endphp

<div class="wrapper-filter">
    <p>{{ trans('core/table::table.filters') }}</p>

    <input
        type="hidden"
        class="filter-data-url"
        value="{{ isset($table) ? $table->getFilterInputUrl() : route('table.filter.input') }}"
    />

    <div class="sample-filter-item-wrap hidden">
        <div class="row filter-item form-filter">
            <div class="col-auto w-50 w-sm-auto">
                <x-core::form.select
                    name="filter_columns[]"
                    :options="array_combine(array_keys($columns), array_column($columns, 'title'))"
                    class="filter-column-key"
                />
            </div>

            <div class="col-auto w-50 w-sm-auto">
                <x-core::form.select
                    name="filter_operators[]"
                    :options="[
                        'like' => trans('core/table::table.contains'),
                        '=' => trans('core/table::table.is_equal_to'),
                        '>' => trans('core/table::table.greater_than'),
                        '<' => trans('core/table::table.less_than'),
                    ]"
                    class="filter-operator filter-column-operator"
                />
            </div>

            <div class="col-auto w-100 w-sm-25">
                <span class="filter-column-value-wrap">
                    <input
                        class="form-control filter-column-value"
                        type="text"
                        placeholder="{{ trans('core/table::table.value') }}"
                        name="filter_values[]"
                    >
                </span>
            </div>

            <div class="col">
                <x-core::button
                    type="button"
                    class="btn-remove-filter-item mb-3 text-danger"
                    :tooltip="trans('core/table::table.delete')"
                    icon="ti ti-trash"
                    :icon-only="true"
                />
            </div>
        </div>
    </div>

    <x-core::form class="filter-form" method="get">
        <input
            type="hidden"
            name="filter_table_id"
            class="filter-data-table-id"
            value="{{ $tableId }}"
        >
        <input
            type="hidden"
            name="class"
            class="filter-data-class"
            value="{{ $class }}"
        >
        <div class="filter_list inline-block filter-items-wrap">
            @foreach ($requestFilters as $filterItem)
                <div @class([
                    'row filter-item form-filter',
                    'filter-item-default' => $loop->first,
                ])>
                    <div class="col-auto w-50 w-sm-auto">
                        <x-core::form.select
                            name="filter_columns[]"
                            :options="['' => trans('core/table::table.select_field')] + array_combine(array_keys($columns), array_column($columns, 'title'))"
                            :value="$filterItem['column']"
                            class="filter-column-key"
                        />
                    </div>

                    <div class="col-auto w-50 w-sm-auto">
                        <x-core::form.select
                            name="filter_operators[]"
                            :options="[
                                'like' => trans('core/table::table.contains'),
                                '=' => trans('core/table::table.is_equal_to'),
                                '>' => trans('core/table::table.greater_than'),
                                '<' => trans('core/table::table.less_than'),
                            ]"
                            :value="$filterItem['operator']"
                            class="filter-operator filter-column-operator"
                        />
                    </div>

                    <div class="col-auto w-100 w-sm-25">
                        <div class="filter-column-value-wrap mb-3">
                            <input
                                class="form-control filter-column-value"
                                type="text"
                                placeholder="{{ trans('core/table::table.value') }}"
                                name="filter_values[]"
                                value="{{ $filterItem['value'] }}"
                            >
                        </div>
                    </div>

                    <div class="col">
                        @if (!$loop->first)
                            <x-core::button
                                type="button"
                                class="btn-remove-filter-item mb-3 text-danger"
                                :tooltip="trans('core/table::table.delete')"
                                icon="ti ti-trash"
                                :icon-only="true"
                            />
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="btn-list">
            <x-core::button
                type="button"
                class="add-more-filter"
            >
                {{ trans('core/table::table.add_additional_filter') }}
            </x-core::button>
            <x-core::button
                type="submit"
                color="primary"
                class="btn-apply"
            >
                {{ trans('core/table::table.apply') }}
            </x-core::button>
            <x-core::button
                tag="a"
                href="{{ URL::current() }}"
                data-bb-toggle="datatable-reset-filter"
                @style(['display: none' => !request()->has('filter_table_id')])
                icon="ti ti-refresh"
                :icon-only="true"
            />
        </div>
    </x-core::form>
</div>
