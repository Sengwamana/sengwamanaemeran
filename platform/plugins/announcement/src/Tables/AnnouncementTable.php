<?php

namespace ArchiElite\Announcement\Tables;

use ArchiElite\Announcement\Models\Announcement;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\YesNoColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class AnnouncementTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Announcement::class)
            ->addActions([
                EditAction::make()->route('announcements.edit'),
                DeleteAction::make()->route('announcements.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()
                    ->route('announcements.edit')
                    ->alignLeft(),
                YesNoColumn::make('is_active')
                    ->title(trans('plugins/announcement::announcements.is_active')),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('announcements.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                SelectBulkChange::make()
                    ->name('is_active')
                    ->title(trans('plugins/announcement::announcements.is_active'))
                    ->choices([
                        1 => trans('core/base::base.yes'),
                        0 => trans('core/base::base.no'),
                    ])
                    ->type('customSelect')
                    ->validate(['required', Rule::in([0, 1])]),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(fn (Builder $query) => $query->select('id', 'name', 'is_active', 'created_at'));
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('announcements.create'), 'announcements.create');
    }
}
