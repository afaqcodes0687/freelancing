<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AffiliatePayout;
use App\Models\AffiliateProgram;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AffiliatePayoutController extends Controller
{
    // Show list of payout requests (search & filter enabled)
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('string_search');

        $query = AffiliatePayout::with('affiliate')->orderBy('created_at', 'desc');

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

        $payouts = $query->paginate(25);

        if ($request->ajax()) {
            return view('backend.pages.affiliate-payouts.index', compact('payouts', 'all_count', 'pending_count', 'paid_count', 'rejected_count'));
        }



        // Counts for tabs
        $all_count = AffiliatePayout::count();
        $pending_count = AffiliatePayout::where('status', 'pending')->count();
        $paid_count = AffiliatePayout::where('status', 'paid')->count();
        $rejected_count = AffiliatePayout::where('status', 'rejected')->count();

        return view('backend.pages.affiliate-payouts.index', compact('payouts', 'all_count', 'pending_count', 'paid_count', 'rejected_count'));
    }

    // Approve a payout: deduct balance, mark payout paid
    public function approve(Request $request, $id)
    {
        $payout = AffiliatePayout::findOrFail($id);

        if ($payout->status !== 'pending') {
            return response()->json(['status' => 'error', 'msg' => 'Only pending payouts can be approved.'], 400);
        }

        DB::beginTransaction();
        try {
            $affiliate = AffiliateProgram::findOrFail($payout->affiliate_id);

            // Ensure affiliate has enough balance
            if ((float) $affiliate->balance < (float) $payout->amount) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'msg' => 'Affiliate has insufficient balance.'], 400);
            }

            // Deduct balance
            $affiliate->balance = (float) $affiliate->balance - (float) $payout->amount;
            $affiliate->save();

            // mark payout paid
            $payout->status = 'paid';
            $payout->transaction_id = $request->input('transaction_id'); // optional admin add
            $payout->save();

            // send notification email to affiliate (optional)
            try {
                Mail::to($affiliate->email)->send(new \App\Mail\BasicMail([
                    'subject' => 'Your payout has been processed',
                    'message' => "Hello {$affiliate->first_name},<br>Your payout #{$payout->id} of {$payout->amount} has been approved and marked as paid."
                ]));
            } catch (\Exception $e) {
                \Log::error('Payout approved email failed: ' . $e->getMessage());
            }

            DB::commit();
            return response()->json(['status' => 'success', 'msg' => 'Payout approved.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Approve payout error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'msg' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    // Reject a payout request: don't change balance, mark rejected
    public function reject(Request $request, $id)
    {
        $payout = AffiliatePayout::findOrFail($id);

        if ($payout->status !== 'pending') {
            return response()->json(['status' => 'error', 'msg' => 'Only pending payouts can be rejected.'], 400);
        }

        try {
            $payout->status = 'rejected';
            $payout->save();

            // email affiliate (optional)
            $affiliate = AffiliateProgram::find($payout->affiliate_id);
            if ($affiliate) {
                try {
                    Mail::to($affiliate->email)->send(new \App\Mail\BasicMail([
                        'subject' => 'Your payout request was rejected',
                        'message' => "Hello {$affiliate->first_name},<br>Your payout request #{$payout->id} was rejected by admin."
                    ]));
                } catch (\Exception $e) {
                    \Log::error('Payout reject email failed: ' . $e->getMessage());
                }
            }

            return response()->json(['status' => 'success', 'msg' => 'Payout rejected.']);
        } catch (\Throwable $e) {
            \Log::error('Payout reject failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'msg' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // optional: show single payout detail page
    public function show($id)
    {
        $payout = AffiliatePayout::with('affiliate')->findOrFail($id);
        return view('backend.pages.affiliate-payouts.show', compact('payout'));
    }
}
