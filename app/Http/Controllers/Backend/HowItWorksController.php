<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HowItWorks;

class HowItWorksController extends Controller
{
    public function edit()
    {
        $howItWorks = HowItWorks::first();
        if (!$howItWorks) {
            $howItWorks = $this->createDefaultContent();
        }
        return view('backend.how-it-works.edit', compact('howItWorks'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:191',
            'meta_title' => 'nullable|string|max:191',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:191',
            'banner_title' => 'nullable|string|max:191',
            
            // Hiring Tab
            'hiring_content_title' => 'nullable|string|max:191',
            'hiring_content_subtitle' => 'nullable|string',
            'hiring_main_content' => 'nullable|string',
            'hiring_faqs' => 'nullable|array',
            'hiring_side_image' => 'nullable|string|max:191',
            
            'hiring_progress_title' => 'nullable|string|max:191',
            'hiring_progress_subtitle' => 'nullable|string',
            'hiring_progress_content' => 'nullable|string',
            'hiring_progress_faqs' => 'nullable|array',
            'hiring_progress_image' => 'nullable|string|max:191',
            
            'hiring_payment_title' => 'nullable|string|max:191',
            'hiring_payment_subtitle' => 'nullable|string',
            'hiring_payment_content' => 'nullable|string',
            'hiring_payment_faqs' => 'nullable|array',
            'hiring_payment_image' => 'nullable|string|max:191',
            
            // Talents Tab
            'talents_content_title' => 'nullable|string|max:191',
            'talents_content_subtitle' => 'nullable|string',
            'talents_main_content' => 'nullable|string',
            'talents_faqs' => 'nullable|array',
            'talents_side_image' => 'nullable|string|max:191',
            
            'talents_payment_title' => 'nullable|string|max:191',
            'talents_payment_subtitle' => 'nullable|string',
            'talents_payment_content' => 'nullable|string',
            'talents_payment_faqs' => 'nullable|array',
            'talents_payment_image' => 'nullable|string|max:191',
            
            // FAQ Tab
            'faq_content_title' => 'nullable|string|max:191',
            'faq_content_subtitle' => 'nullable|string',
            'faq_main_content' => 'nullable|string',
            'faq_faqs' => 'nullable|array',
            'faq_side_image' => 'nullable|string|max:191',
            
            // Projects Tab
            'projects_content_title' => 'nullable|string|max:191',
            'projects_content_subtitle' => 'nullable|string',
            'projects_main_content' => 'nullable|string',
            'projects_faqs' => 'nullable|array',
            'projects_side_image' => 'nullable|string|max:191',
        ]);

        $howItWorks = HowItWorks::first();
        if (!$howItWorks) {
            $howItWorks = new HowItWorks();
        }

        // Handle all FAQ arrays
        $faqFields = [
            'hiring_faqs', 'hiring_progress_faqs', 'hiring_payment_faqs',
            'talents_faqs', 'talents_payment_faqs', 'faq_faqs', 'projects_faqs'
        ];

        $updateData = [];
        foreach ($faqFields as $field) {
            $faqs = [];
            if ($request->has($field)) {
                foreach ($request->$field as $faq) {
                    if (!empty($faq['question']) || !empty($faq['answer'])) {
                        $faqs[] = [
                            'question' => $faq['question'] ?? '',
                            'answer' => $faq['answer'] ?? '',
                        ];
                    }
                }
            }
            $updateData[$field] = $faqs;
        }

        // Update all other fields
        $fields = [
            'title', 'meta_title', 'meta_description', 'meta_keywords', 'banner_title',
            'hiring_content_title', 'hiring_content_subtitle', 'hiring_main_content', 'hiring_side_image',
            'hiring_progress_title', 'hiring_progress_subtitle', 'hiring_progress_content', 'hiring_progress_image',
            'hiring_payment_title', 'hiring_payment_subtitle', 'hiring_payment_content', 'hiring_payment_image',
            'talents_content_title', 'talents_content_subtitle', 'talents_main_content', 'talents_side_image',
            'talents_payment_title', 'talents_payment_subtitle', 'talents_payment_content', 'talents_payment_image',
            'faq_content_title', 'faq_content_subtitle', 'faq_main_content', 'faq_side_image',
            'projects_content_title', 'projects_content_subtitle', 'projects_main_content', 'projects_side_image'
        ];

        foreach ($fields as $field) {
            $updateData[$field] = $request->$field;
        }

        $howItWorks->update($updateData);

        return back()->with(toastr_success('How It Works page updated successfully!'));
    }

