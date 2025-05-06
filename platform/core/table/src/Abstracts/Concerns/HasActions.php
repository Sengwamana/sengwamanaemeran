<?php

namespace Botble\Table\Abstracts\Concerns;

use Botble\Table\Abstracts\TableActionAbstract;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\RowActionsColumn;
use Closure;
use Illuminate\Database\Eloquent\Model;
use LogicException;

trait HasActions
{
    /**
     * @var \Botble\Table\Abstracts\TableActionAbstract[] $actions
     */
    protected array $rowActions = [];

    protected array $rowActionsEditCallbacks = [];

    protected bool $displayActionsAsDropdown = true;

    protected int $displayActionsAsDropdownWhenActionsMoresThan = 3;

    /**
     * @deprecated since v6.8.0
     */
    protected $hasOperations = true;

    /**
     * @internal
     */
    public function getRowActions(): array
    {
        return collect($this->rowActions)
            ->filter(fn (TableActionAbstract $action) => $action->currentUserHasAnyPermissions())
            ->mapWithKeys(fn (TableActionAbstract $action, string $name) => [$name => $this->getAction($name)])
            ->sortBy(fn (TableActionAbstract $action) => $action->getPriority())
            ->all();
    }

    public function addAction(TableActionAbstract $action): static
    {
        $this->hasOperations = false;

        $this->rowActions[$action->getName()] = $action;

        return $this;
    }

    /**
     * @param \Botble\Table\Abstracts\TableActionAbstract[] $actions
     */
    public function addActions(array $actions): static
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }

        return $this;
    }

    public function removeAction(string $name): static
    {
        unset($this->rowActions[$name]);

        return $this;
    }

    public function removeActions(array $name): static
    {
        foreach ($name as $key) {
            $this->removeAction($key);
        }

        return $this;
    }

    public function removeAllActions(): static
    {
        $this->rowActions = [];

        return $this;
    }

    public function hasAction(string $name): bool
    {
        return isset($this->rowActions[$name]);
    }

    public function hasActions(): bool
    {
        return ! empty($this->getRowActions()) || $this->hasOperations;
    }

    /**
     * @param \Closure(\Botble\Table\Abstracts\TableActionAbstract $action): \Botble\Table\Abstracts\TableActionAbstract $callback
     */
    public function editAction(string $name, Closure $callback): static
    {
        if (! $this->hasAction($name)) {
            throw new LogicException('Action not found.');
        }

        $this->rowActionsEditCallbacks[$name][] = $callback;

        return $this;
    }

    public function getAction(string $name): TableActionAbstract
    {
        if (! $this->hasAction($name)) {
            throw new LogicException('Action not found.');
        }

        $action = $this->rowActions[$name];

        if (isset($this->rowActionsEditCallbacks[$name])) {
            foreach ($this->rowActionsEditCallbacks as $callback) {
                $callback($action);
            }
        }

        return $action;
    }

    protected function getRowActionsHeading(): array
    {
        return [
            RowActionsColumn::make()->nowrap(),
        ];
    }

    public function displayActionsAsDropdown(bool $enabled = true): static
    {
        $this->displayActionsAsDropdown = $enabled;

        return $this;
    }

    public function hasDisplayActionsAsDropdown(): bool
    {
        return $this->displayActionsAsDropdown
            && count($this->getRowActions()) > $this->getDisplayActionsAsDropdownWhenActionsMoresThan();
    }

    public function displayActionsAsDropdownWhenActionsMoresThan(int $number): static
    {
        if ($number < 0) {
            throw new LogicException('Number must be greater than 0.');
        }

        $this->displayActionsAsDropdown();

        $this->displayActionsAsDropdownWhenActionsMoresThan = $number;

        return $this;
    }

    public function getDisplayActionsAsDropdownWhenActionsMoresThan(): int
    {
        return $this->displayActionsAsDropdownWhenActionsMoresThan;
    }

    /**
     * @deprecated since v6.8.0, will be removed after operations removed.
     */
    public function getOperationsHeading()
    {
        return [
            Column::make('operations')
                ->title(trans('core/base::tables.operations'))
                ->nowrap()
                ->alignCenter()
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->responsivePriority(99),
        ];
    }

    /**
     * @deprecated since v6.8.0, will be removed after operations removed.
     */
    protected function getOperations(?string $edit, ?string $delete, Model $item, ?string $extra = null): string
    {
        return apply_filters(
            'table_operation_buttons',
            view('core/table::partials.actions', compact('edit', 'delete', 'item', 'extra'))->render(),
            $item,
            $edit,
            $delete,
            $extra
        );
    }

    /**
     * @deprecated since v6.8.0, will be removed after operations removed.
     */
    protected function hasOperations(): bool
    {
        return ! $this->earlyTable && $this->hasOperations && ! $this->isSimpleTable() && empty($this->getRowActions());
    }
}
