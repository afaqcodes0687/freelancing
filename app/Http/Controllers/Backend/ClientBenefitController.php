<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
    use App\Models\ClientBenefit;

class ClientBenefitController extends Controller
{

    public function edit()
    {
        $benefit = ClientBenefit::first();

        if (!$benefit) {
            $benefit = ClientBenefit::create([
                'title' => 'Why Clients Love Right Freelancer LLC',
                'heading' => 'Why Clients Love Right Freelancer LLC',
                'short_description' => 'Right Freelancer LLC is designed to be client-friendly, transparent, and cost-effective.',
                'content' => '',
                'benefits' => [
                    ['title' => '0% Service Charges', 'description' => 'Clients pay zero platform fees. No hidden costs.', 'icon' => 'fa-dollar-sign'],
                    ['title' => 'Professional Project Management', 'description' => 'Every project is handled by a Business Developer.', 'icon' => 'fa-tasks'],
                    ['title' => 'Faster Communication', 'description' => 'No confusion or delays—one clear point of contact.', 'icon' => 'fa-comments'],
                    ['title' => 'Quality Assurance', 'description' => 'Business Developers ensure high-quality results before delivery.', 'icon' => 'fa-shield-alt'],
                    ['title' => 'Secure Payments', 'description' => 'Payments are protected until project completion.', 'icon' => 'fa-lock'],
                    ['title' => 'Focus on Business Growth', 'description' => 'Right Freelancer LLC allows Clients to focus on business growth, while we handle execution.', 'icon' => 'fa-chart-line']
                ],
                'faqs' => [
                    [
                        'question' => 'Why should I choose Right Freelancer LLC as a Client?',
                        'answer' => 'Right Freelancer LLC is designed to be client-friendly, transparent, and cost-effective. We offer 0% service charges, professional project management through Business Developers, faster communication, quality assurance, and secure payments. This allows you to focus on your business growth while we handle project execution.'
                    ],
                    [
                        'question' => 'Are there any hidden fees or service charges?',
                        'answer' => 'No, there are absolutely no hidden fees. Clients pay zero platform fees 0% service charges. What you agree to pay is exactly what you pay, with complete transparency.'
                    ],
                    [
                        'question' => 'How does professional project management work?',
                        'answer' => 'Every project on Right Freelancer LLC is handled by a verified Business Developer who acts as your project manager. They coordinate with freelancers, ensure quality delivery, and manage all project aspects, so you don\'t have to.'
                    ],
                    [
                        'question' => 'How does communication work with Business Developers?',
                        'answer' => 'You have one clear point of contact your Business Developer. This eliminates confusion and delays. All communication goes through a single channel, making project coordination smooth and efficient.'
                    ],
                    [
                        'question' => 'How is quality assured on Right Freelancer LLC?',
                        'answer' => 'Business Developers ensure high-quality results before delivery. They review all work, coordinate with freelancers, and make sure everything meets your requirements before the project is marked as complete.'
                    ],
                    [
                        'question' => 'How are payments secured?',
                        'answer' => 'Payments are protected until project completion. Your funds are held securely and only released when you confirm that the work meets your expectations. This ensures you get what you pay for.'
                    ],
                    [
                        'question' => 'How does Right Freelancer LLC help me focus on business growth?',
                        'answer' => 'Right Freelancer LLC handles all project execution details from finding the right talent to managing delivery. This frees up your time to focus on strategic business decisions and growth initiatives, while we ensure professional project completion.'
                    ]
                ]
            ]);
        }

        return view('backend.client_benefits.edit', compact('benefit'));
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
            'benefits' => 'nullable|array',
            'benefits.*.title' => 'nullable|string|max:255',
            'benefits.*.description' => 'nullable|string|max:500',
            'benefits.*.icon' => 'nullable|string|max:50',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'nullable|string|max:255',
            'faqs.*.answer' => 'nullable|string|max:1000'
        ]);

        $benefit = ClientBenefit::first();
        
        if (!$benefit) {
            // Create the record if it doesn't exist
            $benefit = ClientBenefit::create([
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

        // Handle benefits array
        if ($request->has('benefits')) {
            $benefits = [];
            foreach ($request->benefits as $benefit_item) {
                if (!empty($benefit_item['title']) || !empty($benefit_item['description'])) {
                    $benefits[] = [
                        'title' => $benefit_item['title'] ?? '',
                        'description' => $benefit_item['description'] ?? '',
                        'icon' => $benefit_item['icon'] ?? 'fa-check-circle'
                    ];
                }
            }
            $updateData['benefits'] = !empty($benefits) ? $benefits : null;
        }

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

        $benefit->update($updateData);

        return back()->with(toastr_success('Client Benefits page updated successfully!'));
    }

}
