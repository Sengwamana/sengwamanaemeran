<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('request_logs', function (Blueprint $table): void {
            $table->id();
            $table->integer('status_code')->nullable();
            $table->string('url')->nullable();
            $table->integer('count')->default(0)->unsigned();
            $table->string('user_id')->nullable();
            $table->text('referrer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_logs');
    }
};
