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
        Schema::create('job_proposal_milestones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('job_proposal_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->double('price')->nullable();
            $table->string('revision')->nullable();
            $table->string('deadline')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_proposal_milestones');
    }
};
