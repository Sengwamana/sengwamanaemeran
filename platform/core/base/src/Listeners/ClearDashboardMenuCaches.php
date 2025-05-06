<?php

namespace Botble\Base\Listeners;

use Botble\Base\Facades\DashboardMenu;

class ClearDashboardMenuCaches
{
    public function handle(): void
    {
        DashboardMenu::clearCaches();
    }
}
