<?php

use Botble\LanguageAdvanced\Plugin;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        rescue(fn () => Plugin::activated(), report: false);
    }
};
