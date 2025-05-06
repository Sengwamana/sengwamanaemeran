<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('pf_projects_translations', function (Blueprint $table): void {
            $table->string('lang_code');
            $table->foreignId('pf_projects_id');
            $table->string('name');
            $table->string('description', 400)->nullable();
            $table->longText('content')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pf_projects_translations');
    }
};
