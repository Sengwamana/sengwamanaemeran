<?php

namespace Botble\Table\Columns\Concerns;

use Closure;

trait HasEmptyState
{
    protected string $emptyState;

    protected Closure $emptyStateUsingCallback;

    public function withEmptyState(string $emptyState = '&mdash;'): static
    {
        $this->emptyState = $emptyState;

        return $this;
    }

    public function emptyStateUsing(Closure $callback): static
    {
        $this->emptyStateUsingCallback = $callback;

        return $this;
    }

    public function renderEmptyStateIfAvailable(?string $default): ?string
    {
        $default = trim((string) $default);

        if (! isset($this->emptyState) || $default) {
            return $default;
        }

        if (isset($this->emptyStateUsingCallback)) {
            return call_user_func($this->emptyStateUsingCallback, $this);
        }

        return $this->emptyState;
    }
}
