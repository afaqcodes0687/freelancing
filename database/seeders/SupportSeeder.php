<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Support;

class SupportSeeder extends Seeder
{
    public function run()
    {
        Support::updateOrCreate([
            'id' => 1
        ], [
            'title' => 'Support',
            'meta_title' => 'Support - Get Help & Resolve Issues | Right Freelancer',
            'meta_description' => 'Need help? Our support team is here to assist you with any issues or questions. Get reliable customer support and resolve your freelancing concerns on Right Freelancer.',
            'banner_title' => 'Support',
            'content_title' => 'How To Start Hiring',
            'main_content' => 'Hiring the right person for your job is never easy, but RightFreelancer platform makes it very easy for you to find perfect candidates to do your jobs. We sift through millions of profiles so you are not scammed, or disappointed when you are hiring online. And the best part is that we take the least amount of money as commission than imaginable. All of this is a token of gratitude from us to you so you know we have got your back whenever you need an extra pair of hands in the shape of online freelancers to do a job for you.',
            'faq_title' => 'Frequently Asked Questions',
            'side_image' => '../assets/frontend/img/static/Commission-System-Right-Side.jpg',
            'main_image' => '../assets/frontend/img/static/support-hero.jpg',
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
}
