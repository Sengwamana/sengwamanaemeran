<?php

namespace Botble\Portfolio\Tables;

use Botble\Portfolio\Models\Package;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\Columns\YesNoColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;

class PackageTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Package::class)
            ->addActions([
                EditAction::make()->route('portfolio.packages.edit'),
                DeleteAction::make()->route('portfolio.packages.destroy'),
            ])
            ->addHeaderAction(CreateHeaderAction::make()->route('portfolio.packages.create'))
            ->addBulkActions([
                DeleteBulkAction::make()->permission('portfolio.packages.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('portfolio.packages.edit'),
                Column::make('price')
                    ->title(trans('plugins/portfolio::portfolio.price'))
                    ->width(100),
                EnumColumn::make('duration')
                    ->title(trans('plugins/portfolio::portfolio.duration'))
                    ->width(100),
                YesNoColumn::make('is_popular')
                    ->title(trans('plugins/portfolio::portfolio.is_popular')),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->queryUsing(function ($query) {
                return $query
                    ->select([
                    'id',
                    'name',
                    'price',
                    'duration',
                    'is_popular',
                    'created_at',
                    'status',
                ]);
            });
    }
}
