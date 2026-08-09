<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AboutUs;

class AboutUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Team Members Data
        $teamMembers = [
            [
                'name' => 'Fazal Miran',
                'position' => 'President & Chief Executive Officer',
                'image' => 'photofazalmiran.png'
            ],
            [
                'name' => 'Shahid Javed',
                'position' => 'Project Manager',
                'image' => 'shahid.jpeg'
            ],
            [
                'name' => 'Afaq Tahir',
                'position' => 'Sr. Software Engineer',
                'image' => 'afaq.jpeg'
            ],
            [
                'name' => 'Muhammad Haris',
                'position' => 'Sr. Software Engineer',
                'image' => 'haris.jpeg'
            ],
            [
                'name' => 'Adnan Ahmad',
                'position' => 'WordPress Developer',
                'image' => 'adnan.jpeg'
            ]
        ];

        // Certifications Data
        $certifications = [
            [
                'title' => 'ISO Certification',
                'link' => '/iso-certificate',
                'image' => 'iso-img.avif'
            ],
            [
                'title' => 'SSL Certification',
                'link' => 'https://www.ssl.com/',
                'image' => 'images.jpg'
            ],
            [
                'title' => 'LLC Registration',
                'link' => '',
                'image' => 'llc.jpg'
            ],
            [
                'title' => 'SECP Certification',
                'link' => '/secp-certificate',
                'image' => 'secp.png'
            ]
        ];

        // Create About Us record
        AboutUs::create([
            'ceo_name' => 'Fazal Miran',
            'ceo_title' => 'President & Chief Executive Officer',
            'ceo_description' => 'Fazal Miran is president and CEO of Right Freelancer, world\'s human and IT Service Management for the Provision and Support of an Online Employment (Freelance) Platform, where Fortune 3 companies through small businesses and highly skilled independent professionals from 190 countries connect and work together.<br><br>Fazal Miran joined Right Freelancer in 2019 as a product manager and held roles running Right Freelancer\'s marketplace business, as chief product officer, and as chief marketing officer before being named CEO in January 2026.',
            'ceo_image' => 'photofazalmiran.png',
            
            'main_title' => 'Great Opportunity For Rising Freelancers',
            'main_description' => '<p style="text-align:left;"><span style="font-weight:normal;">Start Today For a Great Future</span></p><p style="text-align:left;"><span style="font-weight:normal;">Right Freelancer is one of talent outsourcing companies that allows the talent from all over the world to showcase their skills, expertise and portfolios at mere platform. <span><br></span></span></p><p style="text-align:left;"><span style="font-weight:normal;">In today\'s digital era, where there is too much competition in job hiring, there are so many other opportunities that we are unaware of. Be it Graphic Designers, Business Analysts, Content Marketers, SEO Experts, Web Designers, Web Developers, Sales Copywriters or Digital Marketers from anywhere across the globe can join Right Freelancer for free, get hired and work from the comfort of their home. We are a helping hand to those proficient freelancers who are intelligent enough to work independently, collaboratively or as an agency.</span><br></p>',
            'opportunity_text' => '<p style="text-align:left;"><span style="font-weight:normal;">From the last 5 years, we have introduced over freshers and highly experienced 57,826 freelancers to employers and different agencies irrespective of race, social status, religion and ethnicity. Our talent pool of freelancers will take your business to the next level with their orthodox flair.</span></p>',
            
            'clients_count' => '1K+',
            'freelancers_count' => '2K+',
            'orders_count' => '2K+',
            'jobs_handled' => '49K',
            'earned_amount' => '$50M',
            'awards_count' => '09X',
            
            'video_title' => 'Right Freelancer Platform Tutorial',
            'video_description' => 'Learn how to use Right Freelancer platform effectively',
            'video_url' => 'https://www.youtube.com/embed/3XUTzdMLVqg?autoplay=1',
            'video_thumbnail' => 'rightfreelancer_tutorial.png',
            
            'what_we_do_title' => 'What we do?',
            'what_we_do_description' => 'Right Freelancer is your seamless gateway to connect clients with exceptional freelancers. We curate a diverse talent pool, streamline collaboration with efficient tools.',
            
            'certifications_title' => 'Certifications',
            'certifications_description' => 'We\'re extremely delighted to be recognized for the great work we do in and out of the office.',
            'certifications' => json_encode($certifications),
            
            'team_title' => 'Meet our hardworking team',
            'team_members' => json_encode($teamMembers),
            
            'meta_title' => 'About Us - Right Freelancer | Global Freelancing Platform',
            'meta_description' => 'Right Freelancer is a global freelancing platform connecting skilled professionals with businesses worldwide. Discover how we help freelancers grow and succeed.'
        ]);
    }
}
