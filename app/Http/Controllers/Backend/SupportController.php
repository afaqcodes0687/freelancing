<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Support;

class SupportController extends Controller
{
    public function edit()
    {
        $support = Support::first();
        if (!$support) {
            $support = Support::create([
                'title' => 'Support',
                'meta_title' => 'Support - Get Help & Resolve Issues | Right Freelancer',
                'meta_description' => 'Need help? Our support team is here to assist you with any issues or questions. Get reliable customer support and resolve your freelancing concerns on Right Freelancer.',
                'banner_title' => 'Support',
                'content_title' => 'How To Start Hiring',
                'main_content' => 'Hiring the right person for your job is never easy, but RightFreelancer platform makes it very easy for you to find perfect candidates to do your jobs. We sift through millions of profiles so you are not scammed, or disappointed when you are hiring online. And best part is that we take least amount of money as commission than imaginable. All of this is a token of gratitude from us to you so you know we have got your back whenever you need an extra pair of hands in shape of online freelancers to do a job for you.',
                'faq_title' => 'Frequently Asked Questions',
                'faqs' => [
                    [
                        'question' => 'How can i start hiring?',
                        'answer' => 'All you need is an account on our site as an employer. Fill out all the details in the best possible way and let us verify your account. You can start posting jobs once it is approved.'
                    ],
                    [
                        'question' => 'How to post a job?',
                        'answer' => 'A featured job has greater visibility and shows to many more candidates than a regular job post.'
                    ],
                    [
                        'question' => 'How many jobs can an employer feature?',
                        'answer' => 'The number of jobs depends on the plan selected. The number varies from 5 - Silver Plan, 10 - Gold Plan, and 25 in the Platinum Plan.'
                    ],
                    [
                        'question' => 'Can i get a free trial?',
                        'answer' => 'Yes, the first month is a free trial for all employers and freelancers.'
                    ],
                    [
                        'question' => 'Can i cancel my membership?',
                        'answer' => 'Yes, you can opt out of the plan at any point in time, you will not be charged from the next month onwards.'
                    ],
                    [
                        'question' => 'What happens if there is a dispute?',
                        'answer' => 'RightFreelancer has set up an excellent dispute management system through which the majority of disputes are handled automatically. For more complicated concerns, we have set up an arbitration team that looks into complex matters and finds solutions that satisfy both parties without devastating any one of them.'
                    ],
                    [
                        'question' => 'What if i need more information?',
                        'answer' => 'You can find more information regarding any concerns or queries at the 24-hour live chat support desk.'
                    ]
                ]
            ]);
        }
        return view('backend.support.edit', compact('support'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:191',
            'meta_title' => 'nullable|string|max:191',
            'meta_description' => 'nullable|string',
            'banner_title' => 'nullable|string|max:191',
            'content_title' => 'nullable|string|max:191',
            'main_content' => 'nullable|string',
            'faq_title' => 'nullable|string|max:191',
            'faqs' => 'nullable|array',
            'side_image' => 'nullable|string|max:191',
            'main_image' => 'nullable|string|max:191',
        ]);

        $support = Support::first();
        if (!$support) {
            $support = new Support();
        }

        // Handle FAQs Array
        $faqs = [];
        if ($request->has('faqs')) {
            foreach ($request->faqs as $faq) {
                if (!empty($faq['question']) || !empty($faq['answer'])) {
                    $faqs[] = [
                        'question' => $faq['question'] ?? '',
                        'answer' => $faq['answer'] ?? '',
                    ];
                }
            }
        }

        $support->update([
            'title' => $request->title,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'banner_title' => $request->banner_title,
            'content_title' => $request->content_title,
            'main_content' => $request->main_content,
            'faq_title' => $request->faq_title,
            'faqs' => $faqs,
            'side_image' => $request->side_image,
            'main_image' => $request->main_image,
        ]);

        return back()->with(toastr_success('Support page updated successfully!'));
    }
}
