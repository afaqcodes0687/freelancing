<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

class SetupPolicyViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'policy:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup policy views and directories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $srcDir = base_path();
        $baseDir = resource_path('views/backend');

        // Create directories
        $dirs = [
            $baseDir . '/service_shipping_policy',
            $baseDir . '/refund_return_policy',
        ];

        foreach ($dirs as $dir) {
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->info("Created directory: {$dir}");
            }
        }

        // Copy temporary files
        $files = [
            'temp_service_shipping_policy_edit.blade.php' => $baseDir . '/service_shipping_policy/edit.blade.php',
            'temp_refund_return_policy_edit.blade.php' => $baseDir . '/refund_return_policy/edit.blade.php',
        ];

        foreach ($files as $src => $dest) {
            $srcPath = $srcDir . '/' . $src;
            if (File::exists($srcPath)) {
                File::copy($srcPath, $dest);
                $this->info("Copied: {$src} -> {$dest}");
                // Optionally delete the temp file
                File::delete($srcPath);
            }
        }

        $this->info('Policy views setup completed successfully!');
    }
}
