<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FeesAndCharge;
use Illuminate\Http\Request;

class FeesAndChargeController extends Controller
{
    public function edit()
    {
        $policy = FeesAndCharge::first();

        if (!$policy) {
            // Create default data if no record exists
            $policy = FeesAndCharge::create([
                'title' => 'Fees and Charges',
                'heading' => 'Fees and Charges',
                'short_description' => 'Understand our pricing.',
                'content' => '',
                'faqs' => [],
            ]);
        }

        return view('backend.fees_and_charge.edit', compact('policy'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'heading' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'faqs.*.question' => 'nullable|string|max:255',
            'faqs.*.answer' => 'nullable|string',
        ]);

        $policy = FeesAndCharge::first();

        $updateData = $request->only([
            'title',
            'meta_title',
            'meta_description',
            'heading',
            'short_description',
            'content'
        ]);

        // Handle FAQs array
        $faqs = [];
        if ($request->has('faqs')) {
            foreach ($request->input('faqs') as $faq) {
                if (!empty($faq['question']) && !empty($faq['answer'])) {
                    $faqs[] = $faq;
                }
            }
        }
        $updateData['faqs'] = $faqs;

        $policy->update($updateData);

        return back()->with(toastr_success('Fees and Charges page updated successfully!'));
    }
}
