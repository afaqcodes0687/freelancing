<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WinWorkWithRewardsController extends Controller
{
    public function edit()
    {
        $winWorkWithRewards = \App\Models\WinWorkWithRewards::first();
        
        if (!$winWorkWithRewards) {
            $winWorkWithRewards = \App\Models\WinWorkWithRewards::create([
                'title' => 'Win Work With Reward',
                'meta_title' => 'Win Work With Reward - Earn Bonus Opportunities on Right Freelancer',
                'meta_description' => 'Participate in rewarding activities and challenges on Right Freelancer to win extra work opportunities, bonuses, and exclusive recognition.',
                'meta_keywords' => 'win work, rewards, bonuses, freelancer opportunities, challenges, recognition',
                'banner_title' => 'Win Works and Rewards',
                
                'main_title' => 'Maximize Your Exposure with Ads',
                'main_subtitle' => 'Increase your profile visibility and secure more job opportunities with our powerful ad solutions. Whether you\'re looking for more invites or closing more deals, we have the right tools to help you succeed. Get started today!',
                'clients_count' => '39K',
                'clients_text' => 'Clients working with us',
                'freelancers_count' => '60K',
                'freelancers_text' => 'Freelancers working with us',
                'orders_count' => '50K',
                'orders_text' => 'Orders processed',
                'main_image' => 'assets/frontend/img/boosted.png',
                
                'solutions_title' => 'Discover Our Ad Solutions',
                'solutions_subtitle' => 'Explore three effective ways to reach your career goals with our ad products.',
                
                'boosted_profile_title' => 'Spotlight Your Profile',
                'boosted_profile_subtitle' => 'Boosted Pro Profile',
                'boosted_profile_content' => 'Investing in Connects can enhance your likelihood of being hired by up to 2x',
                'boosted_profile_image' => 'assets/frontend/img/boosted-1.jpg',
                
                'availability_badge_title' => 'Show Your Availability',
                'availability_badge_subtitle' => 'Availability Badge',
                'availability_badge_content' => 'Freelancers with this badge receive up to 75% more job invitations.',
                'availability_badge_image' => 'assets/frontend/img/boosted-2.jpg',
                
                'enhanced_proposals_title' => 'Reach New Heights',
                'enhanced_proposals_subtitle' => 'Enhanced Proposals',
                'enhanced_proposals_content' => 'Generates 10x greater returns on ad spend',
                'enhanced_proposals_image' => 'assets/frontend/img/boosted-3.jpg',
                
                'payment_title' => 'What\'s the payment process for ads?',
                'payment_subtitle' => 'Ad Payment with Connects',
                'payment_content' => 'On RightFreelancer, ads are purchased using Connects, a virtual currency. You can also select a custom amount to match your specific needs and budget. Each Connect is priced at $0.15 (USD). Freelancers use Connects to submit proposals and bid on ad products such as Boosted Proposals, Boosted Profiles, and the Availability Badge.',
                
                'why_use_title' => 'Why Use Ads?',
                'why_use_content' => 'Using ads is completely optional—you choose when and if you want to use them. While ads aren\'t necessary to submit proposals, they help increase your visibility, land the projects you care about most, and streamline your workflow to boost your earnings on high-quality jobs.',
                
                'getting_started_title' => 'How to Get Started',
                'getting_started_content' => 'Getting started is simple and low-risk. Select an ad product that aligns with your goals, then place a bid to enter the auction. If you win the auction, you\'ll gain increased visibility, engage more clients, and have a better chance of securing the projects you\'re most interested in.',
                
                'place_bid_title' => 'Where Do I Place a Bid for an Ad?',
                'place_bid_content' => 'After choosing the ad product that fits your needs, placing a bid is quick and easy. Follow a few simple steps to submit your bid and find out if you\'ve won the auction. These articles offer structured walkthroughs for setting up and managing essential features.',
                
                'advertising_options_title' => 'Advertising Options',
                'advertising_options' => [
                    'Enhanced Proposals',
                    'Availability Indicator', 
                    'Profile Promotion'
                ],
                
                'helpful_resources_title' => 'Helpful Resources',
                'helpful_resources_content' => 'Find out how advertising can increase your visibility and help you secure the best opportunities.',
                
                'ads_guide_title' => 'Ads Guide',
                'ads_guide_content' => 'Build, manage, and optimize your campaigns.',
                
                'master_ads_title' => 'Master your ads',
                'master_ads_content' => 'Build, manage, and optimize.',
                
                'cta_title' => 'Use ads to win work.',
                'cta_button_text' => 'Add now'
            ]);
        }
        
        return view('backend.pages.win-work-with-rewards.edit', compact('winWorkWithRewards'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:191',
            'meta_title' => 'nullable|string|max:191',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'banner_title' => 'nullable|string|max:191',
            
            'main_title' => 'nullable|string|max:191',
            'main_subtitle' => 'nullable|string',
            'clients_count' => 'nullable|string|max:50',
            'clients_text' => 'nullable|string|max:191',
            'freelancers_count' => 'nullable|string|max:50',
            'freelancers_text' => 'nullable|string|max:191',
            'orders_count' => 'nullable|string|max:50',
            'orders_text' => 'nullable|string|max:191',
            'main_image' => 'nullable|string|max:191',
            
            'solutions_title' => 'nullable|string|max:191',
            'solutions_subtitle' => 'nullable|string',
            
            'boosted_profile_title' => 'nullable|string|max:191',
            'boosted_profile_subtitle' => 'nullable|string|max:191',
            'boosted_profile_content' => 'nullable|string',
            'boosted_profile_image' => 'nullable|string|max:191',
            
            'availability_badge_title' => 'nullable|string|max:191',
            'availability_badge_subtitle' => 'nullable|string|max:191',
            'availability_badge_content' => 'nullable|string',
            'availability_badge_image' => 'nullable|string|max:191',
            
            'enhanced_proposals_title' => 'nullable|string|max:191',
            'enhanced_proposals_subtitle' => 'nullable|string|max:191',
            'enhanced_proposals_content' => 'nullable|string',
            'enhanced_proposals_image' => 'nullable|string|max:191',
            
            'payment_title' => 'nullable|string|max:191',
            'payment_subtitle' => 'nullable|string|max:191',
            'payment_content' => 'nullable|string',
            
            'why_use_title' => 'nullable|string|max:191',
            'why_use_content' => 'nullable|string',
            
            'getting_started_title' => 'nullable|string|max:191',
            'getting_started_content' => 'nullable|string',
            
            'place_bid_title' => 'nullable|string|max:191',
            'place_bid_content' => 'nullable|string',
            
            'advertising_options_title' => 'nullable|string|max:191',
            'advertising_options' => 'nullable|array',
            
            'helpful_resources_title' => 'nullable|string|max:191',
            'helpful_resources_content' => 'nullable|string',
            
            'ads_guide_title' => 'nullable|string|max:191',
            'ads_guide_content' => 'nullable|string',
            
            'master_ads_title' => 'nullable|string|max:191',
            'master_ads_content' => 'nullable|string',
            
            'cta_title' => 'nullable|string|max:191',
            'cta_button_text' => 'nullable|string|max:191',
        ]);

        $winWorkWithRewards = \App\Models\WinWorkWithRewards::first();
        if (!$winWorkWithRewards) {
            $winWorkWithRewards = new \App\Models\WinWorkWithRewards();
        }

        // Handle Advertising Options Array
        $advertisingOptions = [];
        if ($request->has('advertising_options')) {
            foreach ($request->advertising_options as $option) {
                if (!empty(trim($option))) {
                    $advertisingOptions[] = trim($option);
                }
            }
        }

        $winWorkWithRewards->update([
            'title' => $request->title,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'banner_title' => $request->banner_title,
            
            'main_title' => $request->main_title,
            'main_subtitle' => $request->main_subtitle,
            'clients_count' => $request->clients_count,
            'clients_text' => $request->clients_text,
            'freelancers_count' => $request->freelancers_count,
            'freelancers_text' => $request->freelancers_text,
            'orders_count' => $request->orders_count,
            'orders_text' => $request->orders_text,
            'main_image' => $request->main_image,
            
            'solutions_title' => $request->solutions_title,
            'solutions_subtitle' => $request->solutions_subtitle,
            
            'boosted_profile_title' => $request->boosted_profile_title,
            'boosted_profile_subtitle' => $request->boosted_profile_subtitle,
            'boosted_profile_content' => $request->boosted_profile_content,
            'boosted_profile_image' => $request->boosted_profile_image,
            
            'availability_badge_title' => $request->availability_badge_title,
            'availability_badge_subtitle' => $request->availability_badge_subtitle,
            'availability_badge_content' => $request->availability_badge_content,
            'availability_badge_image' => $request->availability_badge_image,
            
            'enhanced_proposals_title' => $request->enhanced_proposals_title,
            'enhanced_proposals_subtitle' => $request->enhanced_proposals_subtitle,
            'enhanced_proposals_content' => $request->enhanced_proposals_content,
            'enhanced_proposals_image' => $request->enhanced_proposals_image,
            
            'payment_title' => $request->payment_title,
            'payment_subtitle' => $request->payment_subtitle,
            'payment_content' => $request->payment_content,
            
            'why_use_title' => $request->why_use_title,
            'why_use_content' => $request->why_use_content,
            
            'getting_started_title' => $request->getting_started_title,
            'getting_started_content' => $request->getting_started_content,
            
            'place_bid_title' => $request->place_bid_title,
            'place_bid_content' => $request->place_bid_content,
            
            'advertising_options_title' => $request->advertising_options_title,
            'advertising_options' => $advertisingOptions,
            
            'helpful_resources_title' => $request->helpful_resources_title,
            'helpful_resources_content' => $request->helpful_resources_content,
            
            'ads_guide_title' => $request->ads_guide_title,
            'ads_guide_content' => $request->ads_guide_content,
            
            'master_ads_title' => $request->master_ads_title,
            'master_ads_content' => $request->master_ads_content,
            
            'cta_title' => $request->cta_title,
            'cta_button_text' => $request->cta_button_text,
        ]);

        return back()->with(toastr_success('Win Work With Rewards page updated successfully!'));
    }
}
