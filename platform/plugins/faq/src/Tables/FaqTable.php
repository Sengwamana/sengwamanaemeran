<?php

namespace Botble\Faq\Tables;

use Botble\Faq\Models\Faq;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\TextBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\LinkableColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class FaqTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Faq::class)
            ->addColumns([
                IdColumn::make(),
                LinkableColumn::make('question')
                    ->title(trans('plugins/faq::faq.question'))
                    ->route('faq.edit')
                    ->alignStart(),
                FormattedColumn::make('category_id')
                    ->title(trans('plugins/faq::faq.category'))
                    ->alignStart()
                    ->withEmptyState()
                    ->getValueUsing(fn (FormattedColumn $column) => $column->getItem()->category->name),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->addHeaderAction(CreateHeaderAction::make()->route('faq.create'))
            ->addActions([
                EditAction::make()->route('faq.edit'),
                DeleteAction::make()->route('faq.destroy'),
            ])
            ->addBulkAction(DeleteBulkAction::make()->permission('faq.destroy'))
            ->addBulkChanges([
                TextBulkChange::make()
                    ->name('question')
                    ->title(trans('plugins/faq::faq.question')),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                return $query
                    ->select([
                        'id',
                        'question',
                        'created_at',
                        'answer',
                        'category_id',
                        'status',
                    ]);
            });
    }
}
