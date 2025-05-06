<?php

namespace Botble\Base\Helpers;

use Botble\Base\Facades\AdminAppearance;
use Botble\Base\Facades\Html;
use Botble\Base\Supports\GoogleFonts;
use Botble\Base\View\Components\BadgeComponent;
use Botble\Icon\Facades\Icon as IconFacade;
use Botble\Icon\View\Components\Icon;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Throwable;

class BaseHelper
{
    public function formatTime(CarbonInterface $timestamp, ?string $format = 'j M Y H:i', bool $translated = false): string
    {
        $first = Carbon::create(0000, 0, 0, 00, 00, 00);

        if ($timestamp->lte($first)) {
            return '';
        }

        if ($translated) {
            return $timestamp->translatedFormat($format);
        }

        return $timestamp->format($format);
    }

    public function formatDate(CarbonInterface|int|string|null $date, ?string $format = null, bool $translated = false): ?string
    {
        if (empty($format)) {
            $format = $this->getDateFormat();
        }

        if (empty($date)) {
            return $date;
        }

        if (! $date instanceof CarbonInterface) {
            $date = Carbon::parse($date);
        }

        return $this->formatTime($date, $format, $translated);
    }

    public function formatDateTime(CarbonInterface|int|string|null $date, ?string $format = null, bool $translated = false): ?string
    {
        if (empty($format)) {
            $format = $this->getDateTimeFormat();
        }

        return $this->formatDate($date, $format, $translated);
    }

    public function humanFilesize(float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return number_format($bytes, $precision, ',', '.') . ' ' . $units[$pow];
    }

    public function getFileData(string $file, bool $convertToArray = true)
    {
        $file = File::get($file);
        if (! empty($file)) {
            if ($convertToArray) {
                return json_decode($file, true);
            }

            return $file;
        }

        if (! $convertToArray) {
            return null;
        }

        return [];
    }

    public function saveFileData(string $path, array|string|null $data, bool $json = true): bool
    {
        try {
            if ($json) {
                $data = $this->jsonEncodePrettify($data);
            }

            File::ensureDirectoryExists(File::dirname($path));

            File::put($path, $data);

            return true;
        } catch (Throwable $throwable) {
            $this->logError($throwable);

            return false;
        }
    }

    public function jsonEncodePrettify(array|string|null $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }

    public function scanFolder(string $path, array $ignoreFiles = []): array
    {
        if (! $path) {
            return [];
        }

        if (File::isDirectory($path)) {
            $data = array_diff(scandir($path), array_merge(['.', '..', '.DS_Store'], $ignoreFiles));
            natsort($data);

            return $data;
        }

        return [];
    }

    public function getAdminPrefix(): string
    {
        $prefix = config('core.base.general.admin_dir');

        if (! $prefix && class_exists('Theme')) {
            return 'admin';
        }

        return $prefix;
    }

    public function getAdminMasterLayoutTemplate(): string
    {
        $layout = 'core/base::layouts.empty';

        if (Auth::check()) {
            $layout = 'core/base::layouts.master';
        }

        return apply_filters('base_filter_admin_master_layout_template', $layout);
    }

    public function siteLanguageDirection(): string
    {
        return apply_filters(BASE_FILTER_SITE_LANGUAGE_DIRECTION, setting('locale_direction', 'ltr'));
    }

    public function isRtlEnabled(): bool
    {
        return $this->siteLanguageDirection() == 'rtl';
    }

    public function adminLanguageDirection(): string
    {
        return apply_filters(BASE_FILTER_ADMIN_LANGUAGE_DIRECTION, AdminAppearance::forCurrentUser()->getLocaleDirection());
    }

    public function isHomepage(int|string|null $pageId = null): bool
    {
        $homepageId = $this->getHomepageId();

        return $pageId && $homepageId && $pageId == $homepageId;
    }

    public function getHomepageId(): ?string
    {
        if (! function_exists('theme_option')) {
            return null;
        }

        return theme_option('homepage_id', setting('show_on_front'));
    }

    /**
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder  $query
     */
    public function isJoined($query, string $table): bool
    {
        $joins = $query->getQuery()->joins;

        if ($joins == null) {
            return false;
        }

        foreach ($joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }

        return false;
    }

