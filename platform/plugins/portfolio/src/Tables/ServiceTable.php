<?php

namespace Botble\Portfolio\Tables;

use Botble\Portfolio\Models\Service;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ServiceTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Service::class)
            ->addActions([
                EditAction::make()->route('portfolio.services.edit'),
                DeleteAction::make()->route('portfolio.services.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table->eloquent($this->query())
            ->editColumn('category_id', function (Service $service) {
                return $service->category?->name ?: '&mdash;';
            });

        return $this->toJson($data);
    }

    public function query(): Builder
    {
        $query = $this->getModel()
            ->query()
            ->select([
                'id',
                'image',
                'category_id',
                'name',
                'created_at',
                'status',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            ImageColumn::make(),
            NameColumn::make()->route('portfolio.services.edit'),
            Column::make('category_id')
                ->title(trans('plugins/portfolio::portfolio.category')),
            CreatedAtColumn::make(),
            StatusColumn::make(),
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('portfolio.services.create'), 'portfolio.services.create');
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('portfolio.services.destroy'),
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
