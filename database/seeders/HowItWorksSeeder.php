<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HowItWorks;

class HowItWorksSeeder extends Seeder
{
    public function run()
    {
        HowItWorks::create([
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
                ],
                [
                    'question' => 'How to Choose The Skills?',
                    'answer' => 'Your freelancer profile is your most valuable resource- so your skills. With the help of your skills, RightFreelancer is able to jot down the list of jobs relevant to your field. It also helps the clients or companies to view your skills before hiring you for project.'
                ],
                [
                    'question' => 'How to Find The Jobs?',
                    'answer' => 'Browse the job that best suits your skills, expertise, price and schedule. Optimize your filters to get most of the job opportunities. Then enter the job you are looking for and send your proposal to the client right away.'
                ],
                [
                    'question' => 'How To Write Proposal Pitch?',
                    'answer' => 'If you are a pro in your professional experience, then you won\'t need an answer to this question. The best solution to this is to read the job description carefully, analyse the necessities, let the client know that you understand his/her requirements and use the proactive approach to give a brief idea of how you will get the job done successfully within short span of time. In short, write a tailored proposal.'
                ],
                [
                    'question' => 'How To Start The Project?',
                    'answer' => 'If employer is interested to work with you after viewing your proposal, he will award you the project, chat in real time with you to discuss the project in detail. After mutual collaboration with regard to working and agreed budget, you can accept the offer and start working on the project right away.'
                ],
                [
                    'question' => 'How to Get Paid For Work?',
                    'answer' => 'Once the project is completed successfully and client accepts the work, amount in Escrow of RightFreelancer will be directly transferred into your account. Build long lasting working relationships with your employer while working on his/her project. To get recognized for the work you have done, ask your client to provide feedback to open another door of opportunities from new clients.'
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
                ],
                [
                    'question' => 'When Should I Not Pay To Freelancer?',
                    'answer' => 'Although rare case in here, if the freelancers work is not up to the mark, then you may ask him/her for amendments to avoid getting into dispute. However, if you are not satisfied with a work based on the amount you are paying, you can get in touch with the RightFreelancer support team. No matter what we will keep your money safe and secure until the dispute is resolved. Our team is available 24/7 to resolve the issue in your maximum benefits.'
                ],
                [
                    'question' => 'Why Should I Give Feedback?',
                    'answer' => 'Your feedback can make a huge difference to this platform and ultimately you i.e leaving feedback after the project completion is very important to us, our employers, agencies and freelancers. It\'s the real asset of our hardworking freelancers who make your job done, take your business to the next level with their help. They work assiduously to meet your requirements and make the best use of their skills and expertise to stand you out from your competitors. Your feedback to freelancers is a kind of reward to them for their next awaiting project that will help them to pursue their freelancing and also let us filter out the best freelancers for our valued employers for their upcoming projects.'
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
                ],
                [
                    'question' => 'How to post a job?',
                    'answer' => 'A featured job has greater visibility and shows to many more candidates than the regular job post.'
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
                    'answer' => 'Yes you can opt out of the plan at any point in time, you will not be charged from next month onwards.'
                ],
                [
                    'question' => 'What happens if there is a dispute?',
                    'answer' => 'RightFreelancer has set up excellent dispute management system through which majority disputes are handled automatically. For more complicated concerns, we have set up an arbitration team that looks into complex matters and finds solutions that satisfy both parties without disastifying any one of them.'
                ],
                [
                    'question' => 'What if i need more information?',
                    'answer' => 'You can find more information regarding any concerns or queries at the 24 hour live chat support desk.'
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
