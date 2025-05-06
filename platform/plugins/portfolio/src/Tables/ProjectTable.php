<?php

namespace Botble\Portfolio\Tables;

use Botble\Portfolio\Models\Project;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;

class ProjectTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Project::class)
            ->addActions([
                EditAction::make()->route('portfolio.projects.edit'),
                DeleteAction::make()->route('portfolio.projects.destroy'),
            ]);
    }

    public function query(): Builder
    {
        $query = $this->getModel()
            ->query()
            ->select([
                'id',
                'image',
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
            NameColumn::make()->route('portfolio.projects.edit'),
            CreatedAtColumn::make(),
            StatusColumn::make(),
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('portfolio.projects.create'), 'portfolio.projects.create');
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make(),
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
