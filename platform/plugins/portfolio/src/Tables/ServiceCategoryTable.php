<?php

namespace Botble\Portfolio\Tables;

use Botble\Portfolio\Models\ServiceCategory;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;

class ServiceCategoryTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(ServiceCategory::class)
            ->addActions([
                EditAction::make()->route('portfolio.service-categories.edit'),
                DeleteAction::make()->route('portfolio.service-categories.destroy'),
            ]);
    }

    public function query(): Builder
    {
        return $this->applyScopes(
            $this->getModel()
                ->query()
                ->select([
                    'id',
                    'name',
                    'status',
                    'created_at',
                ])
        );
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            NameColumn::make()->route('portfolio.service-categories.edit'),
            CreatedAtColumn::make(),
            StatusColumn::make(),
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('portfolio.service-categories.create'), 'portfolio.service-categories.create');
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('portfolio.service-categories.destroy'),
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
