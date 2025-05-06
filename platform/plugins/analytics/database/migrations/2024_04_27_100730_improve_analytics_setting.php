<?php

use Botble\Base\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        rescue(function (): void {
            $value = 0;
            if (setting('analytics_property_id') && setting('analytics_service_account_credentials')) {
                $value = 1;
            }

            DB::table('settings')->insertOrIgnore([
                'id' => BaseModel::isUsingIntegerId() ? null : (new BaseModel())->newUniqueId(),
                'key' => 'analytics_dashboard_widgets',
                'value' => $value,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        });
    }
};
