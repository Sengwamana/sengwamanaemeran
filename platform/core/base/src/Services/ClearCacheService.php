<?php

namespace Botble\Base\Services;

use Botble\Media\Facades\RvMedia;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClearCacheService
{
    public function __construct(protected Application $app, protected Filesystem $files)
    {
    }

    public static function make(): self
    {
        return app(self::class);
    }

    public function clearFrameworkCache(): void
    {
        Event::dispatch('cache:clearing');

        Cache::flush();

        Event::dispatch('cache:cleared');
    }

    public function clearGoogleFontsCache(): void
    {
        if (! config('core.base.general.google_fonts_enabled_cache')) {
            return;
        }

        if ($this->files->isDirectory($fontPath = Storage::path('fonts'))) {
            $this->files->deleteDirectory($fontPath);
        }
    }

    public function clearBootstrapCache(): void
    {
        foreach ($this->files->glob($this->app->bootstrapPath('cache/*.php')) as $view) {
            $this->files->delete($view);
        }
    }

    public function clearCompiledViews(): void
    {
        foreach ($this->files->glob(config('view.compiled') . '/*.php') as $view) {
            $this->files->delete($view);
        }
    }

    public function clearConfig(): void
    {
        $this->files->delete($this->app->getCachedConfigPath());
    }

    public function clearRoutesCache(): void
    {
        foreach ($this->files->glob($this->app->bootstrapPath('cache/*')) as $cacheFile) {
            if (Str::contains($cacheFile, 'cache/routes-v7')) {
                $this->files->delete($cacheFile);
            }
        }
    }

    public function clearLogs(): void
    {
        if (! $this->files->isDirectory($logPath = storage_path('logs'))) {
            return;
        }

        foreach ($this->files->glob($logPath . '/*.log') as $file) {
            $this->files->delete($file);
        }
    }

    public function clearPackagesCache(): void
    {
        $this->files->delete($this->app->getCachedPackagesPath());
    }

    public function clearServicesCache(): void
    {
        $this->files->delete($this->app->getCachedServicesPath());
    }

    public function clearPurifier(): void
    {
        $purifierPath = config('purifier.cachePath');

        if (! $purifierPath || ! $this->files->isDirectory($purifierPath)) {
            return;
        }

        $this->files->deleteDirectories($purifierPath);
    }

    public function clearDebugbar(): void
    {
        $debugbarPath = config('debugbar.storage.path');

        if (! $debugbarPath || ! $this->files->isDirectory($debugbarPath)) {
            return;
        }

        $this->files->deleteDirectories($debugbarPath);
    }

    public function purgeAll(): void
    {
        $this->clearFrameworkCache();
        $this->clearCompiledViews();
        $this->clearBootstrapCache();
        $this->clearRoutesCache();
        $this->clearConfig();
        $this->clearLogs();
        $this->clearPurifier();
        $this->clearDebugbar();

        RvMedia::refreshCache();
    }
}
