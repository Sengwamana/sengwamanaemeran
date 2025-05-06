<?php

namespace Botble\Portfolio;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('pf_service_categories');
        Schema::dropIfExists('pf_services');
        Schema::dropIfExists('pf_packages');
        Schema::dropIfExists('pf_service_categories_translations');
        Schema::dropIfExists('pf_services_translations');
        Schema::dropIfExists('pf_packages_translations');
    }
}
