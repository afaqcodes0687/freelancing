<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AffiliateProgramme;
use App\Models\AffiliateFaq;

class AffiliateProgrammeController extends Controller
{
    public function edit()
    {
        $affiliateProgram = \App\Models\AffiliateProgramme::first();
        
        if (!$affiliateProgram) {
            $affiliateProgram = \App\Models\AffiliateProgramme::create([
                'title' => 'Affiliate Programme',
                'meta_title' => 'Join Our Affiliate Programme | Right Freelancer',
                'meta_description' => 'Earn money by referring clients and freelancers to Right Freelancer. Join our Affiliate Programme today and start generating passive income.',
                'meta_keywords' => 'affiliate program, earn money, referrals, commission, freelancer, client',
                'banner_title' => 'Affiliates',
                
                'hero_title' => 'Recommend Right Freelancer. Earn Commissions.',
                'hero_subtitle' => 'As an Right Freelancer affiliate you can bring value to your community by sharing in our mission to create economic opportunities so people have better lives.',
                'hero_button_text' => 'Start Earning',
                'hero_image' => 'assets/uploads/partnerimage/partnerwithus.png',
                
                'trusted_by_title' => 'Trusted by',
                'easy_start_title' => "It's easy to get started",
                
                'step1_title' => 'Sign up',
                'step1_subtitle' => "It's fast and easy to get started.",
                'step1_image' => 'assets/uploads/affiliate_img/hand.png',
                
                'step2_title' => 'Promote',
                'step2_subtitle' => 'Share Right Freelancer with your audience.',
                'step2_image' => 'assets/uploads/affiliate_img/hand1.png',
                
                'step3_title' => 'Earn',
                'step3_subtitle' => 'Start earning when the client funds a project.',
                'step3_image' => 'assets/uploads/affiliate_img/dollar.png',
                
                'benefits_title' => 'Right Freelancer Affiliate Benefits',
                'commission_title' => 'Commissions',
                'commission_content' => 'Get <span style="font-weight: 600;">70%</span> of the first contract spend up to <span style="font-weight: 600;">$150</span> for every new client referred to Right Freelancer & <span style="font-weight: 600;">5%</span> commission for repeat contracts for spend up to <span style="font-weight: 600;">$150</span>.',
                'commission_image' => 'assets/uploads/affiliatesimage/60b11850cdadf22e07dcbbd7_How_it_Works_1_Post-A-Job_Martin_Nicholausson (3).png',
                
                'support_title' => 'Support',
                'support_content' => 'With Right Freelancer dedicated affiliate team you will have your questions answered and receive the help you need.',
                'support_image' => 'assets/uploads/affiliatesimage/60c753a83ccd7e60ed907950_image.png',
                
                'resources_title' => 'Resources',
                'resources_content' => 'As an affiliate, you\'ll have access to regularly refreshed logos, ads, and banners to help optimize conversions.',
                'resources_image' => 'assets/uploads/affiliatesimage/60c753b8dc5df76686dfb0ba_image.png',
                
                'why_title' => 'Why Right Freelancer',
                'why_subtitle' => 'The world\'s work marketplace',
                'why_content' => 'Businesses and independent professionals from around the world come to Right Freelancer to grow their businesses, take control of their careers, and create meaningful work relationships.',
                'why_image' => 'assets/uploads/affiliate_img/business.png',
                
                'promote_title' => 'Two ways to promote',
                'promote_content' => 'Everyone comes to Right Freelancer with a vision in mind. Project Catalog™ and Talent Marketplace™ give your audience two ways to fulfill their visions.',
                'promote_image' => 'assets/uploads/affiliate_img/blog.jpg',
                'promote_avatar' => 'assets/uploads/affiliatesimage/afaq.jpg',
                'promote_name' => 'Afaq',
                'promote_profession' => 'Laravel Developer',
                'promote_subtitle' => 'From $250',
                'promote_reviews' => '(124 jobs)',
                
                'jobs_title' => 'More than 60k jobs posted every week',
                'jobs_content' => 'With thousands of opportunities to connect, Right Freelancer unlocks ways for business and independent professionals to work together that weren\'t possible before.',
                'jobs_image' => 'assets/uploads/affiliate_img/developers.png',
                
                'faq_title' => 'Frequently asked questions',
                
                'cta_title' => 'Join the world\'s work marketplace as an Right Freelancer Affiliate today.',
                'cta_button_text' => 'Start Earning',
                'cta_image' => 'assets/uploads/affiliate_img/dollar.png',
                
                'stats1_number' => '49K',
                'stats1_text' => 'Jobs we have handled in our Right Freelancer platform',
                'stats2_number' => '$50M',
                'stats2_text' => 'Earned by Freelancers in our platform till date',
                'stats3_number' => '09X',
                'stats3_text' => 'Awards received in IT for excellence in service',
            ]);
        }
        
        return view('backend.pages.affiliate-programme.edit', compact('affiliateProgram'));
    }

