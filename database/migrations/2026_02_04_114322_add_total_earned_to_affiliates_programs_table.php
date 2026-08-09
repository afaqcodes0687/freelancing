<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('affiliates_programs', function (Blueprint $table) {
            if (!Schema::hasColumn('affiliates_programs', 'total_earned')) {
                $table->decimal('total_earned', 12, 2)->default(0)->after('balance');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliates_programs', function (Blueprint $table) {
            $table->dropColumn('total_earned');
        });
    }
};
