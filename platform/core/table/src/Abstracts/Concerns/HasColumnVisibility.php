<?php

namespace Botble\Table\Abstracts\Concerns;

use Botble\ACL\Models\User;
use Botble\Table\Columns\Column;
use Illuminate\Support\Str;

trait HasColumnVisibility
{
    protected array $userVisibleColumns;

    protected array $defaultVisibleColumns = [];

    public function hasColumnVisibilityEnabled(): bool
    {
        return (bool) setting('datatables_default_show_column_visibility');
    }

    public function getDefaultVisibleColumns(): array
    {
        return $this->defaultVisibleColumns;
    }

    public function determineIfColumnIsVisible(Column $column): bool
    {
        if (Str::contains($column->className, 'no-column-visibility')
            || in_array($column->name, $this->getDefaultVisibleColumns())) {
            return true;
        }

        $userVisibleColumns = $this->userVisibleColumns();

        if (empty($userVisibleColumns)) {
            return true;
        }

        $isVisible = (int) ($userVisibleColumns[$column->name] ?? 1);

        return $isVisible === 1;
    }

    protected function userVisibleColumns(): array
    {
        if (isset($this->userVisibleColumns)) {
            return $this->userVisibleColumns;
        }

        $user = $this->request()->user();

        if (! $user instanceof User) {
            return [];
        }

        $visibility = json_decode($user->getMeta('datatable_columns_visibility', '[]'), true);

        return $this->userVisibleColumns = $visibility[static::class] ?? [];
    }

    protected function applyFilterVisibleColumns(array $columns): array
    {
        if (! $this->hasColumnVisibilityEnabled()) {
            return $columns;
        }

        foreach ($columns as &$column) {
            if ($column instanceof Column && ! $this->determineIfColumnIsVisible($column)) {
                $column->hidden();
            }
        }

        return apply_filters('table_columns_visibility', $columns, $this);
    }
}
