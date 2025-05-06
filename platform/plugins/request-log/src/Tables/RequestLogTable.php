<?php

namespace Botble\RequestLog\Tables;

use Botble\RequestLog\Models\RequestLog;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\LinkableColumn;
use Botble\Table\HeaderActions\HeaderAction;
use Illuminate\Database\Eloquent\Builder;

class RequestLogTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(RequestLog::class)
            ->setView('plugins/request-log::table')
            ->addColumns([
                IdColumn::make(),
                LinkableColumn::make('url')
                    ->title(trans('core/base::tables.url'))
                    ->alignStart()
                    ->externalLink(),
                Column::make('status_code')
                    ->title(trans('plugins/request-log::request-log.status_code')),
                Column::make('count')
                    ->title(trans('plugins/request-log::request-log.count')),
            ])
            ->addHeaderActions([
                HeaderAction::make('empty')
                    ->label(trans('plugins/request-log::request-log.delete_all'))
                    ->icon('ti ti-trash')
                    ->url('javascript:void(0)')
                    ->attributes(['class' => 'empty-request-logs-button']),
            ])
            ->addAction(DeleteAction::make()->route('request-log.destroy'))
            ->addBulkAction(DeleteBulkAction::make()->permission('request-log.destroy'))
            ->queryUsing(function (Builder $query): void {
                $query
                    ->select([
                    'id',
                    'url',
                    'status_code',
                    'count',
                ]);
            });
    }
}
