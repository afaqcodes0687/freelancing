<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrustAndSafety;

class TrustAndSafetySeeder extends Seeder
{
    public function run()
    {
        TrustAndSafety::updateOrCreate([
            'id' => 1
        ], [
            'title' => 'Trust and Safety',
            'meta_title' => 'Trust and Safety - Secure Freelancing with Confidence | Right Freelancer',
            'meta_description' => 'Learn how Right Freelancer ensures a safe and trustworthy platform for both freelancers and clients. Explore our safety protocols, verification process, and dispute resolution systems.',
            'banner_title' => 'Trust & Safety',
            'content_title' => 'Trust & Safety',
            'introduction' => 'Right Freelancer has put in place the best thresholds for valuable freelancers and employers to sift through. The thresholds are in place so the perfect candidates can be selected for each job. You are still encouraged to look for user ratings and feedback before you select your employer as well as your freelancer, whoever you are seeking.',
            'top_rated_program' => 'Top Rated Program is in place to highlight the top freelancers in the field and to bring out the best options according to their history, credit score, reviews and followers.',
            'communication_importance' => 'Chatting is Essential Having real-time conversations before and after you hire or choose to work is very significant to the outcome we find. Keep communication open to enable a safe and successful workplace.',
            'escrow_system' => 'Escrow lets you double-check finished work once milestones are met – before you release pre-funded payments to your freelancer.',
            'customer_support' => 'Right Freelancer has made it easy to get help from our customer support team anytime, from anywhere. The support desk is all humans, not bots, ready to serve you as per your concerns.',
            'dispute_resolution' => 'Disputes rarely happen. But in the event they do occur, The platform assists with dispute resolution until both parties are satisfied.',
            'freelancer_profiles' => 'Freelancer profiles include detailed information such as work experience, resumes, portfolios, number of hours worked through Right Freelancer, and skills tests taken so you can better know who is being hired. The Top Rated program highlights freelancers who have achieved an impressive reputation in the online job marketplace here.',
            'project_guidelines' => 'It\'s important to have a clear idea of what you want when creating a job post or a proposal. As a freelancer, protect yourself by only accepting contracts with reasonable requirements. With escrow, clients pre-fund work and release funds to their freelancers as projects are completed or milestones are met – knowing that they\'ll be able to review and approve delivered work before releasing payment. Escrow also gives freelancers the comfort of knowing that a project is funded before they begin working.',
            'scam_protection_title' => 'Try to protect yourself from scams by taking note of the following concepts:',
            'scam_protection_points' => [
                'Phishing: Prevent unscrupulous users from trying to steal your passwords. Double check that links or HTML files clients give you don\'t lead to fake login pages.',
                'Free work fraud: Don\'t start work before the official contract start date. Never pay anything to work for a client, even if they claim that the money will be reimbursable.',
                'Check cashing fraud: Report clients who ask you to process PayPal payments, or request favors to cash or deposit checks and money orders in order to send the money somewhere else.'
            ],
            'contact_info' => 'Contact Us In case of confusion please contact us at info@rightfreelancer.com and the live chat support should get back to you with an answer right away.'
        ]);
    }
}
