<?php

use Botble\ACL\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('pf_projects', function (Blueprint $table): void {
            $table->foreignId('author_id')->after('content');
            $table->string('author_type', 255)->default(addslashes(User::class))->after('content');
            $table->string('client', 255)->nullable()->after('content');
            $table->string('place', 255)->nullable()->after('content');
            $table->foreignId('category_id')->index()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('pf_projects', function (Blueprint $table): void {
            $table->dropColumn('author_id');
            $table->dropColumn('author_type');
            $table->dropColumn('client');
            $table->dropColumn('place');
            $table->dropColumn('category_id');
        });
    }
};
