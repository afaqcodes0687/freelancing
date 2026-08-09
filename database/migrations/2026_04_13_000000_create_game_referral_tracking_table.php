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
        if (!Schema::hasTable('game_referral_tracking')) {
            Schema::create('game_referral_tracking', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('referrer_id');
                $table->unsignedBigInteger('referred_user_id');
                $table->unsignedBigInteger('draw_id');
                $table->unsignedBigInteger('entry_id');
                $table->integer('referral_level')->default(1); // 1 for direct, 2 for level 2
                $table->decimal('commission_amount', 10, 2)->default(0);
                $table->string('commission_type', 50)->default('game_entry'); // game_entry, level_1, level_2
                $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('referrer_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('referred_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('draw_id')->references('id')->on('one_rupee_draws')->onDelete('cascade');
                $table->foreign('entry_id')->references('id')->on('one_rupee_entries')->onDelete('cascade');
                
                $table->index(['referrer_id', 'status']);
                $table->index(['referred_user_id', 'status']);
                $table->index(['draw_id', 'referral_level']);
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
        Schema::dropIfExists('game_referral_tracking');
    }
};
