<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use File;

class PolicySetupServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Create policy view directories if they don't exist
        $dirs = [
            resource_path('views/backend/service_shipping_policy'),
            resource_path('views/backend/refund_return_policy'),
        ];

        foreach ($dirs as $dir) {
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
        }
    }
}
