<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Portfolio\Enums\PackageDuration;
use Botble\Portfolio\Enums\QuoteStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('pf_service_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable()->index();
            $table->string('name');
            $table->string('description', 400)->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('order')->default(0);
            $table->string('status')->default(BaseStatusEnum::PUBLISHED);
            $table->timestamps();
        });

        Schema::create('pf_services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->index();
            $table->string('name');
            $table->string('description', 400)->nullable();
            $table->longText('content')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('image')->nullable();
            $table->text('images')->nullable();
            $table->integer('views')->default(0);
            $table->string('status', 60)->default(BaseStatusEnum::PUBLISHED);
            $table->timestamps();
        });

        Schema::create('pf_packages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('description', 400)->nullable();
            $table->text('content');
            $table->string('price');
            $table->string('annual_price')->nullable();
            $table->string('duration')->default(PackageDuration::MONTHLY);
            $table->text('features')->nullable();
            $table->string('status')->default(BaseStatusEnum::PUBLISHED);
            $table->boolean('is_popular')->default(false);
            $table->timestamps();
        });

        Schema::create('pf_projects', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('description', 400)->nullable();
            $table->longText('content')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('image')->nullable();
            $table->text('images')->nullable();
            $table->integer('views')->default(0);
            $table->string('status', 60)->default(BaseStatusEnum::PUBLISHED);
            $table->timestamps();
        });

        Schema::create('pf_quotes', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->text('message');
            $table->text('fields')->nullable();
            $table->string('status')->default(QuoteStatus::UNREAD);
            $table->timestamps();
        });

        Schema::create('pf_service_categories_translations', function (Blueprint $table): void {
            $table->string('lang_code');
            $table->foreignId('pf_service_categories_id');
            $table->string('name', 255)->nullable();
            $table->string('description', 400)->nullable();

            $table->primary(['lang_code', 'pf_service_categories_id'], 'pf_service_categories_translations_primary');
        });

        Schema::create('pf_services_translations', function (Blueprint $table): void {
            $table->string('lang_code');
            $table->foreignId('pf_services_id');
            $table->string('name', 255)->nullable();
            $table->string('description', 400)->nullable();
            $table->text('content')->nullable();

            $table->primary(['lang_code', 'pf_services_id'], 'pf_services_translations_primary');
        });

        Schema::create('pf_packages_translations', function (Blueprint $table): void {
            $table->string('lang_code');
            $table->foreignId('pf_packages_id');
            $table->string('name', 255)->nullable();
            $table->string('description', 400)->nullable();
            $table->text('content')->nullable();
            $table->string('price')->nullable();
            $table->string('annual_price')->nullable();
            $table->text('features')->nullable();

            $table->primary(['lang_code', 'pf_packages_id'], 'pf_packages_translations_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pf_service_categories');
        Schema::dropIfExists('pf_services');
        Schema::dropIfExists('pf_packages');
        Schema::dropIfExists('pf_projects');
        Schema::dropIfExists('pf_service_categories_translations');
        Schema::dropIfExists('pf_services_translations');
        Schema::dropIfExists('pf_packages_translations');
    }
};
