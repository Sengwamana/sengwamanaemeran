<?php

namespace Botble\Testimonial\Tables;

use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Botble\Testimonial\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;

class TestimonialTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Testimonial::class)
            ->addColumns([
                IdColumn::make(),
                ImageColumn::make(),
                NameColumn::make()->route('testimonial.edit'),
                CreatedAtColumn::make(),
            ])
            ->addHeaderAction(CreateHeaderAction::make()->route('testimonial.create'))
            ->addActions([
                EditAction::make()->route('testimonial.edit'),
                DeleteAction::make()->route('testimonial.destroy'),
            ])
            ->addBulkAction(DeleteBulkAction::make()->permission('testimonial.destroy'))
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
                        'image',
                    ]);
            });
    }
}
