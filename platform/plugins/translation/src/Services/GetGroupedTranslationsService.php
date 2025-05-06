<?php

namespace Botble\Translation\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class GetGroupedTranslationsService
{
    public function handle(): Collection
    {
        $translations = [];

        $langLoader = Lang::getLoader();

        foreach ($this->getGroups() as $group) {
            if (! str_contains($group, DIRECTORY_SEPARATOR)) {
                $trans = $langLoader->load('en', $group);
            } else {
                $trans = $langLoader->load('en', Str::afterLast($group, DIRECTORY_SEPARATOR), Str::beforeLast($group, DIRECTORY_SEPARATOR));
            }

            if ($trans && is_array($trans)) {
                foreach (Arr::dot($trans) as $key => $value) {
                    if (empty($value)) {
                        continue;
                    }

                    $translations[$group][$key] = $value;
                }
            }
        }

        $translationsCollection = collect();

        foreach ($translations as $group => $items) {
            foreach (Arr::dot($items) as $key => $value) {
                $translationsCollection->push([
                    'group' => $group,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }

        return $translationsCollection;
    }

    public function getGroups(): array
    {
        $groups = [];

        if (File::isDirectory(lang_path('en'))) {
            foreach (File::allFiles(lang_path('en')) as $directory) {
                $group = File::name($directory);

                $groups[$group] = $group;
            }
        }

        foreach (Lang::getLoader()->namespaces() as $namespace => $langPath) {
            $defaultLanguage = $langPath . DIRECTORY_SEPARATOR . 'en';

            if (! File::isDirectory($defaultLanguage)) {
                continue;
            }

            foreach (File::allFiles($defaultLanguage) as $directory) {
                $group =  $namespace . DIRECTORY_SEPARATOR . File::name($directory);

                $groups[$group] = $group;
            }
        }

        ksort($groups);

        return $groups;
    }
}
