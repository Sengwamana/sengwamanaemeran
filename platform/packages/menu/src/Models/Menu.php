<?php

namespace Botble\Menu\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Base\Models\Concerns\HasSlug;
use Botble\Support\Services\Cache\Cache;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends BaseModel
{
    use HasSlug;

    protected $table = 'menus';

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
    ];

    protected static function booted(): void
    {
        static::deleted(function (self $model): void {
            $model->menuNodes()->delete();
            $model->locations()->delete();
        });

        static::saving(function (self $model): void {
            if (! $model->slug) {
                $model->slug = self::createSlug($model->name, $model->getKey());
            }
        });

        static::saved(function (): void {
            Cache::make(static::class)->flush();
        });

        static::deleted(function (): void {
            Cache::make(static::class)->flush();
        });
    }

    public function menuNodes(): HasMany
    {
        return $this->hasMany(MenuNode::class, 'menu_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(MenuLocation::class, 'menu_id');
    }
}
