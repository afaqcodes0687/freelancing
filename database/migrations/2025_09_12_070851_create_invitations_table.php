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
            Schema::create('invitations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id');      // who sends the invitation
                $table->unsignedBigInteger('freelancer_id');  // who receives the invitation
                $table->unsignedBigInteger('job_id');         // related job
                $table->text('message')->nullable();
                $table->timestamps();

                // Foreign keys (optional but recommended)
                $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('freelancer_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('job_id')->references('id')->on('job_posts')->onDelete('cascade');
            });
        }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
