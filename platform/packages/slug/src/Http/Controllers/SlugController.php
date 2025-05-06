<?php

namespace Botble\Slug\Http\Controllers;

use Botble\Menu\Facades\Menu;
use Botble\Setting\Http\Controllers\SettingController;
use Botble\Setting\Supports\SettingStore;
use Botble\Slug\Events\UpdatedPermalinkSettings;
use Botble\Slug\Forms\SlugSettingForm;
use Botble\Slug\Http\Requests\SlugRequest;
use Botble\Slug\Http\Requests\SlugSettingsRequest;
use Botble\Slug\Models\Slug;
use Botble\Slug\Services\SlugService;
use Illuminate\Support\Str;

class SlugController extends SettingController
{
    public function store(SlugRequest $request, SlugService $slugService)
    {
        return $slugService->create(
            $request->input('value'),
            $request->input('slug_id'),
            $request->input('model')
        );
    }

    public function edit()
    {
        $this->pageTitle(trans('packages/slug::slug.settings.title'));

        return SlugSettingForm::create()->renderForm();
    }

    public function update(SlugSettingsRequest $request, SettingStore $settingStore)
    {
        $hasChangedEndingUrl = false;

        foreach ($request->except(['_token', 'ref_lang']) as $settingKey => $settingValue) {
            if (Str::contains($settingKey, '-model-key')) {
                continue;
            }

            if (Str::startsWith($settingKey, 'public_single_ending_url')) {
                if ($settingValue) {
                    $settingValue = ltrim($settingValue, '.');
                }

                if ($settingStore->get($settingKey) !== $settingValue) {
                    $hasChangedEndingUrl = true;
                }
            }

            $prefix = (string) $settingValue;
            $reference = $request->input($settingKey . '-model-key');

            if ($reference && $settingStore->get($settingKey) !== $prefix) {
                if (! $request->filled('ref_lang')) {
                    Slug::query()
                        ->where('reference_type', $reference)
                        ->update(['prefix' => $prefix]);
                }

                event(new UpdatedPermalinkSettings($reference, $prefix, $request));

                Menu::clearCacheMenuItems();
            }

            $settingStore->set($settingKey, $prefix);
        }

        $settingStore->save();

        if ($hasChangedEndingUrl) {
            Menu::clearCacheMenuItems();
        }

        return $this
            ->httpResponse()
            ->setPreviousRoute('slug.settings')
            ->withUpdatedSuccessMessage();
    }
}
