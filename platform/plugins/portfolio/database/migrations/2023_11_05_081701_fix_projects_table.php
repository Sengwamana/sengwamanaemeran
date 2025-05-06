<?php

use Botble\ACL\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        Schema::table('pf_projects', function (Blueprint $table): void {
            $table->dropColumn('author_id');
            $table->dropColumn('author_type');
        });

        Schema::table('pf_projects', function (Blueprint $table): void {
            $table->string('author', 255)->nullable();
            $table->date('start_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pf_projects', function (Blueprint $table): void {
            $table->dropColumn('author');
            $table->dropColumn('start_date');
        });

        Schema::table('pf_projects', function (Blueprint $table): void {
            $table->foreignId('author_id')->after('content');
            $table->string('author_type', 255)->default(addslashes(User::class))->after('content');
        });
    }
};
