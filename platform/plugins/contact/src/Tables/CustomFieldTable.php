<?php

namespace Botble\Contact\Tables;

use Botble\Contact\Models\CustomField;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class CustomFieldTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(CustomField::class)
            ->addHeaderActions([
                CreateHeaderAction::make()->route('contacts.custom-fields.create')->permission('contacts.edit'),
            ])
            ->addBulkChanges([
                NameBulkChange::make()->validate('required|max:120'),
                CreatedAtBulkChange::make(),
            ])
            ->addBulkAction(DeleteBulkAction::make()->permission('contacts.edit'))
            ->addActions([
                EditAction::make()->route('contacts.custom-fields.edit')->permission('contacts.edit'),
                DeleteAction::make()->route('contacts.custom-fields.destroy')->permission('contacts.edit'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('contacts.custom-fields.edit')->permission('contacts.edit'),
                EnumColumn::make('type')
                    ->title(trans('plugins/contact::contact.custom_field.type'))
                    ->alignLeft(),
                CreatedAtColumn::make(),
            ])
            ->queryUsing(fn (Builder $query) => $query->select([
                'id',
                'name',
                'type',
                'created_at',
            ]));
    }
}
