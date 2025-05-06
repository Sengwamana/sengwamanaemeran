<?php

namespace Botble\Faq\Tables;

use Botble\Faq\Models\FaqCategory;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class FaqCategoryTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(FaqCategory::class)
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('faq_category.edit'),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->addHeaderAction(CreateHeaderAction::make()->route('faq_category.create'))
            ->addActions([
                EditAction::make()->route('faq_category.edit'),
                DeleteAction::make()->route('faq_category.destroy'),
            ])
            ->addBulkAction(DeleteBulkAction::make()->permission('faq_category.destroy'))
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                return $query
                    ->select([
                        'id',
                        'name',
                        'created_at',
                        'status',
                    ]);
            });
    }
}
