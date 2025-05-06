<?php

use Botble\Portfolio\Models\Package;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('pf_packages', function (Blueprint $table): void {
            $table->after('is_popular', function (Blueprint $table): void {
                $table->string('action_label')->nullable();
                $table->string('action_url')->nullable();
            });
        });

        Schema::table('pf_packages_translations', function (Blueprint $table): void {
            $table->string('action_label')->nullable();
            $table->string('action_url')->nullable();
        });

        Package::query()->each(function ($package): void {
            $package->update([
                'action_label' => $package->getMetaData('action_label', true) ?? null,
                'action_url' => $package->getMetaData('action_url', true) ?? null,
            ]);

            $package->metadata()->whereIn('meta_key', ['action_url', 'action_label'])->delete();
        });
    }

    public function down(): void
    {
        Schema::table('pf_packages', function (Blueprint $table): void {
            $table->dropColumn('action_label');
            $table->dropColumn('action_url');
        });

        Schema::table('pf_packages_translations', function (Blueprint $table): void {
            $table->dropColumn('action_label');
            $table->dropColumn('action_url');
        });
    }
};
