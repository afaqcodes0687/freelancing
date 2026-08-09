<?php

namespace App\Services;

use App\Models\User;
use App\Models\Referral;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletHistory;

class ReferralService
{
    /**
     * Process a new referral
     */
    public function processReferral($referralCode, $referredUserId)
    {
        try {
            Log::info('Starting referral processing for code: ' . $referralCode . ' and user: ' . $referredUserId);
            DB::beginTransaction();

            // Find the referrer by referral code
            $referrer = User::where('referral_code', $referralCode)->first();
            
            if (!$referrer) {
                Log::error('Referrer not found for code: ' . $referralCode);
                throw new \Exception('Invalid referral code');
            }
            
            Log::info('Found referrer: ' . $referrer->id . ' for code: ' . $referralCode);

            // Check if user is trying to refer themselves
            if ($referrer->id == $referredUserId) {
                throw new \Exception('You cannot refer yourself');
            }

            // Check if this referral already exists
            $existingReferral = Referral::where('referrer_id', $referrer->id)
                ->where('referred_id', $referredUserId)
                ->first();

            if ($existingReferral) {
                throw new \Exception('This referral already exists');
            }

            // Check if referrer has reached the $500 limit
            if ($referrer->hasReachedReferralLimit()) {
                throw new \Exception('You have reached the maximum referral earnings limit of $500');
            }

            // Update the referred user's referred_by column
            $referredUser = User::find($referredUserId);
            if ($referredUser) {
                $referredUser->update(['referred_by' => $referrer->id]);
            }

            // Create the referral record
            $referral = Referral::create([
                'referrer_id' => $referrer->id,
                'referred_id' => $referredUserId,
                'referral_code' => $referralCode,
                'reward_amount' => 0,
                'max_reward' => 100,
                'status' => 'pending'
            ]);

            Log::info('Referral record created successfully: ' . $referral->id);
            DB::commit();
            
            return $referral;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Referral processing error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Complete a referral and award credits
     */
    public function completeReferral($referralId, $orderAmount = null)
    {
        try {
            DB::beginTransaction();

            $referral = Referral::with(['referrer', 'referred'])->find($referralId);
            
            if (!$referral) {
                throw new \Exception('Referral not found');
            }

            if ($referral->status !== 'pending') {
                throw new \Exception('Referral is already processed');
            }

            // Calculate reward amount (10% of order amount, max $100)
            $rewardAmount = $orderAmount ? min($orderAmount * 0.10, 100) : 100;
            
            // Check if referrer has reached the $500 limit
            $totalEarned = $referral->referrer->getTotalReferralEarnings();
            $remainingPotential = 500 - $totalEarned;
            
            if ($remainingPotential <= 0) {
                throw new \Exception('Referrer has reached the maximum earnings limit');
            }

            // Adjust reward if it would exceed the $500 limit
            $actualReward = min($rewardAmount, $remainingPotential);

            // Update referral status
            $referral->update([
                'status' => 'completed',
                'reward_amount' => $actualReward,
                'completed_at' => now()
            ]);

            // Add credits to referrer's wallet
            $this->addCreditsToWallet($referral->referrer, $actualReward, $referral);

            Log::info("Referral {$referralId} completed successfully. Reward: \${$actualReward}");

            DB::commit();
            
            return $referral;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Referral completion error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Add credits to user's wallet
     */
    private function addCreditsToWallet($user, $amount, $referral = null)
    {
        // Find or create wallet for the user
        $wallet = Wallet::where('user_id', $user->id)->first();
        
        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'remaining_balance' => 0,
                'withdraw_amount' => 0,
                'status' => 1
            ]);
        }

        // Add the referral reward to both balance and remaining_balance
        $wallet->increment('balance', $amount);
        $wallet->increment('remaining_balance', $amount);

        // Also add to UserEarning for withdrawable balance
        $userEarning = \App\Models\UserEarning::where('user_id', $user->id)->first();
        if (!$userEarning) {
            $userEarning = \App\Models\UserEarning::create([
                'user_id' => $user->id,
                'total_earning' => 0,
                'total_withdraw' => 0,
                'remaining_balance' => 0
            ]);
        }
        
        // Update UserEarning to make referral earnings withdrawable
        $userEarning->increment('total_earning', $amount);
        $userEarning->increment('remaining_balance', $amount);

        // Create wallet history record for tracking
        $transactionId = 'REF_' . time() . '_' . $user->id;
        
        WalletHistory::create([
            'user_id' => $user->id,
            'payment_gateway' => 'referral_reward',
            'payment_status' => 'complete',
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'status' => 1,
            'email_send' => 0
        ]);

        // Log the transaction
        Log::info("Added referral reward of \${$amount} to user {$user->id} wallet and UserEarning. Transaction ID: {$transactionId}");
        
        // Log referral details if available
        if ($referral) {
            Log::info("Referral ID: {$referral->id}, Referred User: {$referral->referred_id}");
        }
    }

    /**
     * Get referral statistics for a user
     */
    public function getReferralStats($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return null;
        }

        return [
            'total_earnings' => $user->getTotalReferralEarnings(),
            'remaining_potential' => $user->getRemainingReferralPotential(),
            'pending_referrals' => $user->getPendingReferralsCount(),
            'completed_referrals' => $user->getCompletedReferralsCount(),
            'has_reached_limit' => $user->hasReachedReferralLimit(),
            'referral_code' => $user->generateReferralCode()
        ];
    }

    /**
     * Get all referrals for a user
     */
    public function getUserReferrals($userId, $status = null)
    {
        $query = Referral::with(['referred'])
            ->where('referrer_id', $userId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Send referral invitation emails
     */
    public function sendReferralInvitations($referrerId, $emails)
    {
        $referrer = User::find($referrerId);
        
        if (!$referrer) {
            throw new \Exception('Referrer not found');
        }

        $referralCode = $referrer->generateReferralCode();
        $referralLink = route('user.register', ['ref' => $referralCode]);
        
        $sentCount = 0;
        $errors = [];

        foreach ($emails as $email) {
            try {
                // Send email invitation
                $this->sendReferralEmail($email, $referrer, $referralLink);
                $sentCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to send to {$email}: " . $e->getMessage();
            }
        }

        return [
            'sent_count' => $sentCount,
            'errors' => $errors
        ];
    }

    /**
     * Send referral invitation email
     */
    private function sendReferralEmail($email, $referrer, $referralLink)
    {
        // You can customize this email template
        $subject = "Join Right Freelancer - Get 10% off your first order!";
        
        $message = "
        Hi there!
        
        {$referrer->first_name} {$referrer->last_name} thinks you'd love Right Freelancer!
        
        Join now and get 10% off your first order when you use this link:
        {$referralLink}
        
        Right Freelancer connects talented freelancers with amazing projects.
        
        Best regards,
        The Right Freelancer Team
        ";

        // Use your existing email system to send this
        // For now, we'll just log it
        Log::info("Referral email sent to {$email} from {$referrer->email}");
        
        // TODO: Implement actual email sending using your email service
        // Mail::to($email)->send(new ReferralInvitation($referrer, $referralLink));
    }
} 