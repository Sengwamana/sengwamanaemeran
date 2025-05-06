<?php

namespace Botble\Table\Abstracts;

use Botble\ACL\Models\User;
use Botble\Base\Contracts\BaseModel as BaseModelContract;
use Botble\Base\Contracts\Builders\Extensible as ExtensibleContract;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\Form;
use Botble\Base\Facades\Html;
use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\Builders\Extensible;
use Botble\Base\Supports\Builders\RenderingExtensible;
use Botble\Table\Abstracts\Concerns\DeprecatedFunctions;
use Botble\Table\Abstracts\Concerns\HasActions;
use Botble\Table\Abstracts\Concerns\HasBulkActions;
use Botble\Table\Abstracts\Concerns\HasColumnVisibility;
use Botble\Table\Abstracts\Concerns\HasFilters;
use Botble\Table\Abstracts\Concerns\HasHeaderActions;
use Botble\Table\Columns\CheckboxColumn;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\RowActionsColumn;
use Botble\Table\Contracts\FormattedColumn;
use Botble\Table\HeaderActions\HeaderAction;
use Botble\Table\Supports\Builder as CustomTableBuilder;
use Botble\Table\Supports\TableExportHandler;
use Closure;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use LogicException;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\QueryDataTable;
use Yajra\DataTables\Services\DataTable;

abstract class TableAbstract extends DataTable implements ExtensibleContract
{
    use Conditionable;
    use DeprecatedFunctions;
    use Extensible;
    use HasActions;
    use HasBulkActions;
    use HasColumnVisibility;
    use HasFilters;
    use HasHeaderActions;
    use RenderingExtensible;

    public const TABLE_TYPE_ADVANCED = 'advanced';

    public const TABLE_TYPE_SIMPLE = 'simple';

    protected bool $bStateSave = true;

    protected string $type = self::TABLE_TYPE_ADVANCED;

    protected string $ajaxUrl;

    protected int $pageLength = 10;

    protected $view = 'core/table::table';

    protected array $options = [];

    /**
     * @deprecated since v6.8.0
     */
    protected $repository;

    protected ?BaseModelContract $model = null;

    protected bool $useDefaultSorting = true;

    protected int $defaultSortColumn = 1;

    protected ?string $defaultSortColumnName = null;

    protected Closure $defaultSortingCallback;

    protected bool $hasResponsive = true;

    protected string $exportClass = TableExportHandler::class;

    /**
     * @var \Closure(static): \Illuminate\Http\JsonResponse
     */
    protected Closure $onAjaxCallback;

    /**
     * @var \Botble\Table\Columns\Column[]
     */
    protected array $columns = [];

    /**
     * @var \Closure(\Illuminate\Contracts\Database\Eloquent\Builder): void
     */
    protected Closure $queryUsingCallback;

    /**
     * @var \Closure(\Illuminate\Contracts\Database\Eloquent\Builder): void
     */
    protected Closure $modifyQueryUsingCallback;

    protected bool $earlyTable = false;

    protected string $dom = "fBrt<'card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2'<'d-flex justify-content-between align-items-center gap-3'l<'m-0 text-muted'i>><'d-flex justify-content-center'p>>";

    public function __construct(protected DataTables $table, UrlGenerator $urlGenerator)
    {
        parent::__construct();

        $this->ajaxUrl = $urlGenerator->current();

        if (! $this->getOption('id')) {
            $this->setOption('id', strtolower(Str::slug(Str::snake($this::class))));
        }

        if (! $this->getOption('class')) {
            $this->setOption('class', 'table card-table table-vcenter table-striped table-hover');
        }

        $this->setup();

        $this->setupExtended();

        $this->booted();
    }

    public function setup(): void
    {
    }

    public function booted(): void
    {
    }

    public function getOption(string $key): ?string
    {
        return Arr::get($this->options, $key);
    }

