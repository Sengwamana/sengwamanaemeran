<?php

namespace Botble\Slug\Facades;

use Botble\Slug\SlugHelper as BaseSlugHelper;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\Slug\SlugHelper registerModule(array|string $model, \Closure|string|null $name = null)
 * @method static static registering(\Closure $callback)
 * @method static \Botble\Slug\SlugHelper removeModule(array|string $model)
 * @method static array supportedModels()
 * @method static \Botble\Slug\SlugHelper setPrefix(string $model, string|null $prefix, bool $canEmptyPrefix = false)
 * @method static \Botble\Slug\SlugHelper setColumnUsedForSlugGenerator(string $model, string $column)
 * @method static bool isSupportedModel(string $model)
 * @method static \Botble\Slug\SlugHelper disablePreview(array|string $model)
 * @method static bool canPreview(string $model)
 * @method static \Botble\Base\Contracts\BaseModel|\Botble\Slug\Models\Slug createSlug(\Botble\Base\Contracts\BaseModel $model, string|null $name = null)
 * @method static mixed getSlug(string|null $key, string|null $prefix = null, string|null $model = null, $referenceId = null)
 * @method static string|null getPrefix(string $model, string $default = '', bool $translate = true)
 * @method static string getHelperTextForPrefix(string $model, string $default = '/', bool $translate = true)
 * @method static string getHelperText(string $prefix, string|null $postfix = '', string|null $separation = '')
 * @method static string|null getColumnNameToGenerateSlug(object|array|string|null $model)
 * @method static string getPermalinkSettingKey(string $model)
 * @method static bool turnOffAutomaticUrlTranslationIntoLatin()
 * @method static string|null getPublicSingleEndingURL()
 * @method static string getSettingKey(string $key)
 * @method static array getCanEmptyPrefixes()
 * @method static \Botble\Slug\SlugCompiler getTranslator()
 * @method static array getSlugPrefixes()
 * @method static array getAllPrefixes()
 *
 * @see \Botble\Slug\SlugHelper
 */
class SlugHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseSlugHelper::class;
    }
}
