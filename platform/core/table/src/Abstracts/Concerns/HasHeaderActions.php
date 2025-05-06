<?php

namespace Botble\Table\Abstracts\Concerns;

use Botble\Table\HeaderActions\HeaderAction;

trait HasHeaderActions
{
    protected array $headerActions = [];

    public function addHeaderAction(HeaderAction $action): static
    {
        $this->headerActions[$action->getName()] = $action;

        return $this;
    }

    public function addHeaderActions(array $actions): static
    {
        foreach ($actions as $action) {
            $this->addHeaderAction($action);
        }

        $this->earlyTable = true;

        return $this;
    }

    public function removeHeaderAction(string $name): static
    {
        unset($this->headerActions[$name]);

        return $this;
    }

    public function removeHeaderActions(array $names = []): static
    {
        if (! $names) {
            $names = array_keys($this->headerActions);
        }

        foreach ($names as $name) {
            $this->removeHeaderAction($name);
        }

        return $this;
    }

    public function disableHeaderActions(): static
    {
        $this->headerActions = [];

        return $this;
    }

    public function getHeaderActions(): array
    {
        return $this->headerActions;
    }
}
