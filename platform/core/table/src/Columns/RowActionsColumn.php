<?php

namespace Botble\Table\Columns;

use Botble\Table\Contracts\FormattedColumn as FormattedColumnContract;

class RowActionsColumn extends FormattedColumn implements FormattedColumnContract
{
    /**
     * @var \Botble\Table\Abstracts\TableActionAbstract[] $actions
     */
    protected array $rowActions = [];

    public static function make(array|string $data = [], string $name = ''): static
    {
        return parent::make($data ?: 'row_actions', $name)
            ->title(trans('core/base::tables.operations'))
            ->alignCenter()
            ->orderable(false)
            ->searchable(false)
            ->exportable(false)
            ->printable(false)
            ->responsivePriority(99)
            ->columnVisibility();
    }

    public function setRowActions(array $actions): static
    {
        $this->rowActions = $actions;

        return $this;
    }

    public function getRowActions(): array
    {
        return $this->rowActions;
    }

    public function formattedValue($value): string
    {
        return view('core/table::row-actions', [
            'model' => $this->getItem(),
            'actions' => $this->getRowActions(),
            'table' => $this->getTable(),
        ])->render();
    }
}
