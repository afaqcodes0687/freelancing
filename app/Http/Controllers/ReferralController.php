<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Referral;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReferralController extends Controller
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Process a new referral when user registers
     */
    public function processReferral(Request $request)
    {
        try {
            $request->validate([
                'referral_code' => 'required|string',
                'user_id' => 'required|integer|exists:users,id'
            ]);

            $referral = $this->referralService->processReferral(
                $request->referral_code,
                $request->user_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Referral processed successfully',
                'referral' => $referral
            ]);

        } catch (\Exception $e) {
            Log::error('Referral processing failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Complete a referral and award wallet credits
     */
    public function completeReferral(Request $request)
    {
        try {
            $request->validate([
                'referral_id' => 'required|integer|exists:referrals,id',
                'order_amount' => 'nullable|numeric|min:0'
            ]);

            $referral = $this->referralService->completeReferral(
                $request->referral_id,
                $request->order_amount
            );

            // Get updated wallet balance
            $wallet = $referral->referrer->user_wallet;
            $walletBalance = $wallet ? $wallet->balance : 0;

            return response()->json([
                'success' => true,
                'message' => 'Referral completed successfully. Wallet credited!',
                'referral' => $referral,
                'wallet_balance' => $walletBalance,
                'reward_amount' => $referral->reward_amount
            ]);

        } catch (\Exception $e) {
            Log::error('Referral completion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get referral statistics for a user
     */
    public function getReferralStats(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer|exists:users,id'
            ]);

            $stats = $this->referralService->getReferralStats($request->user_id);

            if (!$stats) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get referral stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get user's referral history
     */
    public function getReferralHistory(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'status' => 'nullable|string|in:pending,completed'
            ]);

            $referrals = $this->referralService->getUserReferrals(
                $request->user_id,
                $request->status
            );

            return response()->json([
                'success' => true,
                'referrals' => $referrals
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get referral history: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Send referral invitations
     */
    public function sendInvitations(Request $request)
    {
        try {
            $request->validate([
                'referrer_id' => 'required|integer|exists:users,id',
                'emails' => 'required|array',
                'emails.*' => 'email'
            ]);

            $result = $this->referralService->sendReferralInvitations(
                $request->referrer_id,
                $request->emails
            );

            return response()->json([
                'success' => true,
                'message' => "Invitations sent successfully. {$result['sent_count']} emails sent.",
                'result' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send referral invitations: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Test method to manually complete a referral (for testing purposes)
     */
    public function testCompleteReferral(Request $request)
    {
        try {
            $request->validate([
                'referral_id' => 'required|integer|exists:referrals,id'
            ]);

            $referral = Referral::with(['referrer', 'referred'])->find($request->referral_id);
            
            if (!$referral) {
                return response()->json([
                    'success' => false,
                    'message' => 'Referral not found'
                ], 404);
            }

            // Complete the referral with a test order amount
            $completedReferral = $this->referralService->completeReferral(
                $request->referral_id,
                1000 // $1000 order amount for testing
            );

            // Get wallet details
            $wallet = $completedReferral->referrer->user_wallet;
            $walletBalance = $wallet ? $wallet->balance : 0;

            return response()->json([
                'success' => true,
                'message' => 'Test referral completed successfully!',
                'referral' => $completedReferral,
                'wallet_balance' => $walletBalance,
                'reward_amount' => $completedReferral->reward_amount,
                'referrer_name' => $completedReferral->referrer->first_name . ' ' . $completedReferral->referrer->last_name,
                'referred_name' => $completedReferral->referred->first_name . ' ' . $completedReferral->referred->last_name
            ]);

        } catch (\Exception $e) {
            Log::error('Test referral completion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
} 