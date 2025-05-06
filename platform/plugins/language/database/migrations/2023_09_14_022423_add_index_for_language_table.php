<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('languages', function (Blueprint $table): void {
            $table->index('lang_locale', 'lang_locale_index');
            $table->index('lang_code', 'lang_code_index');
            $table->index('lang_is_default', 'lang_is_default_index');
        });

        Schema::table('language_meta', function (Blueprint $table): void {
            $table->string('lang_meta_code', 20)->nullable()->change();
            $table->string('lang_meta_origin', 32)->change();
        });

        Schema::table('language_meta', function (Blueprint $table): void {
            $table->index('lang_meta_code', 'meta_code_index');
            $table->index('lang_meta_origin', 'meta_origin_index');
            $table->index('reference_type', 'meta_reference_type_index');
        });
    }

    public function down(): void
    {
        Schema::table('languages', function (Blueprint $table): void {
            $table->dropIndex('lang_locale_index');
            $table->dropIndex('lang_code_index');
            $table->dropIndex('lang_is_default_index');
        });

        Schema::table('language_meta', function (Blueprint $table): void {
            $table->dropIndex('meta_code_index');
            $table->dropIndex('meta_origin_index');
            $table->dropIndex('meta_reference_type_index');
        });
    }
};
