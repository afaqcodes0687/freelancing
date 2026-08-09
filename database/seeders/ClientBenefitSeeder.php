<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClientBenefit;

class ClientBenefitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        ClientBenefit::truncate();
        
        // Create client benefits record with provided data
        ClientBenefit::create([
            'title' => 'Why Clients Love Right Freelancer LLC',
            'meta_title' => 'Why Clients Love Right Freelancer LLC - Client Benefits',
            'meta_description' => 'Discover why clients choose Right Freelancer LLC. 0% service charges, professional project management, secure payments, and quality assurance.',
            'heading' => 'Why Clients Love Right Freelancer LLC',
            'short_description' => 'Right Freelancer LLC is designed to be client-friendly, transparent, and cost-effective.',
            'content' => '<h3>Key Benefits for Clients:</h3>
                        <p>Discover the advantages of working with Right Freelancer LLC for your project needs.</p>',
            'benefits' => [
                [
                    'title' => '0% Service Charges',
                    'description' => 'Clients pay zero platform fees. No hidden costs.'
                ],
                [
                    'title' => 'Professional Project Management',
                    'description' => 'Every project is handled by a Business Developer.'
                ],
                [
                    'title' => 'Faster Communication',
                    'description' => 'No confusion or delays—one clear point of contact.'
                ],
                [
                    'title' => 'Quality Assurance',
                    'description' => 'Business Developers ensure high-quality results before delivery.'
                ],
                [
                    'title' => 'Secure Payments',
                    'description' => 'Payments are protected until project completion.'
                ],
                [
                    'title' => 'Focus on Business Growth',
                    'description' => 'Right Freelancer LLC allows Clients to focus on business growth, while we handle execution.'
                ]
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
}
