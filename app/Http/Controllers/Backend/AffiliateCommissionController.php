<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AffiliateCommission;
use App\Models\AffiliateProgram;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AffiliateCommissionController extends Controller
{
    public function index(Request $request)
    {
        // show pending by default, allow filter
        $status = $request->get('status');
        $search = $request->get('string_search');

        $query = AffiliateCommission::with('affiliate')->orderBy('created_at', 'desc');

        if ($status && $status != 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhereHas('affiliate', function ($q2) use ($search) {
                        $q2->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        $commissions = $query->paginate(25);

        if ($request->ajax()) {
            return view('backend.pages.affiliate-commissions.index', compact('commissions', 'status', 'all_count', 'pending_count', 'approved_count', 'rejected_count'))->fragment('search-results');
        }

        // Counts for tabs
        $all_count = AffiliateCommission::count();
        $pending_count = AffiliateCommission::where('status', 'pending')->count();
        $approved_count = AffiliateCommission::where('status', 'approved')->count();
        $rejected_count = AffiliateCommission::where('status', 'rejected')->count();

        return view('backend.pages.affiliate-commissions.index', compact('commissions', 'status', 'all_count', 'pending_count', 'approved_count', 'rejected_count'));
    }

    public function approve(Request $request, $id)
    {
        $commission = AffiliateCommission::findOrFail($id);

        if ($commission->status !== 'pending') {
            return response()->json(['status' => 'error', 'msg' => 'Only pending commissions can be approved.'], 400);
        }

        DB::beginTransaction();
        try {
            if ($commission->referrer_user_id) {
                // Unified: Credit User Wallet
                $this->creditUserWallet($commission->referrer_user_id, $commission->commission_amount, $commission);
            } elseif ($commission->affiliate_id) {
                // Legacy/Affiliate: Update balance
                $affiliate = AffiliateProgram::findOrFail($commission->affiliate_id);
                $affiliate->increment('balance', $commission->commission_amount);
                $affiliate->increment('total_earned', $commission->commission_amount);
            }

            // mark commission approved
            $commission->status = 'approved';
            $commission->save();

            // Notify (Simplified)
            $email = $commission->referrer_user_id ? \App\Models\User::find($commission->referrer_user_id)?->email : \App\Models\AffiliateProgram::find($commission->affiliate_id)?->email;
            if ($email) {
                try {
                    Mail::to($email)->send(new \App\Mail\BasicMail([
                        'subject' => 'Your commission has been approved',
                        'message' => "Your commission (ID: {$commission->id}) of {$commission->commission_amount} has been approved."
                    ]));
                } catch (\Exception $e) {
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'msg' => 'Commission approved.']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Approve commission error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'msg' => 'Something went wrong.'], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $commission = AffiliateCommission::findOrFail($id);
        if ($commission->status !== 'pending') {
            return response()->json(['status' => 'error', 'msg' => 'Only pending commissions can be rejected.'], 400);
        }

        $commission->status = 'rejected';
        $commission->approval_token = null;
        $commission->save();

        // send email to affiliate
        try {
            $affiliate = AffiliateProgram::find($commission->affiliate_id);
            if ($affiliate) {
                Mail::to($affiliate->email)->send(new \App\Mail\BasicMail([
                    'subject' => 'Commission rejected',
                    'message' => "Hi {$affiliate->first_name},<br>Your commission (ID: {$commission->id}) was rejected by admin."
                ]));
            }
        } catch (\Exception $e) {
            \Log::error('Commission reject email failed: ' . $e->getMessage());
        }

        return response()->json(['status' => 'success', 'msg' => 'Commission rejected.']);
    }
    protected function creditUserWallet($userId, $amount, $commission)
    {
        $wallet = \Modules\Wallet\Entities\Wallet::firstOrCreate(['user_id' => $userId], [
            'balance' => 0,
            'remaining_balance' => 0,
            'status' => 1
        ]);
        $wallet->increment('balance', $amount);
        $wallet->increment('remaining_balance', $amount);
        \Modules\Wallet\Entities\WalletHistory::create([
            'user_id' => $userId,
            'payment_gateway' => 'referral_commission',
            'payment_status' => 'complete',
            'amount' => $amount,
            'transaction_id' => 'COMM_' . $commission->id,
            'status' => 1
        ]);
    }
}
