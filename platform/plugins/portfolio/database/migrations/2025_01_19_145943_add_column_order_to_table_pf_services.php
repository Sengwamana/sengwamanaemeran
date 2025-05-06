<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('pf_projects', function (Blueprint $table) {
            $table->tinyInteger('order')->default(0);
        });

        Schema::table('pf_packages', function (Blueprint $table) {
            $table->tinyInteger('order')->default(0);
        });

        Schema::table('pf_services', function (Blueprint $table) {
            $table->tinyInteger('order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('pf_projects', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        Schema::table('pf_packages', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        Schema::table('pf_services', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
