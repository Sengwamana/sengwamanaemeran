<?php

namespace Botble\Language\Commands;

use Botble\Language\Facades\Language;
use Illuminate\Foundation\Console\RouteClearCommand as BaseRouteClearCommand;

class RouteClearCommand extends BaseRouteClearCommand
{
    public function handle(): int
    {
        parent::handle();

        $defaultLocale = Language::getDefaultLocale();

        foreach (Language::getSupportedLanguagesKeys() as $locale) {
            $path = $this->laravel->getCachedRoutesPath();

            $locale = $locale ?: $defaultLocale;

            $path = substr($path, 0, -4) . '_' . $locale . '.php';

            if ($this->files->exists($path)) {
                $this->files->delete($path);
            }
        }

        return self::SUCCESS;
    }
}
