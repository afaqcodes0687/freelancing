<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Use raw SQL to modify column to avoid Doctrine dependency and ensure ENUM update
        DB::statement("ALTER TABLE affiliate_payouts MODIFY COLUMN status ENUM('pending', 'paid', 'failed', 'rejected') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert back to original (optional, but good practice)
        DB::statement("ALTER TABLE affiliate_payouts MODIFY COLUMN status ENUM('pending', 'paid', 'failed') DEFAULT 'pending'");
    }
};
