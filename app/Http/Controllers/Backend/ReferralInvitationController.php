<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ReferralInvitation;
use App\Models\Referral;


class ReferralInvitationController extends Controller
{
    public function index()
    {
        $referrals = ReferralInvitation::with('user')->latest()->paginate(10);
        return view('backend.pages.referral_invitations.index', compact('referrals'));
    }

    public function referrals()
    {
        $referrals_email = Referral::with(['referrer', 'referred'])->latest()->paginate(10);
        return view('backend.pages.referrals.index', compact('referrals_email'));
    }

}
