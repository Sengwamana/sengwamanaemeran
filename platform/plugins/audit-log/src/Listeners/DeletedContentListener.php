<?php

namespace Botble\AuditLog\Listeners;

use Botble\AuditLog\AuditLog;
use Botble\AuditLog\Events\AuditHandlerEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Facades\BaseHelper;
use Exception;

class DeletedContentListener
{
    public function handle(DeletedContentEvent $event): void
    {
        try {
            if ($event->data->getKey()) {
                $model = $event->screen;

                event(new AuditHandlerEvent(
                    $model,
                    'deleted',
                    $event->data->getKey(),
                    AuditLog::getReferenceName($model, $event->data),
                    'danger'
                ));
            }
        } catch (Exception $exception) {
            BaseHelper::logError($exception);
        }
    }
}
