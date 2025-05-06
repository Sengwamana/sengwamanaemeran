<?php

use Botble\Slug\Models\Slug;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('slugs', function (Blueprint $table): void {
            if (! Schema::hasIndex($table->getTable(), 'slugs_reference_id_index')) {
                $table->index('reference_id');
            }
        });

        try {
            foreach (Slug::query()->get() as $slug) {
                if (
                    $slug->reference_type && class_exists($slug->reference_type) &&
                    (! $slug->reference || ! $slug->reference->id)
                ) {
                    $slug->delete();
                }
            }
        } catch (Throwable) {
        }
    }

    public function down(): void
    {
        Schema::table('slugs', function (Blueprint $table): void {
            if (Schema::hasIndex($table->getTable(), 'slugs_reference_id_index')) {
                $table->dropIndex('reference_id');
            }
        });
    }
};