    public function setOption(string $key, $value): static
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function setView(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    public function setDom(string $dom): static
    {
        $this->dom = $dom;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): static
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function html()
    {
        if ($this->isFiltering()) {
            $this->bStateSave = false;
        }

        $parameters = [
            'dom' => $this->getDom(),
            'buttons' => $this->getBuilderParameters(),
            'initComplete' => $this->htmlInitComplete(),
            'drawCallback' => $this->htmlDrawCallback(),
            'paging' => true,
            'searching' => true,
            'info' => true,
            'searchDelay' => 350,
            'bStateSave' => $this->bStateSave,
            'lengthMenu' => [
                array_values(
                    array_unique(array_merge(Arr::sortRecursive([10, 30, 50, 100, 500, $this->pageLength]), [-1]))
                ),
                array_values(
                    array_unique(
                        array_merge(
                            Arr::sortRecursive([10, 30, 50, 100, 500, $this->pageLength]),
                            [trans('core/base::tables.all')]
                        )
                    )
                ),
            ],
            'pageLength' => $this->pageLength,
            'processing' => true,
            'serverSide' => true,
            'bServerSide' => true,
            'bDeferRender' => true,
            'bProcessing' => true,
            'language' => [
                'aria' => [
                    'sortAscending' => 'orderby asc',
                    'sortDescending' => 'orderby desc',
                    'paginate' => [
                        'next' => trans('pagination.next'),
                        'previous' => trans('pagination.previous'),
                    ],
                ],
                'emptyTable' => trans('core/base::tables.no_data'),
                'info' => view('core/table::table-info')->render(),
                'infoEmpty' => trans('core/base::tables.no_record'),
                'lengthMenu' => Html::tag('span', '_MENU_', ['class' => 'dt-length-style'])->toHtml(),
                'search' => '',
                'searchPlaceholder' => trans('core/table::table.search'),
                'zeroRecords' => trans('core/base::tables.no_record'),
                'processing' => Html::image('vendor/core/core/base/images/loading-spinner-blue.gif'),
                'paginate' => [
                    'next' => trans('pagination.next'),
                    'previous' => trans('pagination.previous'),
                ],
                'infoFiltered' => trans('core/table::table.filtered'),
            ],
            'order' => $this->useDefaultSorting ? $this->getDefaultSorting() : [],
            'responsive' => $this->hasResponsive,
            'autoWidth' => false,
        ];

        if (setting('datatables_pagination_type') == 'dropdown') {
            $parameters['sPaginationType'] = 'listbox';
        }

        return $this->builder()
            ->columns($this->getColumns())
            ->ajax(['url' => $this->getAjaxUrl(), 'method' => 'POST'])
            ->parameters($parameters);
    }

    public function getDefaultSorting(): array
    {
        $defaultSortColumnIndex = $this->hasBulkActions() ? $this->defaultSortColumn : 0;

        if ($this->defaultSortColumnName) {
            $columns = $this->getColumns();

            foreach ($columns as $index => $column) {
                if (Arr::get($column->toArray(), 'name') === $this->defaultSortColumnName) {
                    $defaultSortColumnIndex = $index;

                    break;
                }
            }
        }

        return isset($this->defaultSortingCallback)
            ? call_user_func($this->defaultSortingCallback, $this)
            : [
                [
                    $defaultSortColumnIndex,
                    'desc',
                ],
            ];
    }

    public function defaultSortingUsing(Closure $callback): static
    {
        $this->useDefaultSorting = true;

        $this->defaultSortingCallback = $callback;

        return $this;
    }

    /**
     * @param  \Closure(static $table): \Illuminate\Http\JsonResponse  $onAjaxCallback
     */
    public function onAjax(Closure $onAjaxCallback): static
    {
        $this->onAjaxCallback = $onAjaxCallback;

        $this->earlyTable = true;

        return $this;
    }

    public function ajax(): JsonResponse
    {
        if (isset($this->onAjaxCallback)) {
            return call_user_func($this->onAjaxCallback, $this);
        }

        return $this->toJson($this->table->eloquent($this->query()));
    }

    public function getColumns(): array
    {
        $columns = array_merge($this->columns(), $this->columns);

        if (! $this->isSimpleTable()) {
            foreach ($columns as $key => &$column) {
                $className = implode(
                    ' ',
                    array_filter(
                        [Arr::get($column, 'className'), Arr::get($column, 'class'), ' column-key-' . $key]
                    )
                );

                $column['class'] = $className;
                $column['className'] = $className;
            }

            if ($this->hasBulkActions()) {
                $columns = array_merge($this->getCheckboxColumnHeading(), $columns);
            }
        }

        $columns = apply_filters(BASE_FILTER_TABLE_HEADINGS, $columns, $this->getModel(), $this);

        // TODO: Will be removed after operations removed.
        if ($this->hasOperations()) {
            $columns = array_merge($columns, $this->getOperationsHeading());
        }

        if (! empty($this->getRowActions()) && ! $this->isSimpleTable()) {
            $columns = array_merge($columns, $this->getRowActionsHeading());

            foreach ($columns as $index => $item) {
                if ($item instanceof Column && $item->name === 'operations') {
                    unset($columns[$index]);

                    break;
                }
            }
        }

        return $this->applyFilterVisibleColumns($columns);
    }

    /**
     * @param  BaseModel|class-string<BaseModel>  $model
     */
    public function model(BaseModelContract|string $model): static
    {
        if (is_string($model)) {
            throw_unless(
                class_exists($model),
                new LogicException(sprintf('Class [%s] does not exists.', $model))
            );

            throw_unless(
                ($model = app($model)) instanceof BaseModelContract,
                new LogicException(
                    sprintf('Class [%s] must be an instance of %s.', $model::class, BaseModelContract::class)
                )
            );

            $this->model = $model;

            return $this;
        }

        $this->model = $model;

        $this->earlyTable = true;

        return $this;
    }

    protected function getModel(): BaseModelContract|Model
    {
        return $this->model ?: ($this->repository ? $this->repository->getModel() : new BaseModel());
    }

    public function columns()
    {
        return [];
    }

    public function addColumn(Column $column): static
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * @param  \Botble\Table\Columns\Column[]  $columns
     */
    public function addColumns(array $columns): static
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }

