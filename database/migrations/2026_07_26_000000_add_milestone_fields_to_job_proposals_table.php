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
        Schema::table('job_proposals', function (Blueprint $table) {
            $table->string('pay_type')->default('pay-at-once')->comment('pay-at-once, pay-by-milestone')->after('cover_letter');
            $table->json('milestone_data')->nullable()->after('pay_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_proposals', function (Blueprint $table) {
            $table->dropColumn(['pay_type', 'milestone_data']);
        });
    }
};
