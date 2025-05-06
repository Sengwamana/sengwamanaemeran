<?php

namespace Botble\Slug;

use Botble\Base\Contracts\BaseModel;
use Botble\Page\Models\Page;
use Botble\Slug\Models\Slug;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SlugHelper
{
    protected array $canEmptyPrefixes = [Page::class];

    protected array $registering = [];

    protected array $supportedModels = [];

    protected array $prefixes = [];

    public function __construct(protected SlugCompiler $translator)
    {
        $this->supportedModels[Page::class] = fn () => trans('packages/page::pages.pages');
    }

    public function registerModule(string|array $model, string|null|Closure $name = null): self
    {
        foreach ((array) $model as $item) {
            $this->supportedModels[$item] = $name ?: $item;
        }

        return $this;
    }

    public function registering(Closure $callback): static
    {
        $this->registering[] = $callback;

        return $this;
    }

    protected function dispatchRegistering(): void
    {
        if (empty($this->registering)) {
            return;
        }

        foreach ($this->registering as $callback) {
            call_user_func($callback, $this);
        }
    }

    public function removeModule(string|array $model): self
    {
        foreach ((array) $model as $item) {
            unset($this->supportedModels[$item]);
        }

        return $this;
    }

    public function supportedModels(): array
    {
        $this->dispatchRegistering();

        return array_map(function ($name) {
            return is_callable($name) ? $name() : $name;
        }, $this->supportedModels);
    }

    public function setPrefix(string $model, ?string $prefix, bool $canEmptyPrefix = false): self
    {
        $this->prefixes[$model] = $prefix;

        if ($canEmptyPrefix) {
            $this->canEmptyPrefixes[] = $model;
        }

        return $this;
    }

    public function setColumnUsedForSlugGenerator(string $model, string $column): self
    {
        $columns = config('packages.slug.general.slug_generated_columns', []);
        $columns[$model] = $column;

        config(['packages.slug.general.slug_generated_columns' => $columns]);

        return $this;
    }

    public function isSupportedModel(string $model): bool
    {
        return in_array($model, array_keys($this->supportedModels()));
    }

    public function disablePreview(array|string $model): self
    {
        if (! is_array($model)) {
            $model = [$model];
        }

        config([
            'packages.slug.general.disable_preview' => array_merge(
                config('packages.slug.general.disable_preview', []),
                $model
            ),
        ]);

        return $this;
    }

    public function canPreview(string $model): bool
    {
        return ! in_array($model, config('packages.slug.general.disable_preview', []));
    }

    public function createSlug(BaseModel $model, string $name = null): BaseModel|Slug
    {
        /**
         * @var Slug $slug
         */
        $slug = Slug::query()->firstOrNew([
            'reference_type' => $model::class,
            'reference_id' => $model->getKey(),
            'prefix' => $this->getPrefix($model::class),
        ]);

        if ($this->turnOffAutomaticUrlTranslationIntoLatin()) {
            $slug->key = $name ?: $model->{$this->getColumnNameToGenerateSlug($model::class)};
        } else {
            $slug->key = Str::slug($name ?: $model->{$this->getColumnNameToGenerateSlug($model::class)});
        }

        $slug->ensureIdCanBeCreated();

        $slug->saveQuietly();

        return $slug;
    }

    public function getSlug(
        ?string $key,
        ?string $prefix = null,
        ?string $model = null,
        $referenceId = null
    ) {
        $condition = [];

        $extension = $this->getPublicSingleEndingURL();

        if ($key !== null) {
            $condition = ['key' => $key];

            if (! empty($extension)) {
                $condition = ['key' => Str::replaceLast($extension, '', $key)];
            }
        }

        if ($model !== null) {
            $condition['reference_type'] = $model;
        }

        if ($referenceId !== null) {
            $condition['reference_id'] = $referenceId;
        }

        if ($prefix !== null) {
            $condition['prefix'] = $prefix;
        }

        $query = apply_filters(
            'slug_helper_get_slug_query',
            Slug::query()->where($condition),
            $condition,
            (string) $key,
            (string) $prefix,
            $model
        );

        return $query->first();
    }

    public function getPrefix(string $model, string $default = '', bool $translate = true): ?string
    {
        $prefix = setting($this->getPermalinkSettingKey($model));

        if ($prefix === null) {
            $this->dispatchRegistering();

            $prefix = Arr::get($this->prefixes, $model);
        }

        if ($prefix !== null) {
            if ($translate) {
                $prefix = $this->translator->compile($prefix, $model);
            }

            $default = $prefix;
        }

        return $default;
    }

    public function getHelperTextForPrefix(string $model, string $default = '/', bool $translate = true): string
    {
        return $this->getHelperText(
            $this->getPrefix($model, $default, $translate) ?: '',
            Str::slug('Your URL Here'),
            '/'
        );
    }

    public function getHelperText(string $prefix, ?string $postfix = '', ?string $separation = ''): string
    {
        $url = ($prefix ? $prefix . $separation : '') . $postfix;
        $url = ltrim(str_replace('//', '/', $url), '/');

        return trans('packages/slug::slug.settings.helper_text', [
            'url' => sprintf('<a href="javascript:void(0)">%s</a>', url($url)),
        ]);
    }

    public function getColumnNameToGenerateSlug(array|string|object|null $model): ?string
    {
        if (! $model) {
            return null;
        }

        if (is_object($model)) {
            $model = get_class($model);
        }

        $config = Arr::get(config('packages.slug.general.slug_generated_columns', []), $model);

        if ($config !== null) {
            return (string) $config;
        }

        return 'name';
    }

    public function getPermalinkSettingKey(string $model): string
    {
        return $this->getSettingKey('permalink-' . Str::slug(str_replace('\\', '_', $model)));
    }

    public function turnOffAutomaticUrlTranslationIntoLatin(): bool
    {
        return setting($this->getSettingKey('slug_turn_off_automatic_url_translation_into_latin'), 0) == 1;
    }

    public function getPublicSingleEndingURL(): ?string
    {
        $endingURL = setting($this->getSettingKey('public_single_ending_url'), config('packages.theme.general.public_single_ending_url'));

        return ! empty($endingURL) ? '.' . $endingURL : null;
    }

    public function getSettingKey(string $key): string
    {
        return apply_filters('slug_helper_get_permalink_setting_key', $key);
    }

    public function getCanEmptyPrefixes(): array
    {
        return $this->canEmptyPrefixes;
    }

    public function getTranslator(): SlugCompiler
    {
        return $this->translator;
    }

    public function getSlugPrefixes(): array
    {
        $prefixes = [];

        foreach ($this->supportedModels() as $class => $model) {
            $prefixes[] = addslashes($this->getPrefix($class, translate: false));
        }

        return array_values(array_filter($prefixes));
    }

    public function getAllPrefixes(): array
    {
        $allSettingPrefixes = collect(setting()->all())
            ->filter(function ($value, $key) {
                return $value && Str::startsWith($key, 'permalink-');
            })
            ->all();

        $prefixes = [];

        foreach ($this->supportedModels() as $class => $model) {
            $normalizeModel = Str::slug(str_replace('\\', '_', $class));

            foreach ($allSettingPrefixes as $key => $value) {
                if (! Str::startsWith($key, 'permalink-' . $normalizeModel)) {
                    continue;
                }

                $prefixes[] = $value;

                unset($allSettingPrefixes[$key]);
            }

            $prefixes[] =  Arr::get($this->prefixes, $class);
        }

        return array_unique(array_filter($prefixes ?: []));
    }
}
