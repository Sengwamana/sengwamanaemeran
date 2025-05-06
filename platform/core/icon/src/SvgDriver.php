<?php

namespace Botble\Icon;

use Botble\Icon\Exceptions\SvgNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class SvgDriver extends IconDriver
{
    protected array $icons;

    protected array $cachedContents = [];

    public function __construct(
        protected Filesystem $files,
    ) {
        $this->setIconPath(core_path('icon/resources/svg'));
    }

    public function all(): array
    {
        return $this->icons ??= $this->discoveryIcons();
    }

    public function render(string $name, array $attributes = []): string
    {
        if (! $this->has($name)) {
            info(sprintf('Icon "%s" not found.', $name));

            throw_if(App::hasDebugModeEnabled(), SvgNotFoundException::missing($name));

            return '';
        }

        $contents = $this->getContents($name);

        $contents = trim(preg_replace('/^(<\?xml.+?\?>)/', '', $contents));

        return str_replace(
            '<svg',
            rtrim(sprintf('<svg %s', $this->parseAttributesToHtml($attributes))),
            $contents
        );
    }

    public function has(string $name): bool
    {
        return $this->discoveryIcon($name);
    }

    protected function getContents(string $name): string
    {
        $name = Str::startsWith($name, 'ti ti-') ? $name : 'ti ti-' . $name;

        if (! $this->has($name)) {
            return '';
        }

        return $this->cachedContents[$name]
            ??= $this->files->get($this->icons[$name]['path']);
    }

    protected function discoveryIcons(): array
    {
        if (! $this->files->isDirectory($this->iconPath())) {
            return [];
        }

        $files = $this->files->glob($this->iconPath() . '/*.svg');

        $icons = [];

        foreach ($files as $file) {
            $basename = str_replace('.svg', '', basename($file));
            $name = sprintf('ti ti-%s', $basename);
            $icons[$name] = [
                'name' => $name,
                'basename' => $basename,
                'path' => $file,
            ];
        }

        return $icons;
    }

    protected function discoveryIcon(string $name): bool
    {
        $name = Str::startsWith($name, 'ti ti-') ? $name : 'ti ti-' . $name;
        $basename = $this->normalizeName($name);

        if (isset($this->icons[$name])) {
            return true;
        }

        $file = $this->iconPath() . '/' . $basename . '.svg';

        if (! $this->files->exists($file)) {
            return false;
        }

        $this->icons[$name] = [
            'name' => $name,
            'basename' => $basename,
            'path' => $file,
        ];

        return true;
    }

    protected function normalizeName(string $name): string
    {
        return Str::after($name, 'ti ti-');
    }
}
