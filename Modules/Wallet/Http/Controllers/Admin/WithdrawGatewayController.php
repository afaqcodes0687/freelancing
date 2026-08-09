<?php

namespace Modules\Wallet\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\BasicMail;
use App\Mail\WithdrawalStatusUpdate;
use App\Models\User;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WithdrawGateway;
use Modules\Wallet\Entities\WithdrawRequest;
use Modules\Wallet\Http\Requests\StoreGatewayRequest;
use File;
use App\Models\AffiliateCommission;
use App\Models\AffiliateProgram;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawGatewayController extends Controller
{
    public function gateway_settings()
    {
       
        $gateways  = WithdrawGateway::latest()->get();
        return view('wallet::admin.withdraw.gateways',compact('gateways'));
    }

    public function gateway_create(StoreGatewayRequest $request){
        $data = WithdrawGateway::create($request->validated());
        return back()->with(["status" => (bool)$data, "msg" => $data ? toastr_success( __("Payment Gateway Created Successfully.")) : toastr_warning(__("Failed to create payment gateway try again."))]);
    }

    public function gateway_update(StoreGatewayRequest $request){
        $data = $request->validated();

        $id = $data["id"];
        unset($data["id"]);

        $data = WithdrawGateway::where("id", $id)->update($data);

        return back()->with(["status" => (bool)$data, "msg" => $data ? toastr_success(__("Payment Gateway Updated Successfully.")) : toastr_warning(__("Failed to update payment gateway try again."))]);
    }

    public function delete_gateway($id){
        WithdrawGateway::where('id', $id)->delete();
        return back()->with(toastr_success(__("Payment Gateway Deleted Successfully.")));
    }

    public function change_status($id){
        $gateway = WithdrawGateway::findOrFail($id);
        $gateway->status == 1 ? $status = 2 : $status = 1;
        WithdrawGateway::where('id', $id)->update(['status' => $status]);
        return back()->with(toastr_success(__("Status Successfully Changed.")));
    }

    //withdraw amount settings
    public function withdraw_settings(Request $request)
    {
        $request->validate([
            'minimum_withdraw_amount'=>'numeric|gt:0',
            'maximum_withdraw_amount'=>'numeric|gt:0',
        ],
            [
                'minimum_withdraw_amount.numeric'=>'Please enter only numeric value.',
                'maximum_withdraw_amount.numeric'=>'Please enter only numeric value.'
            ]);
        if($request->isMethod('post')){
            $fields = ['minimum_withdraw_amount','maximum_withdraw_amount'];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            toastr_success(__('Update Success'));
            return back();
        }
        return view('wallet::admin.wallet.withdraw-settings');
    }

    public function withdraw_request()
    {
        $all_request  = WithdrawRequest::whereHas('user')->latest()->paginate(10);
        return view('wallet::admin.withdraw.requests',compact('all_request'));
    }

    public function withdraw_request_update(Request $request)
    {
        $request->validate([
            'status' => 'required'
        ]);
        $withdraw_request = WithdrawRequest::where('id',$request->request_id)->first();
        $user_wallet_balance = Wallet::where('user_id',$withdraw_request->user_id)->first();


        $deleteOldImage =  'assets/uploads/withdraw-request/'.$withdraw_request->image;
        if($image = $request->file('image')){
            if(file_exists($deleteOldImage)){
                File::delete($deleteOldImage);
            }
            $image_original_name = $request->image->getClientOriginalName();
            $image_name = $image_original_name.'-'.time().'-'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move('assets/uploads/withdraw-request', $image_name);
        }else{
            $image_name = $withdraw_request->image;
        }

        WithdrawRequest::where('id',$request->request_id)->update([
            'status' => $request->status,
            'note' => $request->note,
            'image' => $image_name
        ]);

        if($request->status == 3){
            Wallet::where('user_id',$withdraw_request->user_id)->update([
                'balance' => $user_wallet_balance->balance + $withdraw_request->amount + get_static_option('withdraw_fee'),
                'remaining_balance' => $user_wallet_balance->remaining_balance + $withdraw_request->amount + get_static_option('withdraw_fee'),
            ]);
        }

        // When admin marks withdraw as complete, approve and credit all pending commissions
        if ($request->status == 2) {
            try {
                DB::beginTransaction();

                $pending = AffiliateCommission::where('status', 'pending')
                    ->where('user_id', $withdraw_request->user_id)
                    ->get();

                foreach ($pending as $commission) {
                    $order = Order::find($commission->order_id);
                    if ($order && (int)$order->status === 3) {
                        $affiliate = AffiliateProgram::find($commission->affiliate_id);
                        if ($affiliate) {
                            $affiliate->balance = (float)$affiliate->balance + (float)$commission->commission_amount;
                            $affiliate->save();

                            $commission->status = 'approved';
                            $commission->save();
                        }
                    }
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('Affiliate commission approval on withdraw complete failed: '.$e->getMessage());
            }
        }

        if($request->status == 1){
            $status_text = __('pending');
            $payment_status = 'pending';
        }
        if($request->status == 2){
            $status_text = __('complete');
            $payment_status = 'success';
        }
        if($request->status == 3){
            $status_text = __('cancel');
            $payment_status = 'cancel';
        }
        if($request->status == 4){
            $status_text = __('processing');
            $payment_status = 'processing';
        }

        // Update WalletHistory payment_status for this withdrawal
        \Modules\Wallet\Entities\WalletHistory::where('transaction_id', (string)$withdraw_request->id)
            ->where('payment_gateway', 'withdraw')
            ->update([
                'payment_status' => $payment_status
            ]);

        freelancer_notification($request->request_id,$withdraw_request->user_id,'Withdraw', __('Your withdraw request status changed to') .' '. $status_text);
        
        // Send email notification to freelancer
        $this->sendWithdrawStatusChangeEmail($withdraw_request, $status_text, $payment_status);
        
        return back()->with(toastr_success(__('Status Successfully Updated')));
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $all_request = WithdrawRequest::latest()->paginate(10);
            return view('wallet::admin.withdraw.search-result', compact('all_request'))->render();
        }
    }


    //client withdraw
    public function enable_disable(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate(['client_withdraw_enable_disable' => 'required']);
            $all_fields = ['client_withdraw_enable_disable'];

            foreach ($all_fields as $field) {
                update_static_option($field, $request->$field);
            }
            toastr_success(__('Client Withdraw Enable Disable Settings Updated Successfully.'));
            return back();
        }
        return view('wallet::admin.withdraw.client-withdraw-enable-disable');
    }
    
    private function sendWithdrawStatusChangeEmail($withdraw_request, $status_text, $payment_status)
    {
        $user = User::find($withdraw_request->user_id);
        if (!$user) return;
        
        try {
            // Determine styling based on status
            $data = [];
            
            switch ($payment_status) {
                case 'success':
                    $data['header_class'] = 'success';
                    $data['header_icon'] = '✅';
                    $data['header_message'] = 'Your withdrawal has been successfully processed';
                    $data['details_class'] = 'success';
                    $data['status_class'] = 'status-success';
                    $data['update_box_class'] = 'success';
                    $data['update_title'] = '✅ Withdrawal Completed';
                    $data['update_message'] = 'Your withdrawal has been successfully processed and funds have been sent to your selected payment method. Thank you for using our platform!';
                    $data['button_class'] = 'success';
                    $data['show_button'] = true;
                    break;
                    
                case 'cancel':
                    $data['header_class'] = 'cancelled';
                    $data['header_icon'] = '❌';
                    $data['header_message'] = 'Your withdrawal request has been cancelled';
                    $data['details_class'] = 'cancelled';
                    $data['status_class'] = 'status-cancelled';
                    $data['update_box_class'] = 'cancelled';
                    $data['update_title'] = '❌ Withdrawal Cancelled';
                    $data['update_message'] = 'Your withdrawal request has been cancelled. If you have any questions, please contact our support team. Note: The amount has been credited back to your wallet.';
                    $data['button_class'] = 'cancelled';
                    $data['show_button'] = true;
                    break;
                    
                case 'processing':
                    $data['header_class'] = 'processing';
                    $data['header_icon'] = '⏳';
                    $data['header_message'] = 'Your withdrawal is being processed';
                    $data['details_class'] = 'processing';
                    $data['status_class'] = 'status-processing';
                    $data['update_box_class'] = 'processing';
                    $data['update_title'] = '⏳ Withdrawal Processing';
                    $data['update_message'] = 'Your withdrawal is currently being processed. You will receive another notification once process is complete. Thank you for your patience!';
                    $data['button_class'] = 'processing';
                    $data['show_button'] = true;
                    break;
                    
                default: // pending
                    $data['header_class'] = 'pending';
                    $data['header_icon'] = '⏱️';
                    $data['header_message'] = 'Your withdrawal is under review';
                    $data['details_class'] = 'pending';
                    $data['status_class'] = 'status-pending';
                    $data['update_box_class'] = 'pending';
                    $data['update_title'] = '⏱️ Withdrawal Under Review';
                    $data['update_message'] = 'Your withdrawal request is still under review. We will notify you once there is an update. Thank you for your patience!';
                    $data['button_class'] = 'pending';
                    $data['show_button'] = true;
                    break;
            }
            
            $data['status_text'] = $status_text;
            
            Mail::to($user->email)
                ->send(new WithdrawalStatusUpdate($user, $withdraw_request, $data));
            
        } catch (\Exception $e) {
            // Log error if needed
        }
    }
}
