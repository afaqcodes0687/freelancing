<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AffiliateProgram;
use App\Models\AffiliatePayout;
use App\Models\AffiliateCommission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\BasicMail;

class AffiliatePayoutController extends Controller
{
    public function index()
    {
        $affiliateId = session('logged_in_affiliate_id');
        if (!$affiliateId)
            return redirect()->route('affiliate.login');

        $affiliate = AffiliateProgram::find($affiliateId);
        $payouts = AffiliatePayout::where('affiliate_id', $affiliateId)->latest()->paginate(20);
        $step1Complete = $affiliate->first_name && $affiliate->last_name && $affiliate->email;
        $minPayout = function_exists('get_static_option') ? (float) (get_static_option('affiliate_min_payout') ?? 100) : 100;

        return view('frontend.user.affiliate.payouts.index', compact('affiliate', 'payouts', 'step1Complete', 'minPayout'));
    }

    public function requestPayout(Request $request)
    {
        $affiliateId = session('logged_in_affiliate_id');
        if (!$affiliateId) {
            return response()->json(['status' => 'error', 'msg' => 'Please login first.']);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'account_details' => 'required|string|max:500',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'warning',
                'msg' => $validator->errors()->first(),
            ]);
        }

        $affiliate = AffiliateProgram::find($affiliateId);
        if (!$affiliate) {
            return response()->json(['status' => 'error', 'msg' => 'Affiliate not found.']);
        }

        $pendingSum = AffiliatePayout::where('affiliate_id', $affiliateId)->where('status', 'pending')->sum('amount');
        $available = max(0, (float) $affiliate->balance - (float) $pendingSum);

        $minThreshold = function_exists('get_static_option') ? (float) (get_static_option('affiliate_min_payout') ?? 100) : 100;

        if ($request->amount < $minThreshold) {
            return response()->json(['status' => 'warning', 'msg' => 'Amount is below minimum payout threshold.']);
        }

        if ($request->amount > $available) {
            return response()->json(['status' => 'warning', 'msg' => 'Amount exceeds available balance.']);
        }

        try {
            AffiliatePayout::create([
                'affiliate_id' => $affiliateId,
                'amount' => (float) $request->amount,
                'payment_method' => (string) $request->payment_method,
                'account_details' => (string) $request->account_details,
                'status' => 'pending',
            ]);

            // Send email to Affiliate
            try {
                $message_body = '
                    <div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; border-radius: 8px;">
                        <h2 style="color: #333; font-size: 22px;">' . __('Payout Request Received') . '</h2>
                        <p style="font-size: 16px; color: #555;">' . __('Hello') . ' ' . $affiliate->first_name . ',</p>
                        <p style="font-size: 16px; color: #555;">' . __('We have received your payout request for amount:') . ' <strong>' . amount_with_currency_symbol($request->amount) . '</strong></p>
                        <p style="font-size: 14px; color: #999;">' . __('You will be notified once it is approved.') . '</p>
                    </div>
                ';

                Mail::to($affiliate->email)->send(new BasicMail([
                    'subject' => __('Payout Request Received'),
                    'message' => $message_body
                ]));
            } catch (\Exception $e) {
                // Log error if email fails
                \Log::error('Affiliate payout email to user failed: ' . $e->getMessage());
            }

            // Send email to Admin
            try {
                $admin_email = get_static_option('site_global_email') ?? 'admin@test.com'; // Fallback if option not set
                $admin_message = '
                    <div style="font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; border-radius: 8px;">
                        <h2 style="color: #333; font-size: 22px;">' . __('New Payout Request') . '</h2>
                        <p style="font-size: 16px; color: #555;">' . __('Affiliate') . ': ' . $affiliate->first_name . ' ' . $affiliate->last_name . ' (' . $affiliate->email . ')</p>
                        <p style="font-size: 16px; color: #555;">' . __('Amount') . ': <strong>' . amount_with_currency_symbol($request->amount) . '</strong></p>
                        <p style="font-size: 16px; color: #555;">' . __('Payment Method') . ': ' . $request->payment_method . '</p>
                        <p style="font-size: 14px; color: #999;">' . __('Please review and approve via the admin panel.') . '</p>
                    </div>
                ';

                Mail::to($admin_email)->send(new BasicMail([
                    'subject' => __('New Affiliate Payout Request'),
                    'message' => $admin_message
                ]));
            } catch (\Exception $e) {
                \Log::error('Affiliate payout email to admin failed: ' . $e->getMessage());
            }

            return response()->json(['status' => 'success', 'msg' => 'Payout request submitted.']);
        } catch (\Exception $e) {
            \Log::error('Affiliate payout create failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'msg' => 'Server error. Please try again later.']);
        }
    }
}

