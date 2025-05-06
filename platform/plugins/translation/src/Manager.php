<?php

namespace Botble\Translation;

use ArrayAccess;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Services\DeleteLocaleService;
use Botble\Base\Services\DeleteUnusedTranslationFilesService;
use Botble\Base\Services\DownloadLocaleService;
use Botble\Base\Supports\Language;
use Botble\Base\Supports\ServiceProvider;
use Botble\Theme\Facades\Theme;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\VarExporter\VarExporter;
use Throwable;

class Manager
{
    protected array|ArrayAccess $config;

    protected DownloadLocaleService $downloadLocaleService;

    protected DeleteLocaleService $deleteLocaleService;

    protected DeleteUnusedTranslationFilesService $deleteUnusedTranslationFilesService;

    public function __construct(protected Application $app, protected Filesystem $files)
    {
        $this->config = $app['config']['plugins.translation.general'];

        $this->downloadLocaleService = new DownloadLocaleService();
        $this->deleteUnusedTranslationFilesService = new DeleteUnusedTranslationFilesService();
        $this->deleteLocaleService = new DeleteLocaleService();
    }

    public function publishLocales(): void
    {
        $this->files->ensureDirectoryExists(lang_path('vendor/themes'));

        $paths = ServiceProvider::pathsToPublish(null, 'cms-lang');

        foreach ($paths as $from => $to) {
            $this->files->ensureDirectoryExists(dirname($to));
            $this->files->copyDirectory($from, $to);
        }

        if (! $this->files->isDirectory(lang_path('en')) || $this->files->isEmptyDirectory(lang_path('en'))) {
            $this->downloadRemoteLocale('en');
        }
    }

    public function updateTranslation(string $locale, string $group, string|array $key, ?string $value = null): void
    {
        $loader = Lang::getLoader();

        $translationsArray = is_array($key) ? $key : [$key => $value];

        if (str_contains($group, DIRECTORY_SEPARATOR)) {
            $englishTranslations = $loader->load('en', Str::afterLast($group, DIRECTORY_SEPARATOR), Str::beforeLast($group, DIRECTORY_SEPARATOR));
            $translations = $loader->load($locale, Str::afterLast($group, DIRECTORY_SEPARATOR), Str::beforeLast($group, DIRECTORY_SEPARATOR));
        } else {
            $englishTranslations = $loader->load('en', $group);
            $translations = $loader->load($locale, $group);
        }

        foreach ($translationsArray as $transKey => $transValue) {
            Arr::set($translations, $transKey, $transValue);
        }

        $translations = array_merge($englishTranslations, $translations);

        $file = $locale . DIRECTORY_SEPARATOR . $group;

        File::ensureDirectoryExists(lang_path($locale));

        $groups = explode(DIRECTORY_SEPARATOR, $group);
        if (count($groups) > 1) {
            $folderName = Arr::last($groups);
            Arr::forget($groups, count($groups) - 1);

            $dir = 'vendor' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $groups) . DIRECTORY_SEPARATOR . $locale;
            File::ensureDirectoryExists(lang_path($dir));

            $file = $dir . DIRECTORY_SEPARATOR . $folderName;
        }

        $path = lang_path($file . '.php');
        $output = "<?php\n\nreturn " . VarExporter::export($translations) . ";\n";

