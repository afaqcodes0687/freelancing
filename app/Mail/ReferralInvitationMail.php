<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReferralInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $referrer;
    public $recipientEmail;
    public $referralLink;

    public function __construct(User $referrer, $recipientEmail)
    {
        $this->referrer = $referrer;
        $this->recipientEmail = $recipientEmail;
        $this->referralLink = route('user.register', ['ref' => $referrer->referral_code]);
    }
   public function build()
    {
        return $this->from(get_static_option('site_global_email'), get_static_option('site_title'))
            ->subject($this->referrer->first_name . ' invited you to join Right Freelancer!')
            ->view('mail.referral-invitation')
            ->with([
                'referrer' => $this->referrer,
                'referralLink' => $this->referralLink, 
            ]);
    }

} 