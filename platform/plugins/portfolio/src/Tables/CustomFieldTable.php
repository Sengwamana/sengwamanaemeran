<?php

namespace Botble\Portfolio\Tables;

use Botble\Portfolio\Models\CustomField;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Illuminate\Database\Eloquent\Builder;

class CustomFieldTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(CustomField::class)
            ->addActions([
                EditAction::make()->route('portfolio.custom-fields.edit'),
                DeleteAction::make()->route('portfolio.custom-fields.destroy'),
            ]);
    }

    public function query(): Builder
    {
        $query = $this->model
            ->query()
            ->select([
                'id',
                'name',
                'type',
                'created_at',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            NameColumn::make()->route('portfolio.custom-fields.edit'),
            EnumColumn::make('type')
                ->title(trans('plugins/portfolio::portfolio.custom_field.type'))
                ->alignLeft(),
            CreatedAtColumn::make(),
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('portfolio.custom-fields.create'), 'portfolio.custom-fields.create');
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('portfolio.custom-fields.destroy'),
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
