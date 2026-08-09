@php use plugins\PageBuilder\PageBuilderSetup; @endphp
@extends('frontend.layout.master')
@section('site_title', $winWorkWithRewards->title ?? 'Win Work With Reward')
@section('meta_title') {{ $winWorkWithRewards->meta_title ?? 'Win Work With Reward - Earn Bonus Opportunities on Right Freelancer' }}@endsection
@section('meta_description')
{{ $winWorkWithRewards->meta_description ?? 'Participate in rewarding activities and challenges on Right Freelancer to win extra work opportunities, bonuses, and exclusive recognition.' }}@endsection

@php
    $winWorkWithRewards = \App\Models\WinWorkWithRewards::first();
    if (!$winWorkWithRewards) {
        $winWorkWithRewards = new \stdClass();
        $winWorkWithRewards->title = 'Win Work With Reward';
        $winWorkWithRewards->meta_title = 'Win Work With Reward - Earn Bonus Opportunities on Right Freelancer';
        $winWorkWithRewards->meta_description = 'Participate in rewarding activities and challenges on Right Freelancer to win extra work opportunities, bonuses, and exclusive recognition.';
        $winWorkWithRewards->banner_title = 'Win Works and Rewards';
        $winWorkWithRewards->main_title = 'Maximize Your Exposure with Ads';
        $winWorkWithRewards->main_subtitle = 'Increase your profile visibility and secure more job opportunities with our powerful ad solutions. Whether you\'re looking for more invites or closing more deals, we have the right tools to help you succeed. Get started today!';
        $winWorkWithRewards->clients_count = '39K';
        $winWorkWithRewards->clients_text = 'Clients working with us';
        $winWorkWithRewards->freelancers_count = '60K';
        $winWorkWithRewards->freelancers_text = 'Freelancers working with us';
        $winWorkWithRewards->orders_count = '50K';
        $winWorkWithRewards->orders_text = 'Orders processed';
        $winWorkWithRewards->main_image = 'assets/frontend/img/boosted.png';
        $winWorkWithRewards->solutions_title = 'Discover Our Ad Solutions';
        $winWorkWithRewards->solutions_subtitle = 'Explore three effective ways to reach your career goals with our ad products.';
        $winWorkWithRewards->boosted_profile_title = 'Spotlight Your Profile';
        $winWorkWithRewards->boosted_profile_subtitle = 'Boosted Pro Profile';
        $winWorkWithRewards->boosted_profile_content = 'Investing in Connects can enhance your likelihood of being hired by up to 2x';
        $winWorkWithRewards->boosted_profile_image = 'assets/frontend/img/boosted-1.jpg';
        $winWorkWithRewards->availability_badge_title = 'Show Your Availability';
        $winWorkWithRewards->availability_badge_subtitle = 'Availability Badge';
        $winWorkWithRewards->availability_badge_content = 'Freelancers with this badge receive up to 75% more job invitations.';
        $winWorkWithRewards->availability_badge_image = 'assets/frontend/img/boosted-2.jpg';
        $winWorkWithRewards->enhanced_proposals_title = 'Reach New Heights';
        $winWorkWithRewards->enhanced_proposals_subtitle = 'Enhanced Proposals';
        $winWorkWithRewards->enhanced_proposals_content = 'Generates 10x greater returns on ad spend';
        $winWorkWithRewards->enhanced_proposals_image = 'assets/frontend/img/boosted-3.jpg';
        $winWorkWithRewards->payment_title = 'What\'s the payment process for ads?';
        $winWorkWithRewards->payment_subtitle = 'Ad Payment with Connects';
        $winWorkWithRewards->payment_content = 'On RightFreelancer, ads are purchased using Connects, a virtual currency. You can also select a custom amount to match your specific needs and budget. Each Connect is priced at $0.15 (USD). Freelancers use Connects to submit proposals and bid on ad products such as Boosted Proposals, Boosted Profiles, and the Availability Badge.';
        $winWorkWithRewards->why_use_title = 'Why Use Ads?';
        $winWorkWithRewards->why_use_content = 'Using ads is completely optional—you choose when and if you want to use them. While ads aren\'t necessary to submit proposals, they help increase your visibility, land the projects you care about most, and streamline your workflow to boost your earnings on high-quality jobs.';
        $winWorkWithRewards->getting_started_title = 'How to Get Started';
        $winWorkWithRewards->getting_started_content = 'Getting started is simple and low-risk. Select an ad product that aligns with your goals, then place a bid to enter the auction. If you win the auction, you\'ll gain increased visibility, engage more clients, and have a better chance of securing the projects you\'re most interested in.';
        $winWorkWithRewards->place_bid_title = 'Where Do I Place a Bid for an Ad?';
        $winWorkWithRewards->place_bid_content = 'After choosing the ad product that fits your needs, placing a bid is quick and easy. Follow a few simple steps to submit your bid and find out if you\'ve won the auction. These articles offer structured walkthroughs for setting up and managing essential features.';
        $winWorkWithRewards->advertising_options_title = 'Advertising Options';
        $winWorkWithRewards->advertising_options = ['Enhanced Proposals', 'Availability Indicator', 'Profile Promotion'];
        $winWorkWithRewards->helpful_resources_title = 'Helpful Resources';
        $winWorkWithRewards->helpful_resources_content = 'Find out how advertising can increase your visibility and help you secure the best opportunities.';
        $winWorkWithRewards->ads_guide_title = 'Ads Guide';
        $winWorkWithRewards->ads_guide_content = 'Build, manage, and optimize your campaigns.';
        $winWorkWithRewards->master_ads_title = 'Master your ads';
        $winWorkWithRewards->master_ads_content = 'Build, manage, and optimize.';
        $winWorkWithRewards->cta_title = 'Use ads to win work.';
        $winWorkWithRewards->cta_button_text = 'Add now';
    }
