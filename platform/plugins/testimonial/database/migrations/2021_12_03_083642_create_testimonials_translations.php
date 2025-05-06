<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('testimonials_translations')) {
            Schema::create('testimonials_translations', function (Blueprint $table): void {
                $table->string('lang_code', 20);
                $table->foreignId('testimonials_id');
                $table->string('name')->nullable();
                $table->text('content')->nullable();
                $table->string('company', 120)->nullable();

                $table->primary(['lang_code', 'testimonials_id'], 'testimonials_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials_translations');
    }
};
