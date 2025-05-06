<?php

namespace Botble\AuditLog;

use Illuminate\Database\Eloquent\Model;

class AuditLog
{
    public static function getReferenceName(string $screen, Model $data): string
    {
        return match ($screen) {
            USER_MODULE_SCREEN_NAME, AUTH_MODULE_SCREEN_NAME => $data->name,
            default => $data->name ?: $data->title ?: ($data->id ? "ID: $data->id" : ''),
        };
    }
}
