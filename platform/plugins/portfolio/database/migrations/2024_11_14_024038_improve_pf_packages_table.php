<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('pf_packages', function (Blueprint $table) {
            $table->text('content')->nullable()->change();
            $table->string('price')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('pf_packages', function (Blueprint $table) {
            $table->text('content')->change();
            $table->string('price')->change();
        });
    }
};
