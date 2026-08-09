<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Modules\Wallet\Entities\WithdrawRequest;

class WithdrawalStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $withdraw_request;
    public $header_class;
    public $header_icon;
    public $header_message;
    public $details_class;
    public $status_class;
    public $update_box_class;
    public $update_title;
    public $update_message;
    public $button_class;
    public $show_button;
    public $status_text;

    public function __construct(User $user, WithdrawRequest $withdraw_request, array $data = [])
    {
        $this->user = $user;
        $this->withdraw_request = $withdraw_request;
        
        // Set default values
        $this->header_class = $data['header_class'] ?? 'success';
        $this->header_icon = $data['header_icon'] ?? '✅';
        $this->header_message = $data['header_message'] ?? 'Your withdrawal has been updated';
        $this->details_class = $data['details_class'] ?? 'success';
        $this->status_class = $data['status_class'] ?? 'status-success';
        $this->update_box_class = $data['update_box_class'] ?? 'success';
        $this->update_title = $data['update_title'] ?? 'Status Update';
        $this->update_message = $data['update_message'] ?? 'Your withdrawal status has been updated';
        $this->button_class = $data['button_class'] ?? 'success';
        $this->show_button = $data['show_button'] ?? true;
        $this->status_text = $data['status_text'] ?? 'Updated';
    }

    public function build()
    {
        return $this->from(get_static_option('site_global_email'), get_static_option('site_title'))
            ->subject(__('Withdrawal Status Update - ID: ') . $this->withdraw_request->id . ' - ' . $this->status_text)
            ->view('mail.withdrawal-status-update');
    }
}
