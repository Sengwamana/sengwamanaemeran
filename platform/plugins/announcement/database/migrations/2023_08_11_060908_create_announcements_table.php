<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->text('content');
                $table->boolean('has_action')->default(false);
                $table->string('action_label', 60)->nullable();
                $table->string('action_url', 400)->nullable();
                $table->boolean('action_open_new_tab')->default(false);
                $table->boolean('dismissible')->default(false);
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('announcements_translations')) {
            Schema::create('announcements_translations', function (Blueprint $table): void {
                $table->string('lang_code');
                $table->foreignId('announcements_id');
                $table->text('content')->nullable();

                $table->primary(['lang_code', 'announcements_id'], 'announcements_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('announcements_translations');
    }
};
