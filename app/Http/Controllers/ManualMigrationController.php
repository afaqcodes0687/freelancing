<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ManualMigrationController extends Controller
{
    public function run()
    {
        if (!Schema::hasColumn('projects', 'basic_details')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->text('basic_details')->nullable()->after('basic_delivery');
            });
        }

        if (!Schema::hasColumn('projects', 'standard_details')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->text('standard_details')->nullable()->after('standard_delivery');
            });
        }

        if (!Schema::hasColumn('projects', 'premium_details')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->text('premium_details')->nullable()->after('premium_delivery');
            });
        }

        return "✅ Migration fields successfully added to 'projects' table.";
    }
}
