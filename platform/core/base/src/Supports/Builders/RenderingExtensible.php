<?php

namespace Botble\Base\Supports\Builders;

use Closure;
use LogicException;

/**
 * @mixin \Botble\Base\Contracts\Builders\Extensible
 */
trait RenderingExtensible
{
    public static function hasGlobalRendering(): bool
    {
        return false;
    }

    public static function globalBeforeRenderingFilterName(): string
    {
        throw new LogicException('Method globalBeforeRenderingFilterName() must be implemented.');
    }

    public static function globalAfterRenderingFilterName(): string
    {
        throw new LogicException('Method globalAfterRenderingFilterName() must be implemented.');
    }

    public static function beforeRendering(callable|Closure $callback, int $priority = 100): void
    {
        if (static::hasGlobalRendering() && static::class === static::getGlobalClassName()) {
            add_action(static::globalBeforeRenderingFilterName(), $callback, $priority, 2);

            return;
        }

        add_action(static::getFilterPrefix() . '_before_rendering', $callback, $priority, 2);
    }

    public static function afterRendering(callable|Closure $callback, int $priority = 100): void
    {
        if (static::hasGlobalRendering() && static::class === static::getGlobalClassName()) {
            add_action(static::globalAfterRenderingFilterName(), $callback, $priority, 2);

            return;
        }

        add_action(static::getFilterPrefix() . '_after_rendering', $callback, $priority, 2);
    }

    protected function dispatchBeforeRendering(): void
    {
        if (static::hasGlobalRendering()) {
            do_action(static::globalBeforeRenderingFilterName(), $this);
        }

        do_action(static::getFilterPrefix() . '_before_rendering', $this);
    }

    protected function dispatchAfterRendering(mixed $rendered): void
    {
        if (static::hasGlobalRendering()) {
            do_action(static::globalAfterRenderingFilterName(), $this, $rendered);
        }

        do_action(static::getFilterPrefix() . '_after_rendering', $this, $rendered);
    }
}
