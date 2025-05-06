<?php

namespace Botble\Menu\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Menu\Facades\Menu as MenuFacade;
use Botble\Menu\Models\Menu;
use Botble\Menu\Models\MenuLocation;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class MenuTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Menu::class)
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('menus.edit'),
                FormattedColumn::make('locations_display')
                    ->label(trans('packages/menu::menu.locations'))
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        $locations = $column
                            ->getItem()
                            ->locations
                            ->sortBy('name')
                            ->map(function (MenuLocation $location) {
                                $locationName = Arr::get(MenuFacade::getMenuLocations(), $location->location);

                                if (! $locationName) {
                                    return null;
                                }

                                return BaseHelper::renderBadge($locationName, 'info', ['class' => 'me-1']);
                            })
                            ->all();

                        return implode(', ', $locations);
                    })
                    ->withEmptyState(),
                FormattedColumn::make('items_count')
                    ->label(trans('packages/menu::menu.items'))
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        return BaseHelper::renderIcon('ti ti-link') . ' '
                            . number_format($column->getItem()->menu_nodes_count);
                    }),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->addHeaderAction(CreateHeaderAction::make()->route('menus.create'))
            ->addActions([
                EditAction::make()->route('menus.edit'),
                DeleteAction::make()->route('menus.destroy'),
            ])
            ->addBulkAction(DeleteBulkAction::make()->permission('menus.destroy'))
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query): void {
                $query
                    ->select([
                        'id',
                        'name',
                        'created_at',
                        'status',
                    ])
                    ->with('locations')
                    ->withCount('menuNodes');
            });
    }
}
