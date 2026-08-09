<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LiveMigrationController extends Controller
{
    public function updateUserSubscriptionsStatusColumn()
    {
        // Optional safety check if table exists
        if (!Schema::hasTable('user_subscriptions')) {
            return response()->json(['status' => 'error', 'message' => 'Table not found.']);
        }

        try {
            DB::statement("ALTER TABLE user_subscriptions CHANGE status status TINYINT(4) DEFAULT 0 COMMENT '0: Inactive, 1: Active, 2: Pending'");
            return response()->json(['status' => 'success', 'message' => 'Migration applied: Comment added to status column.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
