<?php

namespace Botble\Translation;

use Botble\Translation\Exceptions\TranslationRequestException;
use Botble\Translation\Services\GoogleTranslate;
use Illuminate\Support\Str;
use Throwable;

class AutoTranslateManager
{
    public function handle(string $source, string $target, string $value): ?string
    {
        $originalValue = $value;

        $value = str_replace('%', '#_#', $value);

        $variables = $this->findVariablesByRule($value, ':');

        foreach ($variables as $item) {
            $value = str_replace($item, '%s', $value);
        }

        try {
            $translated = (new GoogleTranslate())
                ->setSource($source)
                ->setTarget($target)
                ->translate($value);
        } catch (TranslationRequestException) {
            $translated = $originalValue;
        }

        $translated = str_replace('%S', '%s', $translated);

        if (count($this->findVariablesByRule($translated, '%s')) !== count($variables)) {
            return $originalValue;
        }

        try {
            $translated = sprintf($translated, ...$variables);
        } catch (Throwable) {
            return $originalValue;
        }

        $translated = str_replace('#_#', '%', $translated);
        $translated = str_replace('#_ #', '%', $translated);

        $translatedVariables = $this->findVariablesByRule($translated, '%s');

        if (count($translatedVariables) == count($variables)) {
            return $translated;
        }

        return $originalValue;
    }

    public function translate(string $source, string $target, string $value): ?string
    {
        $translated = app(Dictionary::class)->locale($target)->getTranslate($value);

        if ($translated) {
            return $translated;
        }

        return $this->handle($source, $target, $value);
    }

    protected function findVariablesByRule(string $text, string $rule): array
    {
        return array_values(array_filter(explode(' ', $text), function ($item) use ($rule) {
            return str_replace($rule, '', $item) && Str::startsWith($item, $rule);
        }));
    }
}
