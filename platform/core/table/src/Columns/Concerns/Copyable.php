<?php

namespace Botble\Table\Columns\Concerns;

use Botble\Table\Columns\FormattedColumn;
use Closure;

trait Copyable
{
    protected bool $copyable = false;

    protected Closure $copyableStateCallback;

    protected string $copyableMessage;

    protected CopyablePosition $copyablePosition;

    protected CopyableAction $copyableAction;

    public function initializeCopyable(): void
    {
        $this->append(
            fn ($column) => $column->appendCopyable()
        );

        $this->prepend(
            fn ($column) => $column->prependCopyable()
        );
    }

    public function copyable(): static
    {
        $this->copyable = true;
        $this->copyableMessage(trans('core/table::table.copied'));
        $this->copyableState(fn (FormattedColumn $column) => $column->getOriginalValue());
        $this->copyablePositionEnd();
        $this->copyableActionCopy();

        return $this;
    }

    public function copyableState(Closure $callback): static
    {
        $this->copyableStateCallback = $callback;

        return $this;
    }

    protected function getCopyableState(): string
    {
        $state = call_user_func($this->copyableStateCallback, $this);

        return $state ?: '';
    }

    public function copyableMessage(string $message): static
    {
        $this->copyableMessage = $message;

        return $this;
    }

    public function copyablePosition(CopyablePosition $position): static
    {
        $this->copyablePosition = $position;

        return $this;
    }

    public function copyablePositionStart(): static
    {
        return $this->copyablePosition(CopyablePosition::Start);
    }

    public function copyablePositionEnd(): static
    {
        return $this->copyablePosition(CopyablePosition::End);
    }

    public function copyableAction(CopyableAction $action): static
    {
        $this->copyableAction = $action;

        return $this;
    }

    public function copyableActionCopy(): static
    {
        return $this->copyableAction(CopyableAction::Copy);
    }

    public function copyableActionCut(): static
    {
        return $this->copyableAction(CopyableAction::Cut);
    }

    public function appendCopyable(): string
    {
        if (! $this->copyable || $this->copyablePosition !== CopyablePosition::End) {
            return '';
        }

        return $this->renderCopyable();
    }

    public function prependCopyable(): string
    {
        if (! $this->copyable || $this->copyablePosition !== CopyablePosition::Start) {
            return '';
        }

        return $this->renderCopyable();
    }

    protected function renderCopyable(): string
    {
        return view('core/table::cells.copyable', [
            'copyableState' => $this->getCopyableState(),
            'copyableMessage' => $this->copyableMessage,
            'copyableAction' => $this->copyableAction,
            'copyablePositionClass' => $this->copyablePosition === CopyablePosition::Start ? 'me-1' : 'ms-1',
        ])->render();
    }
}
