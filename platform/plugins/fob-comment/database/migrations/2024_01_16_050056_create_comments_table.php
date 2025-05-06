<?php

use FriendsOfBotble\Comment\Enums\CommentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('fob_comments')) {
            return;
        }

        Schema::create('fob_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reply_to')->nullable()->index();
            $table->nullableMorphs('author');
            $table->nullableMorphs('reference');
            $table->string('reference_url', 255)->nullable()->index();
            $table->string('name', 120);
            $table->string('email', 120)->nullable();
            $table->string('website', 255)->nullable();
            $table->longText('content');
            $table->string('status', 60)->index()->default(CommentStatus::PENDING);
            $table->ipAddress();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fob_comments');
    }
};
