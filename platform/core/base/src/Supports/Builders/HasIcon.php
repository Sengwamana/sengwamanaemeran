<?php

namespace Botble\Base\Supports\Builders;

use Closure;

trait HasIcon
{
    protected Closure|string $icon;

    protected bool $iconOnly = true;

    /**
     * @param \Closure(\Botble\Base\Models\BaseModel $model): string|string $icon
     */
    public function icon(Closure|string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function hasIcon(): bool
    {
        return isset($this->icon);
    }

    public function isRenderabeIcon(): bool
    {
        return $this->icon instanceof Closure;
    }

    public function getIcon(): ?string
    {
        if (! $this->hasIcon()) {
            return null;
        }

        return $this->isRenderabeIcon() ? call_user_func($this->icon, $this) : $this->icon;
    }

    public function iconOnly(bool $iconOnly = true): static
    {
        $this->iconOnly = $iconOnly;

        return $this;
    }

    public function isIconOnly(): bool
    {
        return $this->iconOnly;
    }
}
