<?php

use Botble\Setting\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        try {
            Setting::query()
                ->where('key', 'blacklist_email_domains')
                ->update([
                    'key' => 'email_rules_blacklist_email_domains',
                ]);
        } catch (Throwable) {
        }
    }
};
