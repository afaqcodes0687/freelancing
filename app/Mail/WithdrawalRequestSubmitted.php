<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Modules\Wallet\Entities\WithdrawRequest;

class WithdrawalRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $withdraw;

    public function __construct(User $user, WithdrawRequest $withdraw)
    {
        $this->user = $user;
        $this->withdraw = $withdraw;
    }

    public function build()
    {
        return $this->from(get_static_option('site_global_email'), get_static_option('site_title'))
            ->subject(__('Withdrawal Request Submitted - ID: ') . $this->withdraw->id)
            ->view('mail.withdrawal-request-submitted');
    }
}