@endphp

<style>
    /* ================= Professional Policy Styling ================= */
    .benefit-hero {
        background: linear-gradient(135deg, #309400 0%, #309400 100%);
        color: white;
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }

    .benefit-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="50" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="30" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .benefit-hero .container {
        position: relative;
        z-index: 2;
    }

    .benefit-hero h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: white;
    }

    .benefit-hero p {
        font-size: 1.3rem;
        opacity: 0.95;
        max-width: 600px;
        margin: 0 auto;
        text-align: center;
        color: white;
    }

    .benefits-section {
        padding: 12px 0;
        background: #f8f9fa;
    }

    .benefit-content {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
    }

    .benefit-content h2,
    .benefit-content h3 {
        color: #2c3e50;
        font-weight: 600;
        margin-top: 25px;
        margin-bottom: 15px;
    }

    .benefit-content p {
        color: #6c757d;
        line-height: 1.7;
        font-size: 1.05rem;
        margin-bottom: 20px;
    }

    .benefit-content ul {
        padding-left: 25px;
        margin-bottom: 20px;
    }

    .benefit-content li {
        color: #6c757d;
        margin-bottom: 10px;
        line-height: 1.6;
        font-size: 1.05rem;
    }

    .section-title {
        text-align: center;
        margin-bottom: 60px;
    }

    .section-title h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
    }

    .section-title p {
        font-size: 1.2rem;
        color: #6c757d;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Feature Cards Styling */
    .single-feature {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid #e9ecef;
    }

    .single-feature:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .single-feature h6 {
        color: #309400;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }

    .single-feature h4 {
        color: #2c3e50;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .single-feature p {
        color: #6c757d;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .single-feature img {
        border-radius: 10px;
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    /* Counter Styling */
    .about-counter-item {
        text-align: center;
        padding: 20px;
    }

    .about-counter-item-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #309400;
        margin-bottom: 10px;
    }

    .about-counter-item-para {
        color: #6c757d;
        font-size: 1.1rem;
        margin: 0;
    }

    /* CTA Section */
    .cta-section {
        color: white;
        text-align: center;
        border-radius: 15px;
    }

    .cta-section h2 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .cmn-btn.btn-bg-1 {
        background: white;
        color: #309400;
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .cmn-btn.btn-bg-1:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .benefit-hero h1 {
            font-size: 2rem;
        }

        .benefit-hero p {
            font-size: 1.1rem;
        }

        .section-title h2 {
            font-size: 2rem;
        }

        .benefit-content {
            padding: 20px;
        }

        .cta-section h2 {
            font-size: 1.8rem;
        }
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeInUp 0.6s ease-out;
    }
</style>

@section('content')
    <!-- ================= Hero Section ================= -->
    <section class="benefit-hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1 class="animate-fade-in">{{ $winWorkWithRewards->banner_title ?? 'Win Works and Rewards' }}</h1>
                    <p class="animate-fade-in">{{ $winWorkWithRewards->main_subtitle ?? 'Increase your profile visibility and secure more job opportunities with our powerful ad solutions. Whether you\'re looking for more invites or closing more deals, we have the right tools to help you succeed. Get started today!' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= Main Content Section ================= -->
    <section class="benefits-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="benefit-content">
                        <div class="row align-items-center mb-3">
                            <div class="col-lg-6">
                                <div class="section-title text-left">
                                    <h2>{{ $winWorkWithRewards->main_title ?? 'Maximize Your Exposure with Ads' }}</h2>
                                    <p class="text-left">{{ $winWorkWithRewards->main_subtitle ?? 'Increase your profile visibility and secure more job opportunities with our powerful ad solutions. Whether you\'re looking for more invites or closing more deals, we have the right tools to help you succeed. Get started today!' }}</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-center">
                                    <img src="{{ asset($winWorkWithRewards->main_image ?? 'assets/frontend/img/boosted.png') }}" alt="Boosted Image" class="img-fluid rounded" style="max-height: 400px; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                        <!-- Counter Section -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="about-counter-item">
                                    <h3 class="about-counter-item-title">
                                        <span class="about-counter-item-title-heading">{{ $winWorkWithRewards->clients_count ?? '39K' }}</span>
                                    </h3>
                                    <p class="about-counter-item-para">{{ $winWorkWithRewards->clients_text ?? 'Clients working with us' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="about-counter-item">
                                    <h3 class="about-counter-item-title">
                                        <span class="about-counter-item-title-heading">{{ $winWorkWithRewards->freelancers_count ?? '60K' }}</span>
                                    </h3>
                                    <p class="about-counter-item-para">{{ $winWorkWithRewards->freelancers_text ?? 'Freelancers working with us' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="about-counter-item">
                                    <h3 class="about-counter-item-title">
                                        <span class="about-counter-item-title-heading">{{ $winWorkWithRewards->orders_count ?? '50K' }}</span>
                                    </h3>
                                    <p class="about-counter-item-para">{{ $winWorkWithRewards->orders_text ?? 'Orders processed' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Solutions Section -->
                        <div class="section-title">
                            <h2>{{ $winWorkWithRewards->solutions_title ?? 'Discover Our Ad Solutions' }}</h2>
                            <p>{{ $winWorkWithRewards->solutions_subtitle ?? 'Explore three effective ways to reach your career goals with our ad products.' }}</p>
                        </div>

                        <div class="row g-4">
                            <!-- Boosted Profile -->
                            <div class="col-lg-4 col-md-6">
                                <div class="single-feature">
                                    <h6>{{ $winWorkWithRewards->boosted_profile_title ?? 'Spotlight Your Profile' }}</h6>
                                    <h4>{{ $winWorkWithRewards->boosted_profile_subtitle ?? 'Boosted Pro Profile' }}</h4>
                                    <p>{{ $winWorkWithRewards->boosted_profile_content ?? 'Investing in Connects can enhance your likelihood of being hired by up to 2x' }}</p>
                                    <img src="{{ asset($winWorkWithRewards->boosted_profile_image ?? 'assets/frontend/img/boosted-1.jpg') }}" alt="Boosted Profile">
                                </div>
                            </div>

                            <!-- Availability Badge -->
                            <div class="col-lg-4 col-md-6">
                                <div class="single-feature">
                                    <h6>{{ $winWorkWithRewards->availability_badge_title ?? 'Show Your Availability' }}</h6>
                                    <h4>{{ $winWorkWithRewards->availability_badge_subtitle ?? 'Availability Badge' }}</h4>
                                    <p>{{ $winWorkWithRewards->availability_badge_content ?? 'Freelancers with this badge receive up to 75% more job invitations.' }}</p>
                                    <img src="{{ asset($winWorkWithRewards->availability_badge_image ?? 'assets/frontend/img/boosted-2.jpg') }}" alt="Availability Profile">
                                </div>
                            </div>

                            <!-- Enhanced Proposals -->
                            <div class="col-lg-4 col-md-6">
                                <div class="single-feature">
                                    <h6>{{ $winWorkWithRewards->enhanced_proposals_title ?? 'Reach New Heights' }}</h6>
                                    <h4>{{ $winWorkWithRewards->enhanced_proposals_subtitle ?? 'Enhanced Proposals' }}</h4>
                                    <p>{{ $winWorkWithRewards->enhanced_proposals_content ?? 'Generates 10x greater returns on ad spend' }}</p>
                                    <img src="{{ asset($winWorkWithRewards->enhanced_proposals_image ?? 'assets/frontend/img/boosted-3.jpg') }}" alt="Boosted Proposals">
                                </div>
                            </div>
                        </div>
                        <!-- PageBuilder Widget -->
                        @php
                            $promotionProfile = \App\Models\PageBuilder::where('addon_name', 'ProfilePromotion')->first();
                        @endphp
                        {!! plugins\PageBuilder\PageBuilderSetup::render_widgets_by_name_for_frontend(plugins\PageBuilder\PageBuilderSetup::getWidgetArgs($promotionProfile)) !!}

                        <!-- Payment Information -->
                        <h3>{{ $winWorkWithRewards->payment_title ?? 'What\'s the payment process for ads?' }}</h3>
                        <h4>{{ $winWorkWithRewards->payment_subtitle ?? 'Ad Payment with Connects' }}</h4>
                        <p>{{ $winWorkWithRewards->payment_content ?? 'On RightFreelancer, ads are purchased using Connects, a virtual currency. You can also select a custom amount to match your specific needs and budget. Each Connect is priced at $0.15 (USD). Freelancers use Connects to submit proposals and bid on ad products such as Boosted Proposals, Boosted Profiles, and the Availability Badge.' }}</p>

                        <!-- Why Use Ads -->
                        <h3>{{ $winWorkWithRewards->why_use_title ?? 'Why Use Ads?' }}</h3>
                        <p>{{ $winWorkWithRewards->why_use_content ?? 'Using ads is completely optional—you choose when and if you want to use them. While ads aren\'t necessary to submit proposals, they help increase your visibility, land the projects you care about most, and streamline your workflow to boost your earnings on high-quality jobs.' }}</p>

                        <!-- Getting Started -->
                        <h3>{{ $winWorkWithRewards->getting_started_title ?? 'How to Get Started' }}</h3>
                        <p>{{ $winWorkWithRewards->getting_started_content ?? 'Getting started is simple and low-risk. Select an ad product that aligns with your goals, then place a bid to enter the auction. If you win the auction, you\'ll gain increased visibility, engage more clients, and have a better chance of securing the projects you\'re most interested in.' }}</p>

                        <!-- Place Bid -->
                        <h3>{{ $winWorkWithRewards->place_bid_title ?? 'Where Do I Place a Bid for an Ad?' }}</h3>
                        <p>{{ $winWorkWithRewards->place_bid_content ?? 'After choosing the ad product that fits your needs, placing a bid is quick and easy. Follow a few simple steps to submit your bid and find out if you\'ve won the auction. These articles offer structured walkthroughs for setting up and managing essential features.' }}</p>

                        <!-- Advertising Options -->
                        <h3>{{ $winWorkWithRewards->advertising_options_title ?? 'Advertising Options' }}</h3>
                        <ul>
                            @if($winWorkWithRewards->advertising_options && is_array($winWorkWithRewards->advertising_options))
                                @foreach($winWorkWithRewards->advertising_options as $option)
                                    <li>{{ $option }}</li>
                                @endforeach
                            @else
                                <li>Enhanced Proposals</li>
                                <li>Availability Indicator</li>
                                <li>Profile Promotion</li>
                            @endif
                        </ul>

                        <!-- Helpful Resources -->
                        <h3>{{ $winWorkWithRewards->helpful_resources_title ?? 'Helpful Resources' }}</h3>
                        <p>{{ $winWorkWithRewards->helpful_resources_content ?? 'Find out how advertising can increase your visibility and help you secure the best opportunities.' }}</p>

                        <!-- Ads Guide -->
                        <h3>{{ $winWorkWithRewards->ads_guide_title ?? 'Ads Guide' }}</h3>
                        <p><strong>{{ $winWorkWithRewards->ads_guide_content ?? 'Build, manage, and optimize your campaigns.' }}</strong></p>
                        <p>Build customized ad campaigns tailored to your specific audience segments.</p>

                        <!-- Master Ads -->
                        <h3>{{ $winWorkWithRewards->master_ads_title ?? 'Master your ads' }}</h3>
                        <p><strong>{{ $winWorkWithRewards->master_ads_content ?? 'Build, manage, and optimize.' }}</strong></p>
                        <p><strong>Effortlessly create and optimize ad campaigns.</strong></p>
                        <p><strong>Design, manage, and enhance ad campaigns with rightfreelancer.</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= CTA Section ================= -->
    <section class="benefits-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cta-section">
                        <h2>{{ $winWorkWithRewards->cta_title ?? 'Use ads to win work.' }}</h2>
                        <div class="btn-wrapper mb-3">
                            <a href="{{ route('freelancer.ad.manage') }}" class="cmn-btn btn-bg-1">{{ $winWorkWithRewards->cta_button_text ?? 'Add now' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection