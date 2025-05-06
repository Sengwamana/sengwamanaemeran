<?php

namespace Botble\Table\Columns\Concerns;

trait HasIcon
{
    protected string $icon;

    protected IconPosition $iconPosition;

    public function initializeHasIcon(): void
    {
        $this->append(
            fn ($column) => $column->appendIcon()
        );

        $this->prepend(
            fn ($column) => $column->prependIcon()
        );
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;
        $this->iconPositionStart();

        return $this;
    }

    public function iconPosition(IconPosition $position): static
    {
        $this->iconPosition = $position;

        return $this;
    }

    public function iconPositionStart(): static
    {
        return $this->iconPosition(IconPosition::Start);
    }

    public function iconPositionEnd(): static
    {
        return $this->iconPosition(IconPosition::End);
    }

    public function appendIcon(): string
    {
        if (! isset($this->icon) || $this->iconPosition !== IconPosition::End) {
            return '';
        }

        return $this->renderIcon();
    }

    public function prependIcon(): string
    {
        if (! isset($this->icon) || $this->iconPosition !== IconPosition::Start) {
            return '';
        }

        return $this->renderIcon();
    }

    protected function renderIcon(): string
    {
        return view('core/table::cells.icon', [
            'icon' => $this->icon,
            'positionClass' => $this->iconPosition === IconPosition::Start ? 'me-1' : 'ms-1',
        ])->render();
    }
}
