<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TalentBenefit;

class TalentBenefitController extends Controller
{

    public function edit()
    {
        $benefit = TalentBenefit::first();

        if (!$benefit) {
            $benefit = TalentBenefit::create([
                'title' => 'Why Talents Choose Right Freelancer LLC',
                'heading' => 'Why Talents Choose Right Freelancer LLC',
                'short_description' => 'Right Freelancer LLC is built to support Freelancers with fair pricing, fast payments, and consistent work opportunities.',
                'content' => '',
                'benefits' => [
                    ['title' => 'Only 8% Service Fee', 'description' => 'Freelancers are charged a low and transparent platform fee.', 'icon' => 'fa-percentage'],
                    ['title' => 'Fast Withdrawals (3 Days)', 'description' => 'Unlike other platforms that take 5–10 days, Right Freelancer LLC releases payments in just 3 days.', 'icon' => 'fa-clock'],
                    ['title' => 'No Client Hassle', 'description' => 'Business Developers handle clients and negotiations.', 'icon' => 'fa-user-shield'],
                    ['title' => 'Skill-Based Projects', 'description' => 'Freelancers receive projects relevant to their expertise.', 'icon' => 'fa-tools'],
                    ['title' => 'International-Focused Platform', 'description' => 'Built to support local freelancers with reliable payouts.', 'icon' => 'fa-globe'],
                    ['title' => 'Focus on Great Work', 'description' => 'Clients (Freelancers) can focus purely on doing great work, not managing clients.', 'icon' => 'fa-briefcase']
                ],
                'faqs' => [
                    [
                        'question' => 'Why should I choose Right Freelancer LLC as a Freelancer?',
                        'answer' => 'Right Freelancer LLC is built to support Freelancers with fair pricing, fast payments, and consistent work opportunities. We offer only 8% service fee, fast 3-day withdrawals, no client management hassle, skill-based project matching, and international-focused support. This enables you to concentrate entirely on delivering high-quality work while we take care of managing client relationships.'
                    ],
                    [
                        'question' => 'What is service fee for Freelancers?',
                        'answer' => 'Freelancers are charged only 8% service fee, which is low and transparent. There are no hidden charges or additional fees. This is significantly lower than many other platforms.'
                    ],
                    [
                        'question' => 'How fast are payment withdrawals?',
                        'answer' => 'Right Freelancer LLC releases payments in just 3 days, unlike other platforms that take 5–10 days. This means you get your earnings faster and can manage your finances better.'
                    ],
                    [
                        'question' => 'Do I need to manage clients directly?',
                        'answer' => 'No, you are not required to manage clients. All client communication, negotiations, and project coordination are handled by the Business Development team.'
                    ],
                    [
                        'question' => 'How are projects matched to my skills?',
                        'answer' => 'Freelancers receive projects relevant to their expertise. Our system matches your skills and experience with appropriate projects, ensuring you work on tasks that align with your capabilities.'
                    ],
                    [
                        'question' => 'Is Right Freelancer LLC focused on international freelancers?',
                        'answer' => 'Yes, Right Freelancer LLC is built to support local freelancers in international with reliable payouts, local payment methods, and understanding of local market needs. We prioritize supporting the worldwide freelancing community.'
                    ],
                    [
                        'question' => 'How does Right Freelancer LLC help me focus on my work?',
                        'answer' => 'Right Freelancer LLC handles all client management, negotiations, and project coordination through Business Developers. This means you can focus purely on doing great work and delivering quality results, without worrying about client communication or project management overhead.'
                    ]
                ]
            ]);
        }

        return view('backend.talent_benefits.edit', compact('benefit'));
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

        $benefit = TalentBenefit::first();
        
        if (!$benefit) {
            // Create record if it doesn't exist
            $benefit = TalentBenefit::create([
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

        return back()->with(toastr_success('Talent Benefits page updated successfully!'));
    }

}
