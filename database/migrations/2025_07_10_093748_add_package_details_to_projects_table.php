<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('basic_details')->nullable()->after('basic_delivery');
            $table->text('standard_details')->nullable()->after('standard_delivery');
            $table->text('premium_details')->nullable()->after('premium_delivery');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['basic_details', 'standard_details', 'premium_details']);
        });
    }

};
