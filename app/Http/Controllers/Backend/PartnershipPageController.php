<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PartnershipPage;

class PartnershipPageController extends Controller
{
    public function edit()
    {
        $policy = PartnershipPage::first();
        if (!$policy) {
            $policy = PartnershipPage::create([
                'title' => 'Partnership',
                'meta_title' => 'Partnership - Right Freelancer',
                'meta_description' => 'Explore partnership opportunities with Right Freelancer.'
            ]);
        }
        return view('backend.partnership_page.edit', compact('policy'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:191',
            'meta_title' => 'nullable|string|max:191',
            'meta_description' => 'nullable|string',

            'escrow_title' => 'nullable|string|max:191',
            'escrow_description' => 'nullable|string',
            'escrow_image' => 'nullable|string|max:191',

            'why_partner_title' => 'nullable|string|max:191',
            'why_partner_description' => 'nullable|string',
            'expand_talent_title' => 'nullable|string|max:191',
            'expand_talent_description' => 'nullable|string',
            'expand_talent_image' => 'nullable|string|max:191',

            'foster_innovation_title' => 'nullable|string|max:191',
            'foster_innovation_description' => 'nullable|string',
            'foster_innovation_image' => 'nullable|string|max:191',

            'market_presence_title' => 'nullable|string|max:191',
            'market_presence_description' => 'nullable|string',
            'market_presence_image' => 'nullable|string|max:191',

            'economic_empowerment_description' => 'nullable|string',
            'economic_empowerment_image' => 'nullable|string|max:191',

            'opportunities' => 'nullable|array',
            'process' => 'nullable|array',

            'contact_email' => 'nullable|string|max:191',
            'contact_phone' => 'nullable|string|max:191',
        ]);

        $policy = PartnershipPage::first();
        if (!$policy) {
            $policy = new PartnershipPage();
        }

        // Handle Opportunities Array
        $opportunities = [];
        if ($request->has('opportunities')) {
            foreach ($request->opportunities as $opt) {
                if (!empty($opt['title']) || !empty($opt['description'])) {
                    $opportunities[] = [
                        'title' => $opt['title'] ?? '',
                        'description' => $opt['description'] ?? '',
                        'icon' => $opt['icon'] ?? '',
                    ];
                }
            }
        }

        // Handle Process Array
        $process = [];
        if ($request->has('process')) {
            foreach ($request->process as $proc) {
                if (!empty($proc['title']) || !empty($proc['description'])) {
                    $process[] = [
                        'title' => $proc['title'] ?? '',
                        'description' => $proc['description'] ?? '',
                    ];
                }
            }
        }

        $policy->update([
            'title' => $request->title,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,

            'escrow_title' => $request->escrow_title,
            'escrow_description' => $request->escrow_description,
            'escrow_image' => $request->escrow_image,

            'why_partner_title' => $request->why_partner_title,
            'why_partner_description' => $request->why_partner_description,
            'expand_talent_title' => $request->expand_talent_title,
            'expand_talent_description' => $request->expand_talent_description,
            'expand_talent_image' => $request->expand_talent_image,

            'foster_innovation_title' => $request->foster_innovation_title,
            'foster_innovation_description' => $request->foster_innovation_description,
            'foster_innovation_image' => $request->foster_innovation_image,

            'market_presence_title' => $request->market_presence_title,
            'market_presence_description' => $request->market_presence_description,
            'market_presence_image' => $request->market_presence_image,

            'economic_empowerment_description' => $request->economic_empowerment_description,
            'economic_empowerment_image' => $request->economic_empowerment_image,

            'opportunities' => $opportunities,
            'process' => $process,

            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
        ]);

        return back()->with(toastr_success('Partnership Page Updated Successfully'));
    }
}
