<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 120);
                $table->text('content');
                $table->string('image')->nullable();
                $table->string('company', 120)->nullable();
                $table->string('status', 60)->default('published');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
