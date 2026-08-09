<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TermsOfService;

class TermsOfServiceController extends Controller
{
    public function edit()
    {
        $policy = TermsOfService::first();

        if (!$policy) {
            $policy = TermsOfService::create([
                'title' => 'Terms of Service',
                'heading' => 'Terms of Service',
                'short_description' => 'Read our Terms of Service to understand the rules, obligations, and legal agreements.',
                'content' => '',
                'faqs' => []
            ]);
        }

        return view('backend.terms_of_service.edit', compact('policy'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'heading' => 'nullable|string|max:255',
            'short_description' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'faq_content' => 'nullable|string',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:255',
            'faqs.*.answer' => 'nullable|string|max:1000',
        ]);

        $policy = TermsOfService::first();

        if (!$policy) {
            $policy = TermsOfService::create([
                'title' => $request->title,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'heading' => $request->heading,
                'short_description' => $request->short_description,
                'content' => $request->content,
                'faq_content' => $request->faq_content,
            ]);
        }

        $updateData = [
            'title' => $request->title,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'heading' => $request->heading,
            'short_description' => $request->short_description,
            'content' => $request->content,
            'faq_content' => $request->faq_content,
        ];

        // Handle FAQs array
        if ($request->has('faqs')) {
            $faqs = [];
            foreach ($request->faqs as $faq) {
                if (!empty($faq['question']) || !empty($faq['answer'])) {
                    $faqs[] = [
                        'question' => $faq['question'] ?? '',
                        'answer' => $faq['answer'] ?? '',
                    ];
                }
            }
            $updateData['faqs'] = !empty($faqs) ? $faqs : null;
        }

        $policy->update($updateData);

        return back()->with(toastr_success('Terms of Service page updated successfully!'));
    }
}
