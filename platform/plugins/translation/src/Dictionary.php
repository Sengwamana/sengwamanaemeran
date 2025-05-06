<?php

namespace Botble\Translation;

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\File;
use Throwable;

class Dictionary
{
    protected string $locale;

    protected array $dictionary = [];

    public function locale(string $locale): static
    {
        $this->locale = $locale;

        $this->dictionary = [];

        return $this;
    }

    public function getTranslate(string $text): ?string
    {
        try {
            if (empty($this->dictionary)) {
                $path = sprintf(__DIR__ . '/../resources/dictionaries/%s.json', $this->locale);

                if (! File::exists($path)) {
                    return null;
                }

                $this->dictionary = File::json($path);
            }

            return $this->dictionary[$text] ?? null;
        } catch (Throwable $exception) {
            BaseHelper::logError($exception);

            return null;
        }
    }
}
