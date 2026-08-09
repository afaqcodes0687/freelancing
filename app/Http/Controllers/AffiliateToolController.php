<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AffiliateProgram;

class AffiliateToolController extends Controller
{
    public function index(Request $request)
    {
        $affiliateId = session('logged_in_affiliate_id');
        if (!$affiliateId)
            return redirect()->route('affiliate.login')->with('error', 'Please login.');

        $affiliate = AffiliateProgram::find($affiliateId);
        if (!$affiliate)
            return redirect()->route('affiliate.login')->with('error', 'Account not found.');

        $referralLink = url('/') . '?ref=' . $affiliate->referral_code;
        $step1Complete = $affiliate->first_name && $affiliate->last_name && $affiliate->email;

        $qrSrc = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($referralLink);

        $banners = [
            [
                'title' => 'Logo Banner (Horizontal)',
                'image' => asset('assets/static/img/logo/logo.png'),
                'width' => 200,
                'height' => 60,
                'html' => '<a href="' . $referralLink . '" target="_blank"><img src="' . asset('assets/static/img/logo/logo.png') . '" width="200" alt="Right Freelancer"></a>'
            ],
            [
                'title' => 'Affiliate Landing Promo',
                'image' => asset('assets/uploads/partnerimage/partnerwithus.png'),
                'width' => 300,
                'height' => 250,
                'html' => '<a href="' . $referralLink . '" target="_blank"><img src="' . asset('assets/uploads/partnerimage/partnerwithus.png') . '" width="300" alt="Join Right Freelancer"></a>'
            ]
        ];

        return view('frontend.user.affiliate.tools.index', compact('affiliate', 'referralLink', 'step1Complete', 'qrSrc', 'banners'));
    }
}
