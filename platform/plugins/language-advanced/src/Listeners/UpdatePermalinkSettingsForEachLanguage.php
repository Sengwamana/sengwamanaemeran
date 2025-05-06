<?php

namespace Botble\LanguageAdvanced\Listeners;

use Botble\Slug\Events\UpdatedPermalinkSettings;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Facades\DB;

class UpdatePermalinkSettingsForEachLanguage
{
    public function handle(UpdatedPermalinkSettings $event): void
    {
        if (! $event->request->filled('ref_lang')) {
            return;
        }

        $missingSlugTranslations = Slug::query()
            ->where('reference_type', $event->reference)
            ->whereNotIn('id', DB::table('slugs_translations')->pluck('slugs_id')->all())
            ->get();

        foreach ($missingSlugTranslations as $missingSlugTranslation) {
            DB::table('slugs_translations')
                ->insert([
                    'slugs_id' => $missingSlugTranslation->id,
                    'lang_code' => $event->request->input('ref_lang'),
                    'key' => $missingSlugTranslation->key,
                    'prefix' => $missingSlugTranslation->prefix,
                ]);
        }

        $slugIds = Slug::query()
            ->where('reference_type', $event->reference)
            ->pluck('id')
            ->all();

        DB::table('slugs_translations')
            ->whereIn('slugs_id', $slugIds)
            ->update(['prefix' => $event->prefix]);
    }
}