        return $this;
    }

    public function removeColumn(string $name): static
    {
        foreach ($this->columns as $index => $column) {
            if ($column->get('data') === $name || $column->get('name') === $name) {
                unset($this->columns[$index]);

                break;
            }
        }

        $this->columns = array_values($this->columns);

        return $this;
    }

    public function removeColumns(array $columns = []): static
    {
        if (! $columns) {
            $columns = array_map(fn ($column) => $column->get('data'), $this->getColumns());
        }

        foreach ($columns as $column) {
            $this->removeColumn($column);
        }

        return $this;
    }

    public function getCheckboxColumnHeading(): array
    {
        return [
            CheckboxColumn::make(),
        ];
    }

    public function getAjaxUrl(): string
    {
        return $this->ajaxUrl;
    }

    public function setAjaxUrl(string $ajaxUrl): static
    {
        $this->ajaxUrl = $ajaxUrl;

        return $this;
    }

    protected function getDom(): ?string
    {
        if ($this->isSimpleTable()) {
            $this->dom = $this->simpleDom();
        }

        return $this->dom;
    }

    public function getBuilderParameters(): array
    {
        $params = [
            'stateSave' => true,
        ];

        if ($this->isSimpleTable()) {
            return $params;
        }

        $buttons = array_merge($this->getButtons(), $this->getActionsButton());

        $buttons = array_merge($buttons, array_unique($this->getDefaultButtons(), SORT_REGULAR));

        if (! $buttons) {
            return $params;
        }

        return $params + compact('buttons');
    }

    public function getButtons(): array
    {
        $buttons = [
            ...$this->getHeaderActions(),
            ...$this->buttons(),
        ];

        $buttons = apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, $this->getModel()::class);

        if (! $buttons) {
            return [];
        }

        $data = [];

        foreach ($buttons as $key => $button) {
            if ($button instanceof HeaderAction) {
                if ($button->currentUserHasAnyPermissions()) {
                    $data[] = $button->toArray();
                }

                continue;
            }

            $buttonClass = 'action-item' . (isset($button['class']) ? ' ' . $button['class'] : null);

            if (Arr::get($button, 'extend') == 'collection') {
                $button['className'] = ($button['className'] ?? null) . $buttonClass;

                $data[] = $button;
            } else {
                $data[] = [
                    'className' => $buttonClass,
                    'text' => Html::tag('span', $button['text'], [
                        'data-action' => $key,
                        'data-href' => Arr::get($button, 'link'),
                    ])->toHtml(),
                ];
            }
        }

        return $data;
    }

    public function buttons()
    {
        return [];
    }

    public function getActionsButton(): array
    {
        if (! $this->getActions()) {
            return [];
        }

        return [
            [
                'extend' => 'collection',
                'text' => '<span>' . trans('core/base::forms.actions') . ' <span class="caret"></span></span>',
                'buttons' => $this->getActions(),
            ],
        ];
    }

    public function getActions(): array
    {
        if ($this->isSimpleTable() || ! $this->actions()) {
            return [];
        }

        $actions = [];

        foreach ($this->actions() as $key => $action) {
            $actions[] = [
                'className' => 'action-item',
                'text' => '<span data-action="' . $key . '" data-href="' . $action['link'] . '"> ' . $action['text'] . '</span>',
            ];
        }

        return $actions;
    }

    public function actions(): array
    {
        return [];
    }

    public function getDefaultButtons(): array
    {
        $buttons = ['reload'];

        if (setting('datatables_default_show_export_button')) {
            $buttons[] = 'export';
        }

        if ($this->hasColumnVisibilityEnabled()) {
            $buttons[] = 'visibility';
        }

        return apply_filters('cms_table_default_buttons', $buttons, $this);
    }

    public function htmlInitComplete(): ?string
    {
        return 'function () {' . $this->htmlInitCompleteFunction() . '}';
    }

    public function htmlInitCompleteFunction(): ?string
    {
        return '
            Botble.initResources();

            document.dispatchEvent(new CustomEvent("core-table-init-completed", {
                detail: {
                    table: this
                }
            }));
        ';
    }

    public function htmlDrawCallback(): ?string
    {
        if ($this->isSimpleTable()) {
            return null;
        }

        return 'function () {' . $this->htmlDrawCallbackFunction() . '}';
    }

    public function htmlDrawCallbackFunction(): ?string
    {
        return <<<'JS'
            var tableWrapper = $(this).closest(".dataTables_wrapper");
            var dtDataCount = this.api().data().count();

            if (dtDataCount === 0) {
                tableWrapper.find(".card-footer").prop('style', 'display: none !important;');
            } else {
                tableWrapper.find(".card-footer").prop('style', null);
            }

            tableWrapper.find(".dataTables_paginate").toggle(this.api().page.info().pages > 1);

            tableWrapper.find(".dataTables_length").toggle(dtDataCount >= 10);
            tableWrapper.find(".dataTables_info").toggle(dtDataCount > 0);

            setTimeout(function () {
                var searchInputWrapper = $(".table-wrapper .table-search-input input");
                if (! searchInputWrapper.val()) {
                    searchInputWrapper.val(tableWrapper.find(".dataTables_filter input").val());
                }

                if (searchInputWrapper.val()) {
                    searchInputWrapper.addClass('border-primary bg-info-subtle')

                    searchInputWrapper.closest('label').find('.search-reset-icon').show()
                    searchInputWrapper.closest('label').find('.search-icon').hide()
                } else {
                    searchInputWrapper.removeClass('border-primary bg-info-subtle')

                    searchInputWrapper.closest('label').find('.search-reset-icon').hide()
                    searchInputWrapper.closest('label').find('.search-icon').show()
                }
            }, 200);
        JS . $this->htmlInitCompleteFunction();
    }

    public function renderTable(array $data = [], array $mergeData = []): View|Factory|Response
    {
        return $this->render($this->view, $data, $mergeData);
    }

    public function render(?string $view = null, array $data = [], array $mergeData = [])
    {
        Assets::addScripts(['datatables', 'moment', 'datepicker'])
            ->addStyles(['datatables', 'datepicker'])
            ->addStylesDirectly('vendor/core/core/table/css/table.css')
            ->addScriptsDirectly([
                'vendor/core/core/base/libraries/bootstrap3-typeahead.min.js',
                'vendor/core/core/table/js/table.js',
                'vendor/core/core/table/js/filter.js',
            ]);

        if (setting('datatables_pagination_type') == 'dropdown') {
            Assets::addScriptsDirectly(['vendor/core/core/base/libraries/datatables/extensions/Pagination/js/dataTables.pagination.min.js'])
                ->addStylesDirectly(['vendor/core/core/base/libraries/datatables/extensions/Pagination/css/dataTables.pagination.min.css']);
        }

        $data['id'] = Arr::get($data, 'id', $this->getOption('id'));
        $data['class'] = Arr::get($data, 'class', $this->getOption('class'));

        $this->setAjaxUrl($this->ajaxUrl . '?' . http_build_query(request()->input()));

        $this->setOptions($data);

        $data['actions'] = $this->getBulkActions();

        $data['table'] = $this;

        return parent::render($view, $data, $mergeData);
    }

    protected function applyScopes(
        EloquentBuilder|QueryBuilder|EloquentRelation|Collection|AnonymousResourceCollection $query
    ): EloquentBuilder|QueryBuilder|EloquentRelation|Collection|AnonymousResourceCollection {
        if (isset($this->modifyQueryUsingCallback)) {
            call_user_func($this->modifyQueryUsingCallback, $query);
        }

        $request = $this->request();

        $requestFilters = [];

        if ($this->isFiltering()) {
            foreach ($this->getFilterColumns() as $key => $item) {
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

        foreach ($requestFilters as $requestFilter) {
            if (! empty($requestFilter['column'])) {
                $query = $this->applyFilterCondition(
                    $query,
                    $requestFilter['column'],
                    $requestFilter['operator'],
                    $requestFilter['value']
                );
            }
        }

        return parent::applyScopes(
            $query instanceof EloquentBuilder
                ? apply_filters(BASE_FILTER_TABLE_QUERY, $query, $this)
                : $query
        );
    }

    public function getValueInput(?string $title, ?string $value, ?string $type, array $data = []): array
    {
        $inputName = 'value';

        if (empty($title)) {
            $inputName = 'filter_values[]';
        }

        $attributes = [
            'class' => 'form-control input-value filter-column-value',
            'placeholder' => trans('core/table::table.value'),
            'autocomplete' => 'off',
        ];

        switch ($type) {
            case 'select':
            case 'customSelect':
                $attributes['class'] = str_replace('form-control ', '', $attributes['class']);
                $attributes['placeholder'] = trans('core/table::table.select_option');
                $html = Form::customSelect($inputName, $data, $value, $attributes)->toHtml(); // @phpstan-ignore-line

                break;

            case 'select-search':
                $attributes['class'] = str_replace('form-control ', '', $attributes['class']);
                $attributes['class'] = $attributes['class'] . ' select-search-full';
                $attributes['placeholder'] = trans('core/table::table.select_option');
                $html = Form::customSelect($inputName, $data, $value, $attributes)->toHtml(); // @phpstan-ignore-line

                break;

            case 'select-ajax':
                $attributes['class'] = str_replace('form-control ', '', $attributes['class']);
                $attributes = [
                    'class' => $attributes['class'] . ' select-autocomplete',
                    'data-url' => Arr::get($data, 'url'),
                    'data-minimum-input' => Arr::get($data, 'minimum-input', 2),
                    'multiple' => Arr::get($data, 'multiple', false),
                    'data-placeholder' => Arr::get($data, 'placeholder', $attributes['placeholder']),
                ];

                // @phpstan-ignore-next-line
                $html = Form::customSelect($inputName, Arr::get($data, 'selected', []), $value, $attributes)->toHtml();

                break;

            case 'number':
                $html = Form::number($inputName, $value, $attributes)->toHtml();

                break;

            case 'date':
                $html = Form::date($inputName, $value, $attributes)->toHtml();

                break;

            case 'datePicker':
                $html = Form::datePicker($inputName, $value, $attributes)->toHtml(); // @phpstan-ignore-line

                break;

            default:
                $html = Form::text($inputName, $value, $attributes)->toHtml();

                break;
        }

        return compact('html', 'data');
    }

    public function getFilters(): array
    {
        $filters = $this->filters;

        if (! $filters) {
            $filters = $this->getAllBulkChanges();
        } else {
            foreach ($filters as $key => $filter) {
                if ($filter instanceof TableBulkChangeAbstract) {
                    if ($filter->getName()) {
                        $filters[$filter->getName()] = $filter->toArray();
                        Arr::forget($filters, $key);
                    } else {
                        $filters[$key] = $filter->toArray();
                    }
                }
            }
        }

        return apply_filters('base_filter_table_filters', $filters, $this);
    }

    protected function addCreateButton(string $url, ?string $permission = null, array $buttons = []): array
    {
        if (! $permission || $this->hasPermission($permission)) {
            $queryString = http_build_query(Request::query());

            if ($queryString) {
                $url .= '?' . $queryString;
            }

            $buttons['create'] = [
                'link' => $url,
                'text' => view('core/table::partials.create')->render(),
                'class' => 'btn-primary',
            ];
        }

        return $buttons;
    }

    protected function setupFormattedColumns(DataTableAbstract $table): void
    {
        foreach ($this->getColumnsFromBuilder() as $column) {
            switch (true) {
                case $column instanceof RowActionsColumn:
                    $table->addColumn($column->name, function ($item) use ($column) {
                        return $column
                            ->setRowActions($this->getRowActions())
                            ->renderCell($item, $this);
                    });

                    break;

                case $column instanceof Column && $column instanceof FormattedColumn:
                    $table->editColumn($column->name, function (BaseModelContract|stdClass|array $item) use ($column) {
                        return $column->renderCell($item, $this);
                    });

                    break;
            }
        }
    }

    public function toJson($data, array $escapeColumn = [], bool $mDataSupport = true)
    {
        if ($data instanceof DataTableAbstract) {
            $this->setupFormattedColumns($data);
        }

        $this->dispatchBeforeRendering();

        $data = match (true) {
            $data instanceof EloquentDataTable
                => apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->getModel(), $this),
            $data instanceof QueryDataTable
                => apply_filters(BASE_FILTER_GET_LIST_DATA_FOR_QUERY_TABLE, $data, $this),
            default => apply_filters(BASE_FILTER_GET_LIST_DATA, $data, new BaseModel(), $this),
        };

        return tap(
            $data
                ->escapeColumns($escapeColumn)
                ->make($mDataSupport),
            fn ($response) => $this->dispatchAfterRendering($response)
        );
    }

    public function htmlBuilder(): CustomTableBuilder
    {
        return app(CustomTableBuilder::class);
    }

    protected function simpleDom(): string
    {
        return "rt<'card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2'<'d-flex justify-content-between align-items-center gap-3'l<'m-0 text-muted'i>><'d-flex justify-content-center'p>>";
    }

    protected function isEmpty(): bool
    {
        return ! $this->request()->wantsJson() &&
            ! $this->request()->ajax() &&
            ! $this->isFiltering() &&
            ! (method_exists($this, 'query') && $this->query()->exists());
    }

    public function hasPermission(string $permission): bool
    {
        $user = Auth::guard()->user();

        if (! $user instanceof User) {
            return true;
        }

        return $user->hasPermission($permission);
    }

    public function hasAnyPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  \Closure|callable(\Illuminate\Contracts\Database\Eloquent\Builder $query): void  $queryUsingCallback
     */
    public function queryUsing(Closure|callable $queryUsingCallback): static
    {
        $this->queryUsingCallback = $queryUsingCallback;

        return $this;
    }

    /**
     * @param  \Closure|callable(\Illuminate\Contracts\Database\Eloquent\Builder $query): void  $modifyQueryCallback
     */
    public function modifyQueryUsing(Closure|callable $modifyQueryCallback): static
    {
        $this->modifyQueryUsingCallback = $modifyQueryCallback;

        return $this;
    }

    public function query()
    {
        $query = $this->getModel()->query();

        if (isset($this->queryUsingCallback)) {
            call_user_func($this->queryUsingCallback, $query);

            $query = $this->applyScopes($query);
        }

        return $query;
    }

    protected function isSimpleTable(): bool
    {
        return $this->view === $this->simpleTableView() || $this->type === self::TABLE_TYPE_SIMPLE;
    }

    protected function simpleTableView(): string
    {
        return 'core/table::simple-table';
    }

    public function isExportingToExcel(): bool
    {
        return $this->request()->input('action') === 'excel';
    }

    public function isExportingToCSV(): bool
    {
        return $this->request()->input('action') === 'csv';
    }

    public static function getFilterPrefix(): string
    {
        return sprintf('base_table_%s', Str::of(static::class)->snake()->lower()->replace('\\', '')->toString());
    }

    public static function getGlobalClassName(): string
    {
        return TableAbstract::class;
    }

    public static function hasGlobalExtend(): bool
    {
        return true;
    }

    public static function globalExtendFilterName(): string
    {
        return TableAbstract::getFilterPrefix() . '_extended';
    }

    public static function hasGlobalRendering(): bool
    {
        return true;
    }

    public static function globalBeforeRenderingFilterName(): string
    {
        return TableAbstract::getFilterPrefix() . '_before_rendering';
    }

    public static function globalAfterRenderingFilterName(): string
    {
        return TableAbstract::getFilterPrefix() . '_after_rendering';
    }
}
