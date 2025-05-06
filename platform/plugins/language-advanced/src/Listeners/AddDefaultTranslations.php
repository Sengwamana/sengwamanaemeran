<?php

namespace Botble\LanguageAdvanced\Listeners;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Language\Facades\Language;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Illuminate\Support\Facades\DB;

class AddDefaultTranslations
{
    public function handle(CreatedContentEvent $event): void
    {
        if (! LanguageAdvancedManager::isSupported($event->data)) {
            return;
        }

        $table = $event->data->getTable() . '_translations';

        foreach (Language::getActiveLanguage(['lang_code', 'lang_is_default']) as $language) {
            if ($language->lang_is_default) {
                continue;
            }

            $condition = [
                'lang_code' => $language->lang_code,
                $event->data->getTable() . '_id' => $event->data->getKey(),
            ];

            $existing = DB::table($table)->where($condition)->exists();

            if ($existing) {
                continue;
            }

            $dataDefault = apply_filters('filter_before_save_default_translation_advanced', $event->data, $language);

            $data = [];
            foreach (DB::getSchemaBuilder()->getColumnListing($table) as $column) {
                if (! in_array($column, array_keys($condition))) {
                    $data[$column] = $dataDefault->{$column};
                }
            }

            $data = array_merge($data, $condition);

            $data = apply_filters('language_advanced_before_save', $data, $event->data, $event->request);

            $data = array_map(function ($value) {
                return is_array($value) ? json_encode($value) : $value;
            }, $data);

            DB::table($table)->insert($data);
        }
    }
}
