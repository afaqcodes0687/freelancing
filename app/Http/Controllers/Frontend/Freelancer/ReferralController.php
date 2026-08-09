<?php

namespace App\Http\Controllers\Frontend\Freelancer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Referral;
use App\Models\ReferralInvitation;

class ReferralController extends Controller
{
     public function referralList()
    {
        $userId = auth()->id();

        // Fetch metrics
        $referrals = Referral::with(['referrer', 'referred'])
            ->where('referrer_id', $userId)
            ->latest()
            ->get();

        $totalInvited = ReferralInvitation::where('user_id', $userId)->count();
        $rewardedFriends = Referral::getCompletedCount($userId);
        $totalEarning = Referral::getTotalEarnings($userId);
        $rewardsInProgress = Referral::getPendingCount($userId);

        // Calculate badge progress
        $badgeProgress = $this->calculateBadgeProgress($userId);

        return view('frontend.user.freelancer.referral.referral-list', compact(
            'referrals', 'totalInvited', 'rewardedFriends', 'totalEarning', 'rewardsInProgress', 'badgeProgress'
        ));
    }

    /**
     * Calculate badge progress for a user
     */
    private function calculateBadgeProgress($userId)
    {
        $completedReferrals = Referral::getCompletedCount($userId);
        
        $badges = [
            [
                'name' => 'Referral Starter',
                'required' => 5,
                'reward' => 10,
                'icon' => 'images (1).png',
                'completed_icon' => '1-Badge-Green.svg'
            ],
            [
                'name' => 'Referral Influencer',
                'required' => 10,
                'reward' => 20,
                'icon' => 'images (2).png',
                'completed_icon' => '2-Badge-Green.svg'
            ],
            [
                'name' => 'Referral Expert',
                'required' => 20,
                'reward' => 30,
                'icon' => 'images.png',
                'completed_icon' => '3-Badge-Green.svg'
            ],
            [
                'name' => 'Referral Master',
                'required' => 30,
                'reward' => 40,
                'icon' => '5957125.png',
                'completed_icon' => '4-Badge-Green.svg'
            ],
            [
                'name' => 'Referral Champion',
                'required' => 50,
                'reward' => 100,
                'icon' => 'download.jpg',
                'completed_icon' => '5-Badge-Green.svg'
            ]
        ];

        foreach ($badges as &$badge) {
            $progress = Referral::getBadgeProgress($userId, $badge['required']);
            $badge['current'] = $progress['current'];
            $badge['progress_percentage'] = $progress['percentage'];
            $badge['is_completed'] = $progress['is_completed'];
            $badge['is_unlocked'] = $progress['is_completed'];
        }

        return $badges;
    }
}