    public function availableRichEditors(): array
    {
        return apply_filters(BASE_FILTER_AVAILABLE_EDITORS, [
            'ckeditor' => 'CKEditor',
            'tinymce' => 'TinyMCE',
        ]);
    }

    public function getRichEditor(): string
    {
        $richEditor = setting('rich_editor', config('core.base.general.editor.primary'));

        if (array_key_exists($richEditor, $this->availableRichEditors())) {
            return $richEditor;
        }

        setting()->set(['rich_editor' => 'ckeditor'])->save();

        return 'ckeditor';
    }

    public function removeQueryStringVars(?string $url, array|string $key): ?string
    {
        if (! is_array($key)) {
            $key = [$key];
        }

        foreach ($key as $item) {
            $url = preg_replace('/(.*)(?|&)' . $item . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
            $url = substr($url, 0, -1);
        }

        return $url;
    }

    public function cleanEditorContent(?string $value): string
    {
        if (! $value) {
            return '';
        }

        $value = str_replace('<span class="style-scope yt-formatted-string" dir="auto">', '', $value);

        return htmlentities($this->clean($value));
    }

    public function getPhoneValidationRule(bool $asArray = false): string|array
    {
        $rule = config('core.base.general.phone_validation_rule');

        if ($asArray) {
            return explode('|', $rule);
        }

        return $rule;
    }

    public function getZipcodeValidationRule(bool $asArray = false): string|array
    {
        $rule = config('core.base.general.zipcode_validation_rule');

        if ($asArray) {
            return explode('|', $rule);
        }

        return $rule;
    }

    public function sortSearchResults(array|Collection $collection, string $searchTerms, string $column): Collection
    {
        if (! $collection instanceof Collection) {
            $collection = collect($collection);
        }

        return $collection->sortByDesc(function ($item) use ($searchTerms, $column) {
            $searchTerms = explode(' ', $searchTerms);

            // The bigger the weight, the higher the record
            $weight = 0;

            // Iterate through search terms
            foreach ($searchTerms as $term) {
                if (str_contains($item->{$column}, $term)) {
                    // Increase weight if the search term is found
                    $weight += 1;
                }
            }

            return $weight;
        });
    }

    public function getDateFormats(): array
    {
        $formats = [
            'Y-m-d',
            'Y-M-d',
            'y-m-d',
            'm-d-Y',
            'M-d-Y',
        ];

        foreach ($formats as $format) {
            $formats[] = str_replace('-', '/', $format);
        }

        $formats[] = 'M d, Y';

        return $formats;
    }

    public function clean(array|string|null $dirty, array|string|null $config = null): array|string|null
    {
        if (config('core.base.general.enable_less_secure_web', false)) {
            return $dirty;
        }

        if (! $dirty && $dirty !== null) {
            return $dirty;
        }

        if (! is_numeric($dirty)) {
            $dirty = (string) $dirty;
        }

        return clean($dirty, $config);
    }

    public function html(array|string|null $dirty, array|string|null $config = null): HtmlString
    {
        return new HtmlString((string) $this->clean($dirty, $config));
    }

    public function hexToRgba(string $color, float $opacity = 1): string
    {
        $rgb = implode(',', $this->hexToRgb($color));

        if ($opacity == 1) {
            return 'rgb(' . $rgb . ')';
        }

        return 'rgba(' . $rgb . ', ' . $opacity . ')';
    }

    public function hexToRgb(string $color): array
    {
        $color = Str::lower($color);

        if (Str::startsWith($color, 'rgb')) {
            $color = str_replace('rgb(', '', $color);
            $color = str_replace(')', '', $color);
            $color = str_replace(' ', '', $color);

            [$red, $green, $blue] = explode(',', $color);

            return compact('red', 'green', 'blue');
        }

        [$red, $green, $blue] = sscanf($color, '#%02x%02x%02x');

        $blue = $blue === null ? 0 : $blue;

        return compact('red', 'green', 'blue');
    }

    public function iniSet(string $key, int|string|null $value): self
    {
        if (config('core.base.general.enable_ini_set', true)) {
            @ini_set($key, $value);
        }

        return $this;
    }

    public function maximumExecutionTimeAndMemoryLimit(): self
    {
        $this->iniSet('max_execution_time', -1);
        $this->iniSet('memory_limit', -1);

        return $this;
    }

    public function removeSpecialCharacters(?string $string): array|string|null
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

    public function getInputValueFromQueryString(string $name): string
    {
        $value = request()->input($name);

        if (! is_string($value)) {
            return '';
        }

        return $value;
    }

    public function cleanShortcodes(?string $content): ?string
    {
        if (! $content) {
            return $content;
        }

        $content = $this->clean($content);

        if (! function_exists('shortcode')) {
            return $content;
        }

        $shortcodeCompiler = shortcode()->getCompiler();

        return $shortcodeCompiler->strip($content);
    }

    public function stringify($content): ?string
    {
        if (empty($content)) {
            return null;
        }

        if (is_string($content) || is_numeric($content) || is_bool($content)) {
            return $content;
        }

        if (is_array($content)) {
            return json_encode($content);
        }

        return null;
    }

    public function getGoogleFontsURL(?string $path = null): string
    {
        $url = config('core.base.general.google_fonts_url', 'https://fonts.bunny.net');

        if (! $path) {
            return $url;
        }

        return $url . '/' . ltrim($path, '/');
    }

    public function googleFonts(string $font, bool $inline = true)
    {
        if (! config('core.base.general.google_fonts_enabled', true)) {
            return '';
        }

        $directlyUrl = Html::style(str_replace('https://fonts.googleapis.com', $this->getGoogleFontsURL(), $font));

        if (! config('core.base.general.google_fonts_enabled_cache', true)) {
            return $directlyUrl;
        }

        try {
            $fontUrl = str_replace($this->getGoogleFontsURL(), 'https://fonts.googleapis.com', $font);

            $googleFont = app('core.google-fonts')->load($fontUrl);

            if (! $googleFont) {
                return $directlyUrl;
            }

            if (! $inline) {
                return $googleFont->link();
            }

            return $googleFont->toHtml();
        } catch (Throwable) {
            return $directlyUrl;
        }
    }

    /**
     * @deprecated
     */
    public function routeIdRegex(): ?string
    {
        return '[0-9]+';
    }

    public function hasDemoModeEnabled(): bool
    {
        return App::environment('demo') || config('core.base.general.demo_mode_enabled', false);
    }

    public function logError(Throwable $throwable): void
    {
        logger()->error($throwable->getMessage() . ' - ' . $throwable->getFile() . ':' . $throwable->getLine());
    }

    public function getDateFormat(): string
    {
        return config('core.base.general.date_format.date');
    }

    public function getDateTimeFormat(): string
    {
        return config('core.base.general.date_format.date_time');
    }

    public function joinPaths(array $paths): string
    {
        return implode(DIRECTORY_SEPARATOR, $paths);
    }

    public function hasIcon(?string $name): bool
    {
        if (! $name) {
            return false;
        }

        return IconFacade::has($name);
    }

    public function renderIcon(string $name, ?string $size = null, array $attributes = [], bool $safe = false): string
    {
        if ($safe && ! $this->hasIcon($name)) {
            return '';
        }

        return Blade::renderComponent(
            (new Icon($name, $size))->withAttributes($attributes)
        );
    }

    public function renderBadge(?string $label, string $color = 'primary', array $attributes = [], ?string $icon = null): string
    {
        if (! $label) {
            return '';
        }

        return Blade::renderComponent(
            (new BadgeComponent($label, $color, $icon))->withAttributes($attributes)
        );
    }

    public function cleanToastMessage(string $message): string
    {
        if (Str::startsWith($message, ['http://', 'https://'])) {
            return $message;
        }

        return addslashes($message);
    }

    public function getHomepageUrl()
    {
        return apply_filters('cms_homepage_url', rescue(fn () => route('public.index'), report: false));
    }

    public function getFonts(): array
    {
        $customGoogleFonts = config('core.base.general.custom_google_fonts');
        $customFonts = apply_filters('cms_custom_fonts', config('core.base.general.custom_fonts', []) ?: []);

        if (! empty($customGoogleFonts) && ! is_array($customGoogleFonts)) {
            $customGoogleFonts = array_filter(explode(',', $customGoogleFonts));
        }

        if (! empty($customFonts) && ! is_array($customFonts)) {
            $customFonts = array_filter(explode(',', $customFonts));
        }

        return [
            ...GoogleFonts::getFonts(),
            ...(! empty($customGoogleFonts) ? $customGoogleFonts : []),
            ...(! empty($customFonts) ? $customFonts : []),
        ];
    }
}
