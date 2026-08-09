<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AffiliateSupport;

class AffiliateSupportController extends Controller
{
    public function index()
    {
        $affiliateId = session('logged_in_affiliate_id');
        if (!$affiliateId) return redirect()->route('affiliate.login');

        return view('frontend.user.affiliate.support.index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $affiliateId = session('logged_in_affiliate_id');
        if (!$affiliateId) {
            return response()->json(['status' => 'error', 'msg' => 'Not authenticated'], 401);
        }

        AffiliateSupport::create([
            'affiliate_id' => $affiliateId,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // ✅ Return JSON response for AJAX
        return response()->json(['status' => 'success']);
    }
}
