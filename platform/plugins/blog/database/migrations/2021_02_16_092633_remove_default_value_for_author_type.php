<?php

use Botble\ACL\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('categories', 'author_type')) {
            Schema::table('categories', function (Blueprint $table): void {
                $table->string('author_type');
            });
        }

        Schema::table('categories', function (Blueprint $table): void {
            $table->string('author_type')->change();
        });

        if (! Schema::hasColumn('tags', 'author_type')) {
            Schema::table('tags', function (Blueprint $table): void {
                $table->string('author_type');
            });
        }

        Schema::table('tags', function (Blueprint $table): void {
            $table->string('author_type')->change();
        });

        if (! Schema::hasColumn('posts', 'author_type')) {
            Schema::table('posts', function (Blueprint $table): void {
                $table->string('author_type');
            });
        }

        Schema::table('posts', function (Blueprint $table): void {
            $table->string('author_type')->change();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->string('author_type')->default(addslashes(User::class))->change();
        });

        Schema::table('tags', function (Blueprint $table): void {
            $table->string('author_type')->default(addslashes(User::class))->change();
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->string('author_type')->default(addslashes(User::class))->change();
        });
    }
};
