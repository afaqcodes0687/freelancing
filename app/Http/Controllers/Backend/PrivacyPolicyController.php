<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivacyPolicy;

class PrivacyPolicyController extends Controller
{
    public function edit(Request $request)
    {
        $type = $request->get('type', 'website');
        $policy = PrivacyPolicy::where('type', $type)->first();

        if (!$policy) {
            $policy = PrivacyPolicy::create([
                'type' => $type,
                'title' => $type === 'app' ? 'App Privacy Policy' : 'Privacy Policy',
                'heading' => $type === 'app' ? 'App Privacy Policy' : 'Privacy Policy',
                'short_description' => 'Right Freelancer LLC values your privacy.',
                'content' => '',
                'faqs' => []
            ]);
        }

        return view('backend.privacy_policy.edit', compact('policy', 'type'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'type' => 'required|in:website,app',
            'title' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'heading' => 'nullable|string|max:255',
            'short_description' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'faq_content' => 'nullable|string',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:255',
            'faqs.*.answer' => 'nullable|string|max:1000'
        ]);

        $policy = PrivacyPolicy::where('type', $request->type)->first();

        if (!$policy) {
            $policy = PrivacyPolicy::create([
                'type' => $request->type,
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
                        'answer' => $faq['answer'] ?? ''
                    ];
                }
            }
            $updateData['faqs'] = !empty($faqs) ? $faqs : null;
        }

        $policy->update($updateData);

        return back()->with(toastr_success('Privacy Policy page updated successfully!'));
    }
}