    private function createDefaultContent()
    {
        return HowItWorks::create([
            'title' => 'How It Works',
            'meta_title' => 'How it Works - Right Freelancer | Step-by-Step Guide for Freelancers & Clients',
            'meta_description' => 'Learn how Right Freelancer connects top freelancers with clients globally. Discover our simple, transparent process for posting jobs, hiring talent, and getting work done efficiently.',
            'meta_keywords' => 'how it works, how to hire freelancers, how to find freelance jobs, freelance job process, client freelancer guide, post a job, hire a freelancer, Right Freelancer process',
            'banner_title' => 'How It Works',
            
            'hiring_content_title' => 'What Kind of Work Should I Expect?',
            'hiring_content_subtitle' => 'Process Made Simpler and Easier',
            'hiring_main_content' => 'We have an absolute range of freelancers from all across the US, Germany, Australia, the Netherlands, Canada, Russia, China, UK, Pakistan and many more other countries based on different business profiles, categories ( Web, Tech, Software, IT, Networking, Sales, Marketing, Copy Writing, Creative Designing, Translation, etc.) and skills you are looking for. Our RightFreelancers will outdo the job to meet up your project expectations.',
            'hiring_faqs' => [
                [
                    'question' => 'How to Start with Registration?',
                    'answer' => 'What you need to do is share your project details with us that needs to be done. Our freelancers will send you proposals, and you can connect with the best fit for your project. Create an account on the RightFreelancer.com. Register yourself as a valid employer. Once you register yourself on RightFreelancer, you will get account verification email. Complete the verification process and start posting the job right away.'
                ],
                [
                    'question' => 'How to Post a Job?',
                    'answer' => 'It\'s free to post a job on RightFreelancer. Add the job description with the level of expertise you want for your project. Enter the skills and select any Agency, Independent Freelancers, New Rising Talent with specific job categories. Share your attachments (if needed ) of your projects as a sample for your work requirement. Keep in mind, the more the details you share, the more will be the chances to attract best RightFreelancers.'
                ]
            ],
            'hiring_side_image' => '../assets/frontend/img/static/Commission-System-Right-Side.jpg',
            
            'hiring_progress_title' => 'Track Down The Progress of Your Project?',
            'hiring_progress_subtitle' => 'Concentrate On Your Work',
            'hiring_progress_content' => 'Each and every project you post has a separate shared online workspace, where your team can connect with RightFreelancer, share documents, files, chat with multiple team members. This way you will remain in touch with your troupe and always know how far the project has been completed and what is remaining to be done.',
            'hiring_progress_faqs' => [
                [
                    'question' => 'How to Stay Informed?',
                    'answer' => 'You can stay in touch with each other whenever there is any query, or update regarding the project. Being said that, you can have the real time discussion with the freelancers via real time chat, text, or messages on RightFreelancer platform.'
                ]
            ],
            'hiring_progress_image' => '../assets/frontend/img/static/Wallet-For-Freelancers.jpg',
            
            'hiring_payment_title' => 'One Tap Payment',
            'hiring_payment_subtitle' => 'Right Time To Put Hands in Pocket',
            'hiring_payment_content' => 'RightFreelancer value your work safety and security. Your money is saved in Escrow to the RightFreelancer until you are happy with the work and ready to release the payment. Whether its a fixed price project or an hourly based assignment, you can pay at your earliest convenience by just clicking at the Pay button on your project stream.',
            'hiring_payment_faqs' => [
                [
                    'question' => 'When Should I Pay The Freelancer?',
                    'answer' => 'If you are completely satisfied with the work and authorize it, then release the payment with ease of mind.'
                ]
            ],
            'hiring_payment_image' => '../assets/frontend/img/static/Wallet-For-Freelancers.jpg',
            
            'talents_content_title' => 'Best Elite For The Startup Businesses',
            'talents_content_subtitle' => 'Your Great Future Is Waiting For You',
            'talents_main_content' => 'If you are among millions of remote startups, agencies, software companies, or any e-commerce business beginners who are hunting for a variety of jobs to earn money to live the life they have ever imagined, then RightFreelancer is beginning to that road to success.',
            'talents_faqs' => [
                [
                    'question' => 'How Should I Start?',
                    'answer' => 'You can join RightFreelancer for free. Simply, create your freelancer account, verify it, upload a professional photo and build a strong portfolio based on your skills, experience, and education to storefront your capabilities to employers.'
                ]
            ],
            'talents_side_image' => '../assets/frontend/img/static/Wallet-For-Freelancers.jpg',
            
            'talents_payment_title' => 'One Tap Payment',
            'talents_payment_subtitle' => 'Right Time To Put Hands in Pocket',
            'talents_payment_content' => 'RightFreelancer value your work safety and security. Your money is saved in Escrow to the RightFreelancer until you are happy with the work and ready to release the payment. Whether its a fixed price project or an hourly based assignment, you can pay at your earliest convenience by just clicking at the Pay button on your project stream.',
            'talents_payment_faqs' => [
                [
                    'question' => 'When Should I Pay The Freelancer?',
                    'answer' => 'If you are completely satisfied with the work and authorize it, then release the payment with ease of mind.'
                ]
            ],
            'talents_payment_image' => '../assets/frontend/img/static/Post-Projects-gigs.jpg',
            
            'faq_content_title' => 'How To Start Hiring',
            'faq_content_subtitle' => 'Start Today For a Great Future',
            'faq_main_content' => 'Hiring the right person for your job is never easy, but the RightFreelancer platform makes it very easy for you to find the perfect candidates to do your jobs. We sift through millions of profiles so you are not scammed, or disappointed when you are hiring online. And the best part is that we take the least amount of money as commission than imaginable. All of this is a token of gratitude from us to you so you know we have got your back whenever you need an extra pair of hands in the shape of online freelancers to do a job for you.',
            'faq_faqs' => [
                [
                    'question' => 'How can i start hiring?',
                    'answer' => 'All you need is an account on our site as an employer. Fill out all the details in the best possible way and let us verify your account. You can start posting jobs once it is approved.'
                ]
            ],
            'faq_side_image' => '../assets/frontend/img/static/Commission-System-Right-Side.jpg',
            
            'projects_content_title' => 'How To Selling Your Projects Gigs',
            'projects_content_subtitle' => 'Focus on Your Work',
            'projects_main_content' => 'As a freelancer, you have another option to earn money via right freelancer.com. you can use our Projects option to sell your Gigs.',
            'projects_faqs' => [
                [
                    'question' => 'What is Projects (Gigs)?',
                    'answer' => 'Here at this point, you don\'t need to submit your proposals for specific projects to start work at Right Freelancer. Projects (Gigs) mean that you can sell your Projects to any employer directly.'
                ]
            ],
            'projects_side_image' => '../assets/frontend/img/static/Post-Projects-gigs.jpg',
        ]);
    }
}
