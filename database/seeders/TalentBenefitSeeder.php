<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TalentBenefit;

class TalentBenefitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        TalentBenefit::truncate();
        
        // Create talent benefits record with provided data
        TalentBenefit::create([
            'title' => 'Why Talents Choose Right Freelancer LLC',
            'meta_title' => 'Why Talents Choose Right Freelancer LLC - Freelancer Benefits',
            'meta_description' => 'Discover why freelancers choose Right Freelancer LLC. 8% service fee, fast 3-day withdrawals, no client hassle, skill-based projects, and international support.',
            'heading' => 'Why Talents Choose Right Freelancer LLC',
            'short_description' => 'Right Freelancer LLC is built to support Freelancers with fair pricing, fast payments, and consistent work opportunities.',
            'content' => '<h3>Freelancer Advantages:</h3>
                        <p>Discover the advantages of working with Right Freelancer LLC for your freelance career.</p>',
            'benefits' => [
                [
                    'title' => 'Only 8% Service Fee',
                    'description' => 'Freelancers are charged a low and transparent platform fee.'
                ],
                [
                    'title' => 'Fast Withdrawals (3 Days)',
                    'description' => 'Unlike other platforms that take 5–10 days, Right Freelancer LLC releases payments in just 3 days.'
                ],
                [
                    'title' => 'No Client Hassle',
                    'description' => 'Business Developers handle clients and negotiations.'
                ],
                [
                    'title' => 'Skill-Based Projects',
                    'description' => 'Freelancers receive projects relevant to their expertise.'
                ],
                [
                    'title' => 'International-Focused Platform',
                    'description' => 'Built to support local freelancers with reliable payouts.'
                ],
                [
                    'title' => 'Focus on Great Work',
                    'description' => 'Clients (Freelancers) can focus purely on doing great work, not managing clients.'
                ]
            ],
            'faqs' => [
                [
                    'question' => 'Why should I choose Right Freelancer LLC as a Freelancer?',
                    'answer' => 'Right Freelancer LLC is built to support Freelancers with fair pricing, fast payments, and consistent work opportunities. We offer only 8% service fee, fast 3-day withdrawals, no client management hassle, skill-based project matching, and international-focused support. This enables you to concentrate entirely on delivering high-quality work while we take care of managing client relationships.'
                ],
                [
                    'question' => 'What is the service fee for Freelancers?',
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
}
