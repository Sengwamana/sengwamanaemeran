<?php

namespace Botble\RequestLog;

use Botble\Dashboard\Models\DashboardWidget;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Widget\Models\Widget;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('request_logs');

        Widget::query()
            ->where('widget_id', 'widget_request_errors')
            ->each(fn (DashboardWidget $dashboardWidget) => $dashboardWidget->delete());
    }
}
