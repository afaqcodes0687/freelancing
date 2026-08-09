<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AffiliateProgram;
use App\Models\AffiliateCommission;
use App\Models\AffiliateClick;
use App\Models\AffiliatePayout;
use Carbon\Carbon;

class AffiliateDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // get affiliate id from session (or use Auth if you have affiliate guard)
        $affiliateId = session('logged_in_affiliate_id');

        if (! $affiliateId) {
            return redirect()->route('affiliate.login')->with('error', 'Please login first.');
        }

        $affiliate = AffiliateProgram::withCount('clicks','commissions','payouts')->find($affiliateId);

        if (! $affiliate) {
            return redirect()->route('affiliate.login')->with('error', 'Account not found.');
        }

        // Basic balances
        $balance = $affiliate->balance ?? 0.00;
        // lifetime earnings from commissions (approved or paid)
        $totalEarned = $affiliate->commissions()
            ->whereIn('status', ['approved','paid'])
            ->sum('commission_amount');

        // Pending commission sum
        $pendingCommission = $affiliate->commissions()
            ->where('status', 'pending')
            ->sum('commission_amount');

        // Approved but not paid
        $approvedNotPaid = $affiliate->commissions()
            ->where('status', 'approved')
            ->sum('commission_amount');

        // Pending payouts count + sum
        $pendingPayoutCount = $affiliate->payouts()->where('status','pending')->count();
        $pendingPayoutSum = $affiliate->payouts()->where('status','pending')->sum('amount');

        // Recent lists
        $recentCommissions = $affiliate->commissions()->latest()->limit(8)->get();
        $recentPayouts = $affiliate->payouts()->latest()->limit(8)->get();

        // Clicks & conversions last 30 days (chart)
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();

        $clicks = AffiliateClick::where('affiliate_id', $affiliate->id)
            ->whereBetween('clicked_at', [$start, $end])
            ->selectRaw('DATE(clicked_at) as date, COUNT(*) as clicks')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('clicks','date'); // key: date, value: clicks

        // ensure all 30 days have a value (fill zeros)
        $labels = [];
        $clickValues = [];
        for ($i = 0; $i < 30; $i++) {
            $d = $start->copy()->addDays($i)->format('Y-m-d');
            $labels[] = $d;
            $clickValues[] = (int) ($clicks->has($d) ? $clicks[$d] : 0);
        }

        // conversions in last 30 days (count of commission records)
        $conversionsLast30 = $affiliate->commissions()
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['pending','approved','paid'])
            ->count();

        $clicksTotalLast30 = array_sum($clickValues);
        $conversionRate = $clicksTotalLast30 ? round($conversionsLast30 / $clicksTotalLast30 * 100, 2) : 0;

        // referral link
        $referralLink = url('/') . '?ref=' . $affiliate->referral_code;

        $step1Complete = $affiliate->first_name && $affiliate->last_name && $affiliate->email;


        return view('frontend.user.affiliate.dashboard.dashboard', compact(
            'affiliate',
            'balance',
            'totalEarned',
            'pendingCommission',
            'approvedNotPaid',
            'pendingPayoutCount',
            'pendingPayoutSum',
            'recentCommissions',
            'recentPayouts',
            'labels',
            'clickValues',
            'conversionRate',
            'conversionsLast30',
            'clicksTotalLast30',
            'referralLink', 
            'step1Complete'
        ));
    }
}
