<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TrustAndSafety;

class TrustAndSafetyController extends Controller
{
    public function edit()
    {
        $trustSafety = TrustAndSafety::first();
        if (!$trustSafety) {
            $trustSafety = TrustAndSafety::create([
                'title' => 'Trust and Safety',
                'meta_title' => 'Trust and Safety - Secure Freelancing with Confidence | Right Freelancer',
                'meta_description' => 'Learn how Right Freelancer ensures a safe and trustworthy platform for both freelancers and clients. Explore our safety protocols, verification process, and dispute resolution systems.',
                'banner_title' => 'Trust & Safety',
                'content_title' => 'Trust & Safety',
                'scam_protection_title' => 'Try to protect yourself from scams by taking note of the following concepts:',
                'scam_protection_points' => [
                    'Phishing: Prevent unscrupulous users from trying to steal your passwords. Double check that links or HTML files clients give you don\'t lead to fake login pages.',
                    'Free work fraud: Don\'t start work before the official contract start date. Never pay anything to work for a client, even if they claim that the money will be reimbursable.',
                    'Check cashing fraud: Report clients who ask you to process PayPal payments, or request favors to cash or deposit checks and money orders in order to send the money somewhere else.'
                ]
            ]);
        }
        return view('backend.trust_and_safety.edit', compact('trustSafety'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:191',
            'meta_title' => 'nullable|string|max:191',
            'meta_description' => 'nullable|string',
            'banner_title' => 'nullable|string|max:191',
            'content_title' => 'nullable|string|max:191',
            'introduction' => 'nullable|string',
            'top_rated_program' => 'nullable|string',
            'communication_importance' => 'nullable|string',
            'escrow_system' => 'nullable|string',
            'customer_support' => 'nullable|string',
            'dispute_resolution' => 'nullable|string',
            'freelancer_profiles' => 'nullable|string',
            'project_guidelines' => 'nullable|string',
            'scam_protection_title' => 'nullable|string|max:191',
            'scam_protection_points' => 'nullable|array',
            'contact_info' => 'nullable|string',
        ]);

        $trustSafety = TrustAndSafety::first();
        if (!$trustSafety) {
            $trustSafety = new TrustAndSafety();
        }

        // Handle Scam Protection Points Array
        $scamProtectionPoints = [];
        if ($request->has('scam_protection_points')) {
            foreach ($request->scam_protection_points as $point) {
                if (!empty($point)) {
                    $scamProtectionPoints[] = $point;
                }
            }
        }

        $trustSafety->update([
            'title' => $request->title,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'banner_title' => $request->banner_title,
            'content_title' => $request->content_title,
            'introduction' => $request->introduction,
            'top_rated_program' => $request->top_rated_program,
            'communication_importance' => $request->communication_importance,
            'escrow_system' => $request->escrow_system,
            'customer_support' => $request->customer_support,
            'dispute_resolution' => $request->dispute_resolution,
            'freelancer_profiles' => $request->freelancer_profiles,
            'project_guidelines' => $request->project_guidelines,
            'scam_protection_title' => $request->scam_protection_title,
            'scam_protection_points' => $scamProtectionPoints,
            'contact_info' => $request->contact_info,
        ]);

        return back()->with(toastr_success('Trust and Safety page updated successfully!'));
    }
}
