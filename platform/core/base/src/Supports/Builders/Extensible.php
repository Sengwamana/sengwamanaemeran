<?php

namespace Botble\Base\Supports\Builders;

use Closure;
use LogicException;

/**
 * @mixin \Botble\Base\Contracts\Builders\Extensible
 */
trait Extensible
{
    public static function hasGlobalExtend(): bool
    {
        return false;
    }

    public static function getGlobalClassName(): string
    {
        throw new LogicException('Method getGlobalClassName() must be implemented.');
    }

    public static function globalExtendFilterName(): string
    {
        throw new LogicException('Method globalExtendFilterName() must be implemented.');
    }

    public static function extend(callable|Closure $callback, int $priority = 100): void
    {
        if (static::hasGlobalExtend() && static::class === static::getGlobalClassName()) {
            add_action(static::globalExtendFilterName(), $callback, $priority);

            return;
        }

        add_action(static::getFilterPrefix() . '_extended', $callback, $priority);
    }

    protected function setupExtended(): void
    {
        if (static::hasGlobalExtend()) {
            do_action(static::globalExtendFilterName(), $this);
        }

        do_action(static::getFilterPrefix() . '_extended', $this);
    }
}
