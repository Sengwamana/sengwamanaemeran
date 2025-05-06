<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('announcements_translations', 'action_label')) {
            return;
        }

        Schema::table('announcements_translations', function (Blueprint $table) {
            $table->string('action_label', 60)->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('announcements_translations', 'action_label')) {
            return;
        }

        Schema::table('announcements_translations', function (Blueprint $table) {
            $table->dropColumn('action_label');
        });
    }
};
