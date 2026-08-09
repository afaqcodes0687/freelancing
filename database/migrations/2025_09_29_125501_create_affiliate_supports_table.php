<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_supports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_id');
            $table->string('subject');
            $table->text('message');
            $table->enum('status', ['pending','resolved'])->default('pending');
            $table->timestamps();

            // Foreign key constraint (optional)
            $table->foreign('affiliate_id')->references('id')->on('affiliates_programs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_supports');
    }
};
