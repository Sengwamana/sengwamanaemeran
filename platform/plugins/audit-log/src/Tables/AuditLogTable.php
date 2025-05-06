<?php

namespace Botble\AuditLog\Tables;

use Botble\AuditLog\Models\AuditHistory;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\HeaderActions\HeaderAction;
use Illuminate\Contracts\Database\Eloquent\Builder;

class AuditLogTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(AuditHistory::class)
            ->setView('plugins/audit-log::table')
            ->addColumns([
                IdColumn::make(),
                FormattedColumn::make('action')
                    ->title(trans('plugins/audit-log::history.action'))
                    ->alignStart()
                    ->renderUsing(function (FormattedColumn $column) {
                        return view('plugins/audit-log::activity-line', ['history' => $column->getItem()])->render();
                    }),
            ])
            ->addHeaderActions([
                HeaderAction::make('empty')
                    ->label(trans('plugins/audit-log::history.delete_all'))
                    ->icon('ti ti-trash')
                    ->url('javascript:void(0)')
                    ->attributes(['class' => 'empty-activities-logs-button']),
            ])
            ->addAction(DeleteAction::make()->route('audit-log.destroy'))
            ->addBulkAction(DeleteBulkAction::make()->permission('audit-log.destroy'))
            ->queryUsing(fn (Builder $query) => $query->with('user'))
            ->onAjax(function (AuditLogTable $table) {
                return $table->toJson(
                    $table
                        ->table
                        ->eloquent($table->query())
                        ->filter(function ($query) {
                            if ($keyword = $this->request->input('search.value')) {
                                $keyword = '%' . $keyword . '%';

                                return $query
                                    ->where('action', 'LIKE', $keyword)
                                    ->orWhere('module', 'LIKE', $keyword)
                                    ->orWhere('type', 'LIKE', $keyword)
                                    ->orWhere('ip_address', 'LIKE', $keyword)
                                    ->orWhere('user_agent', 'LIKE', $keyword)
                                    ->orWhere('reference_name', 'LIKE', $keyword)
                                    ->orWhereHas('user', function ($subQuery) use ($keyword) {
                                        return $subQuery
                                            ->where('first_name', 'LIKE', $keyword)
                                            ->orWhere('last_name', 'LIKE', $keyword)
                                            ->orWhereRaw('concat(first_name, " ", last_name) LIKE ?', $keyword);
                                    });
                            }

                            return $query;
                        })
                );
            });
    }
}
