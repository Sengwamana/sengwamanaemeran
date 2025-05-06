<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('pf_custom_fields_translations', function (Blueprint $table): void {
            $table->string('lang_code');
            $table->string('name')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('type', 60)->nullable();
            $table->foreignId('pf_custom_fields_id');

            $table->primary(['lang_code', 'pf_custom_fields_id']);
        });

        Schema::create('pf_custom_field_options_translations', function (Blueprint $table): void {
            $table->string('lang_code');
            $table->string('label')->nullable();
            $table->string('value')->nullable();
            $table->foreignId('pf_custom_field_options_id');

            $table->primary(['lang_code', 'pf_custom_field_options_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pf_custom_fields_translations');
        Schema::dropIfExists('pf_custom_field_options_translations');
    }
};
