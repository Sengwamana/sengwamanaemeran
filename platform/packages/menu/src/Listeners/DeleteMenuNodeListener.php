<?php

namespace Botble\Menu\Listeners;

use Botble\Base\Contracts\BaseModel;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Menu\Facades\Menu;
use Botble\Menu\Models\MenuNode;

class DeleteMenuNodeListener
{
    public function handle(DeletedContentEvent $event): void
    {
        if (
            ! $event->data instanceof BaseModel ||
            ! in_array($event->data::class, Menu::getMenuOptionModels())
        ) {
            return;
        }

        MenuNode::query()
            ->where([
                'reference_id' => $event->data->getKey(),
                'reference_type' => $event->data::class,
            ])
            ->delete();
    }
}
