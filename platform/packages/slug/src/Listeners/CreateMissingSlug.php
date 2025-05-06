<?php

namespace Botble\Slug\Listeners;

use Botble\Slug\Facades\SlugHelper;

class CreateMissingSlug
{
    public function handle(): void
    {
        foreach (SlugHelper::supportedModels() as $model => $name) {
            $model = app($model);

            $model->query()->get()->each(function ($item): void {
                SlugHelper::createSlug($item);
            });
        }
    }
}
