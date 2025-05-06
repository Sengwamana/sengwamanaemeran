<?php

namespace Botble\Base\Enums;

use BadMethodCallException;
use Botble\Base\Supports\Enum;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @method static SystemUpdaterStepEnum DOWNLOAD()
 * @method static SystemUpdaterStepEnum UPDATE_FILES()
 * @method static SystemUpdaterStepEnum UPDATE_DATABASE()
 * @method static SystemUpdaterStepEnum PUBLISH_CORE_ASSETS()
 * @method static SystemUpdaterStepEnum PUBLISH_PACKAGES_ASSETS()
 * @method static SystemUpdaterStepEnum CLEAN_UP()
 * @method static SystemUpdaterStepEnum DONE()
 */
class SystemUpdaterStepEnum extends Enum
{
    public const DOWNLOAD = 'download';
    public const UPDATE_FILES = 'update_files';
    public const UPDATE_DATABASE = 'update_database';
    public const PUBLISH_CORE_ASSETS = 'publish_core_assets';
    public const PUBLISH_PACKAGES_ASSETS = 'publish_packages_assets';
    public const CLEAN_UP = 'clean_up';
    public const DONE = 'done';

    public static $langPath = 'core/base::enums.system_updater_steps';

    public static function firstStep(): string
    {
        return self::DOWNLOAD;
    }

    public static function lastStep(): string
    {
        return self::DONE;
    }

    public function message(): string
    {
        $key = sprintf(
            '%s.messages.%s',
            static::$langPath,
            $value = $this->value
        );

        return Lang::has($key) ? trans($key) : $value;
    }

    public function successMessage(): string
    {
        $key = sprintf(
            '%s.success_messages.%s',
            static::$langPath,
            $value = $this->value
        );

        return Lang::has($key) ? trans($key) : $value;
    }

    public function nextStep(): string
    {
        return match ($this->value) {
            self::DOWNLOAD => self::UPDATE_FILES,
            self::UPDATE_FILES => self::UPDATE_DATABASE,
            self::UPDATE_DATABASE => self::PUBLISH_CORE_ASSETS,
            self::PUBLISH_CORE_ASSETS => self::PUBLISH_PACKAGES_ASSETS,
            self::PUBLISH_PACKAGES_ASSETS => self::CLEAN_UP,
            self::CLEAN_UP => self::DONE,
            default => throw new InvalidArgumentException('Invalid step'),
        };
    }

    public function nextStepMessage(): string
    {
        return match ($this->value) {
            self::DOWNLOAD => self::UPDATE_FILES()->message(),
            self::UPDATE_FILES => self::UPDATE_DATABASE()->message(),
            self::UPDATE_DATABASE => self::PUBLISH_CORE_ASSETS()->message(),
            self::PUBLISH_CORE_ASSETS => self::PUBLISH_PACKAGES_ASSETS()->message(),
            self::PUBLISH_PACKAGES_ASSETS => self::CLEAN_UP()->message(),
            self::CLEAN_UP => self::DONE()->message(),
            default => trans('core/base::enums.system_updater_steps.unknown'),
        };
    }

    public function currentPercent(): int
    {
        return match ($this->value) {
            self::DOWNLOAD => 15,
            self::UPDATE_FILES => 25,
            self::UPDATE_DATABASE => 65,
            self::PUBLISH_CORE_ASSETS => 70,
            self::PUBLISH_PACKAGES_ASSETS => 85,
            self::CLEAN_UP => 100,
            default => 0,
        };
    }

    public static function tryFrom($step): ?self
    {
        try {
            return call_user_func([static::class, Str::upper(Str::snake($step))]);
        } catch (BadMethodCallException) {
            return null;
        }
    }
}
