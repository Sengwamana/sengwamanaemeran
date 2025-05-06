<?php

namespace Botble\Table\Columns\Concerns;

trait HasLink
{
    protected bool $linkable = false;

    public function linkable(bool $linkable = true): static
    {
        $this->linkable = $linkable;

        return $this;
    }

    public function isLinkable(): bool
    {
        return $this->linkable;
    }
}
