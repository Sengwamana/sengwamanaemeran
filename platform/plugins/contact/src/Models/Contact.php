<?php

namespace Botble\Contact\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\Avatar;
use Botble\Contact\Enums\ContactStatusEnum;
use Botble\Media\Facades\RvMedia;
use Botble\Support\Services\Cache\Cache;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Throwable;

class Contact extends BaseModel
{
    protected $table = 'contacts';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'subject',
        'content',
        'status',
        'custom_fields',
    ];

    protected $casts = [
        'status' => ContactStatusEnum::class,
        'name' => SafeContent::class,
        'address' => SafeContent::class,
        'subject' => SafeContent::class,
        'content' => SafeContent::class,
        'custom_fields' => 'array',
    ];

    public function replies(): HasMany
    {
        return $this->hasMany(ContactReply::class);
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            try {
                return Avatar::createBase64Image($this->name);
            } catch (Throwable) {
                return RvMedia::getDefaultImage();
            }
        });
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
