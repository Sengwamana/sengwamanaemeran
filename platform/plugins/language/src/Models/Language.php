<?php

namespace Botble\Language\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Setting\Facades\Setting;
use Botble\Theme\Facades\ThemeOption;
use Botble\Widget\Models\Widget;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends BaseModel
{
    public $timestamps = false;

    protected $primaryKey = 'lang_id';

    protected $table = 'languages';

    protected $fillable = [
        'lang_name',
        'lang_locale',
        'lang_is_default',
        'lang_code',
        'lang_is_rtl',
        'lang_flag',
        'lang_order',
    ];

    protected $casts = [
        'lang_name' => SafeContent::class,
        'lang_locale' => SafeContent::class,
        'lang_code' => SafeContent::class,
        'lang_is_rtl' => 'bool',
        'lang_is_default' => 'bool',
        'lang_order' => 'int',
    ];

    protected static function booted(): void
    {
        self::deleted(function (Language $language): void {
            if (! self::query()->where('lang_is_default', 1)->exists() && self::query()->exists()) {
                self::query()->limit(1)->update(['lang_is_default' => 1]);
            }

            $language->meta()->each(fn (LanguageMeta $item) => $item->delete());

            Setting::newQuery()->where('key', 'LIKE', ThemeOption::getOptionKey('%', $language->lang_code))->delete();
            Widget::query()->where('theme', 'LIKE', Widget::getThemeName($language->lang_code))->delete();
        });
    }

    public function meta(): HasMany
    {
        return $this->hasMany(LanguageMeta::class, 'lang_meta_code', 'lang_code');
    }
}