        File::put(str_replace('/', DIRECTORY_SEPARATOR, $path), $output);
    }

    public function getConfig(?string $key = null): string|array|null
    {
        if ($key == null) {
            return $this->config;
        }

        return $this->config[$key];
    }

    public function removeUnusedThemeTranslations(): bool
    {
        if (Theme::hasInheritTheme()) {
            $this->removeUnusedThemeTranslationsFromTheme(Theme::getInheritTheme());
        }

        $this->removeUnusedThemeTranslationsFromTheme(Theme::getThemeName());

        return true;
    }

    public function removeUnusedThemeTranslationsFromTheme(string $theme): bool
    {
        $themePath = lang_path("vendor/themes/$theme");

        if (! $this->files->isDirectory($themePath)) {
            return true;
        }

        foreach ($this->files->allFiles($themePath) as $file) {
            if ($this->files->isFile($file) && $file->getExtension() === 'json') {
                $locale = $file->getFilenameWithoutExtension();

                if ($locale == 'en') {
                    continue;
                }

                $translations = BaseHelper::getFileData($file->getRealPath());

                $defaultEnglishFile = theme_path("$theme/lang/en.json");

                if (! File::exists($defaultEnglishFile)) {
                    $this->updateThemeTranslations();
                }

                if (File::exists($defaultEnglishFile)) {
                    $enTranslations = BaseHelper::getFileData($defaultEnglishFile);
                    $translations = [
                        ...$enTranslations,
                        ...$translations,
                    ];

                    foreach ($translations as $key => $translation) {
                        if (! array_key_exists($key, $enTranslations)) {
                            Arr::forget($translations, $key);
                        }
                    }
                }

                ksort($translations);

                $this->files->put(
                    $file->getRealPath(),
                    json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                );
            }
        }

        return true;
    }

    public function downloadLocaleIfMissing(string $locale): void
    {
        if (! array_key_exists($locale, Language::getAvailableLocales())) {
            $this->downloadRemoteLocale($locale);
        }
    }

    public function getRemoteAvailableLocales(): array
    {
        return $this->downloadLocaleService->getAvailableLocales();
    }

    public function downloadRemoteLocale(string $locale): array
    {
        $this->ensureAllDirectoriesAreCreated();

        try {
            $this->downloadLocaleService->handle($locale);
        } catch (Throwable $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->deleteUnusedTranslationFilesService->handle();

        $this->removeUnusedThemeTranslations();

        return [
            'error' => false,
            'message' => 'Downloaded translation files!',
        ];
    }

    public function getThemeTranslations(string $locale, bool $withInherit = true): array
    {
        $translations = $withInherit ? $this->getInheritThemeTranslations($locale) : [];

        $currentTranslationFilePath = $this->getThemeTranslationPath($locale);

        if (File::exists($currentTranslationFilePath)) {
            $translations = [
                ...$translations,
                ...BaseHelper::getFileData($currentTranslationFilePath),
            ];
        }

        ksort($translations);

        $translations = $this->getThemeTranslationsFromThemeWithInherit($translations, $locale, $withInherit);

        return array_combine(array_map('trim', array_keys($translations)), $translations);
    }

    public function getInheritThemeTranslations(string $locale): array
    {
        if (! Theme::hasInheritTheme()) {
            return [];
        }

        $originalInheritTranslations = $this->getThemeTranslationsFromTheme(Theme::getInheritTheme(), $locale);

        $translations = BaseHelper::getFileData($this->getThemeTranslationPath($locale, Theme::getInheritTheme()));

        return [...$originalInheritTranslations, ...$translations];
    }

    public function getThemeTranslationsFromTheme(string $theme, string $locale): array
    {
        $themeTranslationsFilePath = $this->getThemeTranslationPath($locale);
        $defaultEnglishFile = theme_path(BaseHelper::joinPaths([$theme, 'lang', 'en.json']));

        if (File::exists($defaultEnglishFile)
            && ($locale !== 'en' || $defaultEnglishFile !== $themeTranslationsFilePath)) {
            return BaseHelper::getFileData($defaultEnglishFile);
        }

        return [];
    }

    public function getThemeTranslationsFromThemeWithInherit(
        array $translations,
        string $locale,
        bool $withInherit = true
    ): array {
        $enTranslations = [];

        if ($withInherit && Theme::hasInheritTheme()) {
            $enTranslations = $this->getThemeTranslationsFromTheme(Theme::getInheritTheme(), $locale);
        }

        $enTranslations = [
            ...$enTranslations,
            ...$this->getThemeTranslationsFromTheme(Theme::getThemeName(), $locale),
        ];
        $translations = [...$enTranslations, ...$translations];
        $enTranslationKeys = array_keys($enTranslations);

        foreach ($translations as $key => $translation) {
            if (! in_array($key, $enTranslationKeys)) {
                Arr::forget($translations, $key);
            }
        }

        return $translations;
    }

    public function getThemeTranslationPath(string $locale, ?string $theme = ''): string
    {
        $theme = $theme ?: Theme::getThemeName();

        $localeFilePath = $defaultLocaleFilePath = lang_path("vendor/themes/$theme/$locale.json");

        if (! File::exists($localeFilePath)) {
            $localeFilePath = lang_path("$locale.json");
        }

        if (! File::exists($localeFilePath)) {
            $localeFilePath = $defaultLocaleFilePath;

            $themeLangPath = theme_path("$theme/lang/$locale.json");

            if (! File::exists($themeLangPath)) {
                $themeLangPath = theme_path("$theme/lang/en.json");
            }

            if (File::exists($themeLangPath)) {

                try {
                    File::ensureDirectoryExists(dirname($localeFilePath));
                } catch (Throwable) {
                    throw new Exception(trans('plugins/translation::translation.folder_is_not_writeable', ['lang_path' => lang_path()]));
                }

                File::copy($themeLangPath, $localeFilePath);
            }
        }

        return $localeFilePath;
    }

    public function saveThemeTranslations(string $locale, array $translations, ?string $theme = null): bool
    {
        $theme = $theme ?: Theme::getThemeName();

        ksort($translations);

        return BaseHelper::saveFileData($this->getThemeTranslationPath($locale, $theme), $translations);
    }

    public function saveInheritThemeTranslation(string $locale, array $translations): bool
    {
        return $this->saveThemeTranslations($locale, $translations, Theme::getInheritTheme());
    }

    public function ensureAllDirectoriesAreCreated(): void
    {
        $this->files->ensureDirectoryExists(lang_path('vendor'));
        $this->files->ensureDirectoryExists(lang_path('vendor/core'));
        $this->files->ensureDirectoryExists(lang_path('vendor/packages'));
        $this->files->ensureDirectoryExists(lang_path('vendor/plugins'));
    }

    public function findJsonTranslations(string $path): array
    {
        $keys = [];

        $stringPattern =
            "[^\w]" .                                       // Must not have an alpha num before real method
            '(__)' .                                        // Must start with one of the functions
            "\(\s*" .                                       // Match opening parenthesis
            "(?P<quote>['\"])" .                            // Match " or ' and store in {quote}
            "(?P<string>(?:\\\k{quote}|(?!\k{quote}).)*)" . // Match any string that can be {quote} escaped
            "\k{quote}" .                                   // Match " or ' previously matched
            "\s*[\),]";                                     // Close parentheses or new parameter

        $finder = new Finder();
        $finder->in($path)->exclude('storage')->exclude('vendor')->name('*.php')->files();

        foreach ($finder as $file) {
            if (! preg_match_all('/' . $stringPattern . '/siU', $file->getContents(), $matches)) {
                continue;
            }

            foreach ($matches['string'] as $key) {
                if (preg_match('/(^[a-zA-Z0-9_-]+([.][^\)\ ]+)+$)/siU', $key) && ! Str::contains(
                    $key,
                    '...'
                )) {
                    // Do nothing, it has to be treated as a group
                    continue;
                }

                // Skip keys which contain namespacing characters, unless they also contain a space, which makes it JSON.
                if (! (Str::contains($key, '::') && Str::contains($key, '.')) || Str::contains($key, ' ')) {
                    $keys[trim($key)] = $key;
                }
            }
        }

        return array_unique($keys);
    }

    public function updateThemeTranslations(): int
    {
        $keys = $this->findJsonTranslations(core_path());
        $keys += $this->findJsonTranslations(package_path());
        $keys += $this->findJsonTranslations(plugin_path());
        $keys += $this->findJsonTranslations(theme_path());

        ksort($keys);

        $data = json_encode($keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        BaseHelper::saveFileData(theme_path(sprintf('%s/lang/en.json', Theme::getThemeName())), $data, false);

        return count($keys);
    }

    public function deleteLocale(string $locale): void
    {
        $this->deleteLocaleService->handle($locale);
    }
}
