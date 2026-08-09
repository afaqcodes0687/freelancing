<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AffiliateProgram;
use App\Models\AffiliateClick;

class AffiliateClickController extends Controller
{
    public function index(Request $request)
    {
        $affiliateId = session('logged_in_affiliate_id');

        if (!$affiliateId) {
            return redirect()->route('affiliate.login');
        }

        $affiliate = AffiliateProgram::find($affiliateId);

        // Fetch clicks for this affiliate
    $clicks = AffiliateClick::where('affiliate_id', $affiliateId)
                            ->latest()
                            ->paginate(25, ['*'], 'page', 1); 

        $step1Complete = true; // avoid sidebar error

        return view('frontend.user.affiliate.clicks.index', compact('affiliate', 'clicks', 'step1Complete'));
    }

}
