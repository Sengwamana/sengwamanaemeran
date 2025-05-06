<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('slugs', function (Blueprint $table): void {
            $table->id();
            $table->string('key');
            $table->foreignId('reference_id');
            $table->string('reference_type');
            $table->string('prefix', 120)->nullable()->default('');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slugs');
    }
};
