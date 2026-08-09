<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_installations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('version', 20);
            $table->string('platform', 10); // android, ios
            $table->string('device_id');
            $table->string('previous_version', 20)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('device_info')->nullable(); // Device details, OS version, etc.
            $table->timestamp('installed_at');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['platform', 'version']);
            $table->index('device_id');
            $table->index('installed_at');
        });

        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20);
            $table->string('version_name');
            $table->string('platform', 10); // android, ios
            $table->text('release_notes')->nullable();
            $table->string('download_url');
            $table->bigInteger('file_size')->default(0);
            $table->string('min_supported_version', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('force_update')->default(false);
            $table->string('checksum')->nullable();
            $table->string('signature')->nullable();
            $table->timestamp('release_date');
            $table->timestamps();

            // Unique constraint for version+platform
            $table->unique(['version', 'platform']);
            $table->index(['platform', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_installations');
        Schema::dropIfExists('app_versions');
    }
};
