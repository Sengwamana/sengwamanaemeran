<?php

namespace Botble\AuditLog\Listeners;

use Botble\AuditLog\AuditLog;
use Botble\AuditLog\Events\AuditHandlerEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Facades\BaseHelper;
use Exception;
use Illuminate\Support\Str;

class CreatedContentListener
{
    public function handle(CreatedContentEvent $event): void
    {
        try {
            if ($event->data->getKey()) {
                $model = $event->screen;

                if ($model === 'form') {
                    $model = strtolower(Str::afterLast(get_class($event->data), '\\'));
                }

                event(new AuditHandlerEvent(
                    $model,
                    'created',
                    $event->data->getKey(),
                    AuditLog::getReferenceName($model, $event->data),
                    'info'
                ));
            }
        } catch (Exception $exception) {
            BaseHelper::logError($exception);
        }
    }
}
