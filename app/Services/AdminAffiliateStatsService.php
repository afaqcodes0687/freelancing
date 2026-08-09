<?php

namespace App\Services;

use App\Models\AffiliateProgram;
use App\Models\AffiliateCommission;
use App\Models\AffiliateClick;
use App\Models\AffiliatePayout;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminAffiliateStatsService
{
    /**
     * Get overall affiliate system stats.
     */
    public function getGlobalStats(): array
    {
        return [
            'total_affiliates' => AffiliateProgram::count(),
            'total_user_referrers' => User::whereNotNull('referral_code')->count(),
            'total_clicks' => AffiliateClick::count(),
            'total_commissions_approved' => AffiliateCommission::where('status', 'approved')->sum('commission_amount'),
            'total_commissions_pending' => AffiliateCommission::where('status', 'pending')->sum('commission_amount'),
            'total_payouts_paid' => AffiliatePayout::where('status', 'paid')->sum('amount'),
            'total_payouts_pending' => AffiliatePayout::where('status', 'pending')->sum('amount'),
            'active_referrals' => Referral::count(),
        ];
    }

    /**
     * Get stats for a specific affiliate.
     */
    public function getAffiliateStats(int $affiliateId): array
    {
        $affiliate = AffiliateProgram::find($affiliateId);
        if (!$affiliate)
            return [];

        return [
            'balance' => $affiliate->balance,
            'total_earned' => $affiliate->total_earned,
            'clicks_count' => $affiliate->clicks()->count(),
            'conversions_count' => $affiliate->commissions()->count(),
            'payouts_total' => $affiliate->payouts()->where('status', 'paid')->sum('amount'),
            'pending_payouts' => $affiliate->payouts()->where('status', 'pending')->sum('amount'),
        ];
    }

    /**
     * Get conversion rate trends (last 30 days).
     */
    public function getTrends(): array
    {
        $days = 30;
        $stats = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $stats[$date] = [
                'clicks' => AffiliateClick::whereDate('clicked_at', $date)->count(),
                'commissions' => AffiliateCommission::whereDate('created_at', $date)->count(),
            ];
        }
        return $stats;
    }
}
