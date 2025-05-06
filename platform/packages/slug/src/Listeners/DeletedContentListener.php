<?php

namespace Botble\Slug\Listeners;

use Botble\Base\Contracts\BaseModel;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Models\Slug;

class DeletedContentListener
{
    public function handle(DeletedContentEvent $event): void
    {
        if ($event->data instanceof BaseModel && SlugHelper::isSupportedModel($event->data::class)) {
            Slug::query()->where([
                'reference_id' => $event->data->getKey(),
                'reference_type' => $event->data::class,
            ])->delete();
        }
    }
}
