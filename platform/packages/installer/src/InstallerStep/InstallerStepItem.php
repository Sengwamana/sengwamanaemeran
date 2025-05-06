<?php

namespace Botble\Installer\InstallerStep;

use Closure;

class InstallerStepItem
{
    protected Closure|string $label;

    protected ?string $route = null;

    protected int $priority = 0;

    public static function make(): static
    {
        return app(static::class);
    }

    public function label(Closure|string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function route(string $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function priority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getLabel(): string
    {
        return value($this->label);
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
