<?php

namespace Botble\ACL\Models;

use Botble\Base\Models\BaseModel;
use Botble\Support\Services\Cache\Cache;
use Illuminate\Support\Facades\Auth;

class UserMeta extends BaseModel
{
    protected $table = 'user_meta';

    protected $fillable = [
        'key',
        'value',
        'user_id',
    ];

    public static function setMeta(string $key, $value = null, int|string $userId = 0): bool
    {
        if ($userId == 0) {
            $userId = Auth::guard()->id();
        }

        $meta = self::query()->firstOrCreate([
            'user_id' => $userId,
            'key' => $key,
        ]);

        return $meta->update(['value' => $value]);
    }

    public static function getMeta(string $key, $default = null, int|string $userId = 0): ?string
    {
        if ($userId == 0) {
            $userId = Auth::guard()->id();
        }

        $meta = self::query()
            ->where([
                'user_id' => $userId,
                'key' => $key,
            ])
            ->select('value')
            ->first();

        if (! empty($meta)) {
            return $meta->value;
        }

        return $default;
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::make(static::class)->flush();
        });

        static::deleted(function (): void {
            Cache::make(static::class)->flush();
        });
    }
}
