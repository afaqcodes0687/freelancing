<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('one_rupee_draws', function (Blueprint $table) {
            if (!Schema::hasColumn('one_rupee_draws', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
            if (!Schema::hasColumn('one_rupee_draws', 'image_path')) {
                $table->string('image_path')->nullable()->after('title');
            }
            if (!Schema::hasColumn('one_rupee_draws', 'announcement_at')) {
                $table->dateTime('announcement_at')->nullable()->after('scheduled_for');
            }
            if (!Schema::hasColumn('one_rupee_draws', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('image_path');
            }
        });

        try {
            Schema::table('one_rupee_draws', function (Blueprint $table) {
                $table->dropUnique('one_rupee_draws_scheduled_for_unique');
            });
        } catch (\Throwable $e) {

        }
    }

    public function down(): void
    {
        Schema::table('one_rupee_draws', function (Blueprint $table) {
            if (Schema::hasColumn('one_rupee_draws', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('one_rupee_draws', 'image_path')) {
                $table->dropColumn('image_path');
            }
            if (Schema::hasColumn('one_rupee_draws', 'announcement_at')) {
                $table->dropColumn('announcement_at');
            }
            if (Schema::hasColumn('one_rupee_draws', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
