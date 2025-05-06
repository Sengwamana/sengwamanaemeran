<?php

namespace Botble\ACL\Hooks;

use Botble\ACL\Models\User;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Illuminate\Support\Collection;

class UserWidgetHook
{
    public static function addUserStatsWidget(array $widgets, Collection $widgetSettings): array
    {
        $users = User::query()->count();

        return (new DashboardWidgetInstance())
            ->setType('stats')
            ->setPermission('users.index')
            ->setTitle(trans('core/acl::users.users'))
            ->setKey('widget_total_users')
            ->setIcon('ti ti-users')
            ->setColor('info')
            ->setStatsTotal($users)
            ->setRoute(route('users.index'))
            ->setColumn('col-12 col-md-6 col-lg-3')
            ->init($widgets, $widgetSettings);
    }
}
