<?php

namespace Botble\Theme\Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Theme\Services\ThemeService;
use Illuminate\Support\Str;
use ReflectionClass;

class ThemeSeeder extends BaseSeeder
{
    public function uploadFiles(string $folder, ?string $basePath = null): array
    {
        $this->setCurrentDirectoryPath((string) $basePath);

        return parent::uploadFiles($folder);
    }

    protected function setCurrentDirectoryPath(string $theme): static
    {
        $basePath = $this->currentDirectoryPath('', $theme);

        $this->setBasePath($basePath);

        return $this;
    }

    protected function currentDirectoryPath(string $path, ?string $theme = null): string
    {
        $theme = $theme ?: $this->getThemeName();

        return database_path(sprintf("seeders/files/$theme%s", $path ? sprintf('/%s', $path) : ''));
    }

    protected function getThemeName(): string
    {
        $reflection = new ReflectionClass($this);

        return Str::slug(Str::afterLast($reflection->getNamespaceName(), '\\'));
    }

    protected function filePath(string $path, ?string $basePath = null): string
    {
        if (! $basePath && ! isset($this->basePath)) {
            $this->setCurrentDirectoryPath('');
        }

        return parent::filePath($path, $basePath);
    }

    protected function activateTheme(string $theme): void
    {
        app(ThemeService::class)->activate($theme);
    }
}
