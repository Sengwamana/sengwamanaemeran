<?php

namespace Botble\Base\Events;

use Botble\Base\Supports\DashboardMenu;
use Illuminate\Foundation\Events\Dispatchable;

class DashboardMenuRetrieving
{
    use Dispatchable;

    public function __construct(
        public DashboardMenu $dashboardMenu
    ) {
    }
}
