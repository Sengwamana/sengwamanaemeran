<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('pf_project_categories');

        Schema::dropIfExists('pf_project_categories_translations');

        Schema::table('pf_projects', function (Blueprint $table): void {
            $table->dropColumn('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('pf_projects', function (Blueprint $table): void {
            $table->foreignId('category_id')->index()->after('content');
        });
    }
};
