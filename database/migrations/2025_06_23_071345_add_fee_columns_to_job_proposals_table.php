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
        Schema::table('job_proposals', function (Blueprint $table) {
            $table->double('freelancer_service_fee')->default(0)->after('amount');
            $table->double('you_receive_amount')->default(0)->after('freelancer_service_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_proposals', function (Blueprint $table) {
            $table->dropColumn(['freelancer_service_fee', 'you_receive_amount']);

        });
    }
};
