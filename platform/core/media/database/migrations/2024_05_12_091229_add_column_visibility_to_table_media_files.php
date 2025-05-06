<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('media_files', 'visibility')) {
            Schema::table('media_files', function (Blueprint $table): void {
                $table->string('visibility')->default('public');
            });
        }
    }

    public function down(): void
    {
        Schema::table('media_files', function (Blueprint $table): void {
            $table->dropColumn('visibility');
        });
    }
};
