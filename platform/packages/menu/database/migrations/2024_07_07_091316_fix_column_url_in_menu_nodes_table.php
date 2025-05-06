<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('menu_nodes', function (Blueprint $table): void {
            $table->string('url')->nullable()->change();
            $table->string('title')->nullable()->change();
            $table->string('css_class')->nullable()->change();
            $table->string('icon_font')->nullable()->change();
        });

        Schema::table('menu_locations', function (Blueprint $table): void {
            $table->string('location')->change();
        });
    }

    public function down(): void
    {
        Schema::table('menu_nodes', function (Blueprint $table): void {
            $table->string('url', 120)->nullable()->change();
            $table->string('title', 120)->nullable()->change();
            $table->string('css_class', 120)->nullable()->change();
            $table->string('icon_font', 50)->nullable()->change();
        });

        Schema::table('menu_locations', function (Blueprint $table): void {
            $table->string('location', 120)->change();
        });
    }
};
