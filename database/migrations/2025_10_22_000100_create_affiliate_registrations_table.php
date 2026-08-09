<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('affiliate_registrations')) {
            Schema::create('affiliate_registrations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                $table->foreign('affiliate_id')->references('id')->on('affiliates_programs')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['affiliate_id','user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_registrations');
    }
};
