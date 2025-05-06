<?php

namespace Botble\Table\Abstracts\Concerns;

use Botble\Base\Facades\BaseHelper;
use Botble\Table\Abstracts\TableBulkChangeAbstract;
use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

trait HasFilters
{
    protected string $filterTemplate = 'core/table::filter';

    protected string $filterInputUrl = '';

    /**
     * @var \Closure(\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Relations\Relation $query): static
     */
    protected Closure $onFilterQueryCallback;

    protected array $filters = [];

    /**
     * @deprecated since v6.8.0, using `hasFilters` instead.
     */
    public function isHasFilter(): bool
    {
        return $this->hasFilters();
    }

    public function hasFilters(): bool
    {
        return ! empty($this->getFilters());
    }

    public function isFiltering(): bool
    {
        return $this->request()->has('filter_table_id') &&
            $this->request()->input('filter_table_id') === $this->getOption('id');
    }

    public function getFilterColumns(): array
    {
        $columns = $this->getFilters();
        $columnKeys = array_keys($columns);

        return Arr::where((array) $this->request->input('filter_columns', []), function ($item) use ($columnKeys) {
            return in_array($item, $columnKeys);
        });
    }

    public function onFilterQuery(Closure $onFilterQueryCallback): static
    {
        $this->onFilterQueryCallback = $onFilterQueryCallback;

        return $this;
    }

    public function applyFilterCondition(
        EloquentBuilder|QueryBuilder|EloquentRelation $query,
        string $key,
        string $operator,
        ?string $value
    ) {
        if (strpos($key, '.') !== -1) {
            $key = Arr::last(explode('.', $key));
        }

        $column = $this->getModel()->getTable() . '.' . $key;

        $key = preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '', $key));

        if (isset($this->onFilterQueryCallback)) {
            $result = call_user_func_array($this->onFilterQueryCallback, [$query, $key, $operator, $value]);

            if ($result) {
                return $result;
            }
        }

        switch ($key) {
            case 'created_at':
            case 'updated_at':
                if (! $value) {
                    break;
                }

                $validator = Validator::make([$key => $value], [$key => ['date']]);

                if (! $validator->fails()) {
                    $value = BaseHelper::formatDate($value);
                    $query = $query->whereDate($column, $operator, $value);
                }

                break;

            default:
                if ($value === null) {
                    break;
                }

                if ($operator === 'like') {
                    $query = $query->where($column, $operator, '%' . $value . '%');

                    break;
                }

                if ($operator !== '=') {
                    $value = (float) $value;
                }

                $query = $query->where($column, $operator, $value);
        }

        return $query;
    }

    public function renderFilter(): string
    {
        $tableId = $this->getOption('id');
        $class = $this::class;
        $columns = $this->getFilters();

        foreach ($columns as $key => $bulkChange) {
            if ($bulkChange instanceof TableBulkChangeAbstract) {
                if ($bulkChange->getName()) {
                    $columns[$bulkChange->getName()] = $bulkChange->toArray();
                    Arr::forget($columns, $key);
                } else {
                    $columns[$key] = $bulkChange->toArray();
                }
            }
        }

        $request = $this->request();
        $requestFilters = [
            '-1' => [
                'column' => '',
                'operator' => '=',
                'value' => '',
            ],
        ];

        $filterColumns = $this->getFilterColumns();

        if ($filterColumns) {
            $requestFilters = [];
            foreach ($filterColumns as $key => $item) {
                $operator = $request->input('filter_operators.' . $key);

                $value = $request->input('filter_values.' . $key);

                if (is_array($operator) || is_array($value) || is_array($item)) {
                    continue;
                }

                $requestFilters[] = [
                    'column' => $item,
                    'operator' => $operator,
                    'value' => $value,
                ];
            }
        }

        $table = $this;

        return view($this->filterTemplate, compact('columns', 'class', 'tableId', 'requestFilters', 'table'))->render();
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function addFilters(array|string $filters): static
    {
        $filters = is_array($filters) ? $filters : [$filters];

        foreach ($filters as $filter) {
            $this->filters[] = $filter;
        }

        $this->earlyTable = true;

        return $this;
    }

    public function filterInputUrl(string $url): static
    {
        $this->filterInputUrl = $url;

        return $this;
    }

    public function getFilterInputUrl(): string
    {
        return $this->filterInputUrl ?: route('table.filter.input');
    }
}
