<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('referrals')) {
            Schema::create('referrals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('referrer_id');
                $table->unsignedBigInteger('referred_id');
                $table->string('referral_code');
                $table->decimal('reward_amount', 10, 2)->default(0);
                $table->decimal('max_reward', 10, 2)->default(100);
                $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->foreign('referrer_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('referred_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['referrer_id', 'referred_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referrals');
    }
}; 