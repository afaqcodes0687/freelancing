<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateReferralCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:generate-referral-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate referral codes for users who do not have one';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = User::whereNull('referral_code')->count();
        
        if ($count === 0) {
            $this->info('No users found with NULL referral codes.');
            return 0;
        }

        $this->info("Found {$count} users without referral codes. Processing in chunks...");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        User::whereNull('referral_code')->chunkById(100, function ($users) use ($bar) {
            foreach ($users as $user) {
                $user->referral_code = $user->generateReferralCode();
                $user->save();
                $bar->advance();
            }
        });
        
        $bar->finish();
        $this->newLine();
        $this->info('Referral codes generated successfully!');
        
        return 0;
    }
    
    /**
     * Generate a unique referral code
     *
     * @return string
     */
    private function generateUniqueReferralCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());
        
        return $code;
    }
} 