    public function frontend()
    {
        $affiliateProgram = \App\Models\AffiliateProgramme::first();
        
        if (!$affiliateProgram) {
            $affiliateProgram = new \App\Models\AffiliateProgramme();
        }
        
        return view('frontend.pages.affiliate-programme', compact('affiliateProgram'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'banner_title' => 'nullable|string|max:255',
            
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_button_text' => 'nullable|string|max:255',
            'hero_image' => 'nullable|string|max:255',
            
            'trusted_by_title' => 'nullable|string|max:255',
            'easy_start_title' => 'nullable|string|max:255',
            
            'step1_title' => 'nullable|string|max:255',
            'step1_subtitle' => 'nullable|string|max:255',
            'step1_image' => 'nullable|string|max:255',
            
            'step2_title' => 'nullable|string|max:255',
            'step2_subtitle' => 'nullable|string|max:255',
            'step2_image' => 'nullable|string|max:255',
            
            'step3_title' => 'nullable|string|max:255',
            'step3_subtitle' => 'nullable|string|max:255',
            'step3_image' => 'nullable|string|max:255',
            
            'benefits_title' => 'nullable|string|max:255',
            'commission_title' => 'nullable|string|max:255',
            'commission_content' => 'nullable|string',
            'commission_image' => 'nullable|string|max:255',
            
            'support_title' => 'nullable|string|max:255',
            'support_content' => 'nullable|string',
            'support_image' => 'nullable|string|max:255',
            
            'resources_title' => 'nullable|string|max:255',
            'resources_content' => 'nullable|string',
            'resources_image' => 'nullable|string|max:255',
            
            'why_title' => 'nullable|string|max:255',
            'why_subtitle' => 'nullable|string|max:255',
            'why_content' => 'nullable|string',
            'why_image' => 'nullable|string|max:255',
            
            'promote_title' => 'nullable|string|max:255',
            'promote_content' => 'nullable|string',
            'promote_image' => 'nullable|string|max:255',
            'promote_avatar' => 'nullable|string|max:255',
            'promote_name' => 'nullable|string|max:255',
            'promote_profession' => 'nullable|string|max:255',
            'promote_subtitle' => 'nullable|string|max:255',
            'promote_reviews' => 'nullable|string|max:255',
            
            'jobs_title' => 'nullable|string|max:255',
            'jobs_content' => 'nullable|string',
            'jobs_image' => 'nullable|string|max:255',
            
            'faq_title' => 'nullable|string|max:255',
            
            'cta_title' => 'nullable|string|max:255',
            'cta_button_text' => 'nullable|string|max:255',
            'cta_image' => 'nullable|string|max:255',
            
            'stats1_number' => 'nullable|string|max:255',
            'stats1_text' => 'nullable|string|max:255',
            'stats2_number' => 'nullable|string|max:255',
            'stats2_text' => 'nullable|string|max:255',
            'stats3_number' => 'nullable|string|max:255',
            'stats3_text' => 'nullable|string|max:255',
        ]);

        $affiliateProgram = \App\Models\AffiliateProgramme::first();
        if (!$affiliateProgram) {
            $affiliateProgram = new \App\Models\AffiliateProgramme();
        }

        $affiliateProgram->fill($request->all());
        $affiliateProgram->save();

        return back()->with(toastr_success('Affiliate Programme page updated successfully!'));
    }

}
