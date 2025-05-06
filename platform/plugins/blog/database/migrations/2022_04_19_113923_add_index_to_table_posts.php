<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->index('status', 'posts_status_index');
            $table->index('author_id', 'posts_author_id_index');
            $table->index('author_type', 'posts_author_type_index');
            $table->index('created_at', 'posts_created_at_index');
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->index('parent_id', 'categories_parent_id_index');
            $table->index('status', 'categories_status_index');
            $table->index('created_at', 'categories_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropIndex('posts_status_index');
            $table->dropIndex('posts_author_id_index');
            $table->dropIndex('posts_author_type_index');
            $table->dropIndex('posts_created_at_index');
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropIndex('categories_parent_id_index');
            $table->dropIndex('categories_status_index');
            $table->dropIndex('categories_created_at_index');
        });
    }
};
