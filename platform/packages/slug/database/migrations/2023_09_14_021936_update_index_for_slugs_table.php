<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        try {
            Schema::table('slugs', function (Blueprint $table): void {
                $table->index('key', 'slugs_key_index');
                $table->index('prefix', 'slugs_prefix_index');
                $table->index(['reference_id', 'reference_type'], 'slugs_reference_index');
            });
        } catch (Throwable) {
        }
    }

    public function down(): void
    {
        try {
            Schema::table('slugs', function (Blueprint $table): void {
                $table->dropIndex('slugs_key_index');
                $table->dropIndex('slugs_prefix_index');
                $table->dropIndex('slugs_reference_index');
            });
        } catch (Throwable) {
        }
    }
};
