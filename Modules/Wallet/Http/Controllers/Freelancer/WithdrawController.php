<?php

namespace Modules\Wallet\Http\Controllers\Freelancer;

use App\Helper\LogActivity;
use App\Mail\BasicMail;
use App\Mail\WithdrawalRequestSubmitted;
use App\Mail\AdminWithdrawalNotification;
use App\Models\User;
use App\Models\UserEarning;
use Modules\Wallet\Entities\WithdrawRequest;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Http\Requests\FreelancerHandleWithdrawRequest;

class WithdrawController extends Controller
{
    public function withdraw_request(FreelancerHandleWithdrawRequest $request)
    {
        $data = $request->validated();

        $wallet = Wallet::where("user_id", $data["user_id"])->first();

        $total_deduction = $data["withdraw_gross_amount"];
        $payout_amount = $data["amount"];

        if ($total_deduction < get_static_option('minimum_withdraw_amount') || $total_deduction > get_static_option('maximum_withdraw_amount')) {
            return back()->with(toastr_warning(__("Please enter a valid amount between " . float_amount_with_currency_symbol(get_static_option('minimum_withdraw_amount')) . '-' . float_amount_with_currency_symbol(get_static_option('maximum_withdraw_amount')))));
        }

        if ($wallet->balance >= $total_deduction) {

            $signup_bonus = $wallet->signup_bonus ?? 0;
            $withdrawable = max(0, $wallet->balance - $signup_bonus);

            if ($withdrawable <= 0 || $total_deduction > $withdrawable) {
                return back()->with(toastr_warning(
                    __('You can only withdraw your earned balance. The $10 signup bonus cannot be withdrawn.')
                ));
            }

            $user_earning = UserEarning::where('user_id', $data["user_id"])->first();
            if (!$user_earning || $user_earning->remaining_balance < $total_deduction) {
                return back()->with(toastr_warning('Insufficient earnings balance'));
            }

            $data['amount'] = $payout_amount;
            $withdraw = WithdrawRequest::create($data);

            // Record this withdrawal request in the wallet history
            \Modules\Wallet\Entities\WalletHistory::create([
                'user_id' => $withdraw->user_id,
                'amount' => $total_deduction,
                'payment_gateway' => 'withdraw',
                'payment_status' => 'pending',
                'transaction_id' => $withdraw->id,
                'status' => 1,
            ]);

            // Update wallet balance (total balance)
            Wallet::where('user_id', $withdraw->user_id)->update([
                'balance' => $wallet->balance - $total_deduction,
                'remaining_balance' => ($wallet->remaining_balance ?? 0) - $total_deduction,
                'withdraw_amount' => ($wallet->withdraw_amount ?? 0) + $total_deduction
            ]);

            // Update user earnings (actual earnings)
            UserEarning::where('user_id', $withdraw->user_id)->update([
                'total_withdraw' => ($user_earning->total_withdraw ?? 0) + $total_deduction,
                'remaining_balance' => $user_earning->remaining_balance - $total_deduction
            ]);

            // Clear wallet cache for the user
            if (function_exists('cache')) {
                cache()->forget('user_wallet_' . $withdraw->user_id);
                cache()->forget('user_earnings_' . $withdraw->user_id);
            }

            //security manage
            if (moduleExists('SecurityManage')) {
                LogActivity::addToLog('Withdraw request', 'Freelancer');
            }

            notificationToAdmin($withdraw->id, $withdraw->user_id, 'Withdraw', 'New withdraw request');
            
            // Send email notifications
            $this->sendWithdrawRequestEmails($withdraw);
            
            return redirect()->route('freelancer.wallet.history')->with(toastr_success(__("Successfully sent your request")));
        }

        return back()->with(toastr_warning('Your requested amount is greater than your wallet balance'));
    }

    public function withdraw_history()
    {
        $all_request = WithdrawRequest::where('user_id', auth()->user()->id)->latest()->paginate(10);
        return view('wallet::freelancer.withdraw.requests', compact('all_request'));
    }

    // pagination
    function pagination(Request $request)
    {
        if ($request->ajax()) {
            $all_request = WithdrawRequest::where('user_id', auth()->user()->id)->latest()->paginate(10);
            return view('wallet::freelancer.withdraw.search-result', compact('all_request'))->render();
        }
    }
    
    private function sendWithdrawRequestEmails($withdraw)
    {
        $user = User::find($withdraw->user_id);
        if (!$user) return;
        
        // Send email to admin
        try {
            Mail::to(get_static_option('site_global_email'))
                ->send(new AdminWithdrawalNotification($user, $withdraw));
        } catch (\Exception $e) {
            // Log error if needed
        }
        
        // Send email to freelancer
        try {
            Mail::to($user->email)
                ->send(new WithdrawalRequestSubmitted($user, $withdraw));
        } catch (\Exception $e) {
            // Log error if needed
        }
    }
}
