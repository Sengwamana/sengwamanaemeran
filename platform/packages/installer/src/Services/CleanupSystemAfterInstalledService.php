<?php

namespace Botble\Installer\Services;

use Botble\Base\Services\DeleteLocaleService;
use Botble\Base\Services\DeleteUnusedTranslationFilesService;
use Illuminate\Support\Facades\File;

class CleanupSystemAfterInstalledService
{
    public function __construct(
        protected DeleteUnusedTranslationFilesService $deleteUnusedTranslationFilesService,
        protected DeleteLocaleService $deleteLocaleService
    ) {
    }

    public function handle(): void
    {
        $this->deleteUnusedTranslationFilesService->handle();

        foreach (File::directories(lang_path()) as $tempLocale) {
            $locale = basename($tempLocale);

            if (! in_array($locale, ['en', 'vendor', app()->getLocale()])) {
                $this->deleteLocaleService->handle($locale);
            }
        }
    }
}
