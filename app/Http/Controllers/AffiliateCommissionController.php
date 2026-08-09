<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AffiliateProgram;
use App\Models\AffiliateCommission;

class AffiliateCommissionController extends Controller
{
    public function index()
    {
        $affiliateId = session('logged_in_affiliate_id');
        if (!$affiliateId) return redirect()->route('affiliate.login');

        $affiliate = AffiliateProgram::find($affiliateId);
        $commissions = AffiliateCommission::where('affiliate_id', $affiliateId)->latest()->paginate(20);
        $step1Complete = $affiliate->first_name && $affiliate->last_name && $affiliate->email;

        return view('frontend.user.affiliate.commissions.index', compact('affiliate','commissions', 'step1Complete'));
    }

    public function show($id)
    {
        $affiliateId = session('logged_in_affiliate_id');
        $commission = AffiliateCommission::where('id',$id)->where('affiliate_id',$affiliateId)->firstOrFail();

        return view('frontend.user.affiliate.commissions.show', compact('commission'));
    }
}
