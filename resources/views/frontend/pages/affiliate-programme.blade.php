@php use plugins\PageBuilder\PageBuilderSetup; @endphp
@extends('frontend.layout.master')
@section('site_title', $affiliateProgram->title ?? 'Affiliate Programme')
@section('meta_title') {{ $affiliateProgram->meta_title ?? 'Join Our Affiliate Programme | Right Freelancer' }}@endsection
@section('meta_description')
{{ $affiliateProgram->meta_description ?? 'Earn money by referring clients and freelancers to Right Freelancer. Join our Affiliate Programme today and start generating passive income.' }}@endsection

@php
    $affiliateProgram = \App\Models\AffiliateProgramme::first();
    if (!$affiliateProgram) {
        $affiliateProgram = new \stdClass();
        $affiliateProgram->title = 'Affiliate Programme';
        $affiliateProgram->meta_title = 'Join Our Affiliate Programme | Right Freelancer';
        $affiliateProgram->meta_description = 'Earn money by referring clients and freelancers to Right Freelancer. Join our Affiliate Programme today and start generating passive income.';
        $affiliateProgram->banner_title = 'Affiliates';
        $affiliateProgram->hero_title = 'Recommend Right Freelancer. Earn Commissions.';
        $affiliateProgram->hero_subtitle = 'As an Right Freelancer affiliate you can bring value to your community by sharing in our mission to create economic opportunities so people have better lives.';
        $affiliateProgram->hero_button_text = 'Start Earning';
        $affiliateProgram->hero_image = 'assets/uploads/partnerimage/partnerwithus.png';
        $affiliateProgram->trusted_by_title = 'Trusted by';
        $affiliateProgram->easy_start_title = "It's easy to get started";
        $affiliateProgram->step1_title = 'Sign up';
        $affiliateProgram->step1_subtitle = "It's fast and easy to get started.";
        $affiliateProgram->step1_image = 'assets/uploads/affiliate_img/hand.png';
        $affiliateProgram->step2_title = 'Promote';
        $affiliateProgram->step2_subtitle = 'Share Right Freelancer with your audience.';
        $affiliateProgram->step2_image = 'assets/uploads/affiliate_img/hand1.png';
        $affiliateProgram->step3_title = 'Earn';
        $affiliateProgram->step3_subtitle = 'Start earning when the client funds a project.';
        $affiliateProgram->step3_image = 'assets/uploads/affiliate_img/dollar.png';
        $affiliateProgram->benefits_title = 'Right Freelancer Affiliate Benefits';
        $affiliateProgram->commission_title = 'Commissions';
        $affiliateProgram->commission_content = 'Get <span style="font-weight: 600;">70%</span> of the first contract spend up to <span style="font-weight: 600;">$150</span> for every new client referred to Right Freelancer & <span style="font-weight: 600;">5%</span> commission for repeat contracts for spend up to <span style="font-weight: 600;">$150</span>.';
        $affiliateProgram->commission_image = 'assets/uploads/affiliatesimage/60b11850cdadf22e07dcbbd7_How_it_Works_1_Post-A-Job_Martin_Nicholausson (3).png';
        $affiliateProgram->support_title = 'Support';
        $affiliateProgram->support_content = 'With Right Freelancer dedicated affiliate team you will have your questions answered and receive the help you need.';
        $affiliateProgram->support_image = 'assets/uploads/affiliatesimage/60c753a83ccd7e60ed907950_image.png';
        $affiliateProgram->resources_title = 'Resources';
        $affiliateProgram->resources_content = 'As an affiliate, you\'ll have access to regularly refreshed logos, ads, and banners to help optimize conversions.';
        $affiliateProgram->resources_image = 'assets/uploads/affiliatesimage/60c753b8dc5df76686dfb0ba_image.png';
        $affiliateProgram->why_title = 'Why Right Freelancer';
        $affiliateProgram->why_subtitle = 'The world\'s work marketplace';
        $affiliateProgram->why_content = 'Businesses and independent professionals from around the world come to Right Freelancer to grow their businesses, take control of their careers, and create meaningful work relationships.';
        $affiliateProgram->why_image = 'assets/uploads/affiliate_img/business.png';
        $affiliateProgram->promote_title = 'Two ways to promote';
        $affiliateProgram->promote_content = 'Everyone comes to Right Freelancer with a vision in mind. Project Catalog™ and Talent Marketplace™ give your audience two ways to fulfill their visions.';
        $affiliateProgram->jobs_title = 'More than 60k jobs posted every week';
        $affiliateProgram->jobs_content = 'With thousands of opportunities to connect, Right Freelancer unlocks ways for business and independent professionals to work together that weren\'t possible before.';
        $affiliateProgram->jobs_image = 'assets/uploads/affiliate_img/developers.png';
        $affiliateProgram->faq_title = 'Frequently asked questions';
        $affiliateProgram->cta_title = 'Join the world\'s work marketplace as an Right Freelancer Affiliate today.';
        $affiliateProgram->cta_button_text = 'Start Earning';
        $affiliateProgram->cta_image = 'assets/uploads/affiliate_img/dollar.png';
        $affiliateProgram->stats1_number = '49K';
        $affiliateProgram->stats1_text = 'Jobs we have handled in our Right Freelancer platform';
        $affiliateProgram->stats2_number = '$50M';
        $affiliateProgram->stats2_text = 'Earned by Freelancers in our platform till date';
        $affiliateProgram->stats3_number = '09X';
        $affiliateProgram->stats3_text = 'Awards received in IT for excellence in service';
    }
@endphp

@section('content')

    <style>
        .escrow-policy-banner {
            background-color: #309400;
            padding: 40px 0;
            text-align: center;
            color: white;
        }

        .escrow-policy-banner .escrow-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            color: white;
        }

        .escrow-policy-banner .effective-date {
            display: inline-block;
            background-color: #2ED47A;
            color: white;
            padding: 10px 25px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 500;
        }

        .start-earning-btn {
            background-color: white;
            color: #309400;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 22px;
        }

        .card-img-top {
            height: 180px;
            background-color: #d4edda;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .card-text {
            font-weight: 600;
            font-size: 1rem;
            color: #309400;
        }

        .card-title {
            font-size: 1.25rem;
        }

        .text-freelancer-green {
            color: #309400;
        }

        .bg-upwork-green-light {
            background-color: #E6F6ED;
        }

        .btn-freelancer-green {
            background-color: #309400;
            border-color: #309400;
            color: white;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-freelancer-green:hover {
            background-color: #6176f6;
            border-color: #6176f6;
            color: white;
        }

        .benefit-card .card-body {
            min-height: 150px;
        }

        .promote-card {
            border-radius: 12px;
            overflow: hidden;
        }

        .promote-card .card-img-top {
            height: 180px;
            object-fit: cover;
        }

        .promote-card-avatar {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            margin-top: -48px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 2px solid transparent;
            color: #666;
            font-weight: 600;
            padding: 0.5rem 1rem;
            margin-right: 1rem;
        }

        .nav-tabs .nav-link.active {
            color: #309400;
            border-color: #309400;
            background-color: transparent;
        }

        .nav-tabs .nav-link:hover {
            border-color: #309400;
            color: #309400;
        }

        .nav-tabs {
            border-bottom: 1px solid #E0E0E0;
            margin-bottom: 1.5rem;
        }

        .faq-item-content {
            background-color: #F8F8F8;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .faq-item-content p {
            font-size: 0.95rem;
            line-height: 1.5;
            color: #555;
        }

        .faq-item-content a {
            color: #309400;
            font-weight: 600;
            text-decoration: none;
        }

        .faq-item-content a:hover {
            text-decoration: underline;
        }


        .bg-upwork-green-light {
            background-color: #E6F6ED;
        }

        .btn-upwork-green {
            background-color: #14A800;
            border-color: #14A800;
            color: white;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-upwork-green:hover {
            background-color: #118C00;
            border-color: #118C00;
            color: white;
        }

        .marketplace-cta-box {
            background-color: #E6F6ED;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .marketplace-cta-box .illustration {
            width: 150px;
            height: auto;
            margin-right: 30px;
            flex-shrink: 0;
        }

        .marketplace-cta-box h3 {
            font-weight: 700;
            font-size: 2rem;
            color: #333;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        @media (max-width: 767.98px) {
            .marketplace-cta-box {
                flex-direction: column;
                text-align: center;
            }

            .marketplace-cta-box .illustration {
                margin-right: 0;
                margin-bottom: 20px;
            }
        }
    </style>
    <div class="banner-inner-area border-top pat-20 pab-20">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="banner-inner-contents">
                        <ul class="inner-menu">
                            <li class="list"><a href="https://www.rightfreelancer.com">Home </a></li>
                            <li class="list"> Affiliates </li>
                        </ul>
                        <h2 class="banner-inner-title">{{ $affiliateProgram->banner_title ?? 'Affiliates' }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="banner-inner-area border-top pat-20">
        <div class="container-fulid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="escrow-policy-banner">
                        <div class="container">
                            <div class="row">
                                <!-- Left 6 columns: Heading + Paragraph -->
                                <div class="col-lg-8">
                                    <h2 class="escrow-title text-start">{{ $affiliateProgram->hero_title ?? 'Recommend Right Freelancer. Earn Commissions.' }}</h2>
                                    <p class="text-start" style="font-size: 16px; color: #fff;">
                                        {{ $affiliateProgram->hero_subtitle ?? 'As an Right Freelancer affiliate you can bring value to your community by sharing in our mission to create economic opportunities so people have better lives.' }}
                                    </p>
                                    <div class="text-start">
                                        <a href="{{ route('affiliate.login') }}"
                                            class="btn btn-primary mt-3 start-earning-btn btn-profile">{{ $affiliateProgram->hero_button_text ?? 'Start Earning' }}</a>
                                    </div>
                                </div>
                                <!-- Right 4 columns: Image -->
                                <div class="col-lg-4 text-center">
                                    <div class="img-fluid w-100">
                                        <img src="{{ asset($affiliateProgram->hero_image ?? 'assets/uploads/partnerimage/partnerwithus.png') }}"
                                            alt="Partnership" style="height:100%; border-radius:10px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About area starts -->
    <section class="about-area py-4 section-bg-2">
        <div class="container">
            <div class="row align-items-center flex-wrap g-5">
                <div class="col-auto">
                    <h6 class="fw-semibold mb-0" style="font-size: 16px; color: #666;">{{ $affiliateProgram->trusted_by_title ?? 'Trusted by' }}</h6>
                </div>

                <div class="col d-flex flex-wrap align-items-center gap-4" style="gap: 8.5rem !important;">
                    <img src="{{ asset('assets/uploads/affiliatesimage/60c6edf1ac258c018f8dc1dd_NASDAQ_Logo.svg') }}"
                        alt="NASDAQ" style="height: 40px; object-fit: contain;" />
                    <img src="{{ asset('assets/uploads/affiliatesimage/6080bb0ff04903bb884fc2b0_Airbnb.svg') }}"
                        alt="Airbnb" style="height: 40px; object-fit: contain;" />
                    <img src="{{ asset('assets/uploads/affiliatesimage/6080bb0ff0490361a94fc2b4_Microsoft.svg') }}"
                        alt="Microsoft" style="height: 40px; object-fit: contain;" />
                    <img src="{{ asset('assets/uploads/affiliatesimage/6080bb0ff0490327424fc2b2_Bissell.svg') }}"
                        alt="Bissell" style="height: 40px; object-fit: contain;" />
                    <img src="{{ asset('assets/uploads/affiliatesimage/6080bb0ff0490343134fc2b5_logo-automatic.svg') }}"
                        alt="Automatic" style="height: 40px; object-fit: contain;" />
                </div>
                <hr>
            </div>
        </div>
    </section>

    <section class="about-area py-4 section-bg-2">
        <div class="container">
            <h2 class="fw-bold title mb-2" style="font-size:40px;">{{ $affiliateProgram->easy_start_title ?? "It's easy to get started" }}</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-img-top d-flex align-items-center justify-content-center p-4">
                            <img src="{{ asset($affiliateProgram->step1_image ?? 'assets/uploads/affiliate_img/hand.png') }}"
                                alt="Paper plane" class="img-fluid" style="max-height: 120px;">
                        </div>
                        <div class="card-body p-4">
                            <p class="card-text mb-1">{{ $affiliateProgram->step1_title ?? '1. Sign up' }}</p>
                            <h5 class="card-title mb-2">{{ $affiliateProgram->step1_subtitle ?? "It's fast and easy to get started." }}</h5>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-img-top d-flex align-items-center justify-content-center p-4">
                            <img src="{{ asset($affiliateProgram->step2_image ?? 'assets/uploads/affiliate_img/hand1.png') }}"
                                alt="People promoting" class="img-fluid" style="max-height: 120px;">
                        </div>
                        <div class="card-body p-4">
                            <p class="card-text mb-1">{{ $affiliateProgram->step2_title ?? '2. Promote' }}</p>
                            <h5 class="card-title mb-2">{{ $affiliateProgram->step2_subtitle ?? 'Share Right Freelancer with your audience.' }}</h5>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-img-top d-flex align-items-center justify-content-center p-4">
                            <img src="{{ asset($affiliateProgram->step3_image ?? 'assets/uploads/affiliate_img/dollar.png') }}"
                                alt="Funnel with money" class="img-fluid" style="max-height: 120px;">
                        </div>
                        <div class="card-body p-4">
                            <p class="card-text mb-1">{{ $affiliateProgram->step3_title ?? '3. Earn' }}</p>
                            <h5 class="card-title mb-2">{{ $affiliateProgram->step3_subtitle ?? 'Start earning when the client funds a project.' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-area py-4 section-bg-2">
        <div class="container">
            <h2 class="fw-bold title mb-2" style="font-size:40px;">{{ $affiliateProgram->benefits_title ?? 'Right Freelancer Affiliate Benefits' }}</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm benefit-card" style="border-radius: 12px;">
                        <div class="card-img-top bg-upwork-green-light d-flex align-items-center justify-content-center p-4"
                            style="height: 180px; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                            <img src="{{ asset($affiliateProgram->commission_image ?? 'assets/uploads/affiliatesimage/60b11850cdadf22e07dcbbd7_How_it_Works_1_Post-A-Job_Martin_Nicholausson (3).png') }}"
                                alt="Commissions icon" class="img-fluid" style="max-height: 120px;">
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-2 text-freelancer-green" style="font-weight: 700; font-size: 1.25rem;">
                                {{ $affiliateProgram->commission_title ?? 'Commissions' }}</h5>
                            <p style="font-size: 0.95rem; line-height: 1.5;">
                                {!! $affiliateProgram->commission_content ?? 'Get <span style="font-weight: 600;">70%</span> of the first contract spend up to <span style="font-weight: 600;">$150</span> for every new client referred to Right Freelancer & <span style="font-weight: 600;">5%</span> commission for repeat contracts for spend up to <span style="font-weight: 600;">$150</span>.' !!}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm benefit-card" style="border-radius: 12px;">
                        <div class="card-img-top bg-upwork-green-light d-flex align-items-center justify-content-center p-4"
                            style="height: 180px; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                            <img src="{{ asset($affiliateProgram->support_image ?? 'assets/uploads/affiliatesimage/60c753a83ccd7e60ed907950_image.png') }}"
                                alt="Support icon" class="img-fluid" style="max-height: 120px;">
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-2 text-freelancer-green" style="font-weight: 700; font-size: 1.25rem;">
                                {{ $affiliateProgram->support_title ?? 'Support' }}</h5>
                            <p style="font-size: 0.95rem; line-height: 1.5;">
                                {{ $affiliateProgram->support_content ?? 'With Right Freelancer dedicated affiliate team you will have your questions answered and receive the help you need.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm benefit-card" style="border-radius: 12px;">
                        <div class="card-img-top bg-upwork-green-light d-flex align-items-center justify-content-center p-4"
                            style="height: 180px; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                            <img src="{{ asset($affiliateProgram->resources_image ?? 'assets/uploads/affiliatesimage/60c753b8dc5df76686dfb0ba_image.png') }}"
                                alt="Resources icon" class="img-fluid" style="max-height: 120px;">
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-2 text-freelancer-green" style="font-weight: 700; font-size: 1.25rem;">
                                {{ $affiliateProgram->resources_title ?? 'Resources' }}</h5>
                            <p style="font-size: 0.95rem; line-height: 1.5;">
                                {{ $affiliateProgram->resources_content ?? 'As an affiliate, you\'ll have access to regularly refreshed logos, ads, and banners to help optimize conversions.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="">
                <a href="{{ route('affiliate.register') }}" class="btn btn-freelancer-green btn-profile">{{ $affiliateProgram->hero_button_text ?? 'Start Earning' }}</a>
            </div>
        </div>
    </section>

    <section class="about-area py-4 section-bg-2">
        <div class="container">
            <h2 class="fw-bold title mb-2" style="font-size:40px;">{{ $affiliateProgram->why_title ?? 'Why Right Freelancer' }}</h2>
            <hr>

            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="pe-lg-5">
                        <h3 class="mb-3" style="font-weight: 700; font-size: 2.25rem;">{{ $affiliateProgram->why_subtitle ?? 'The world\'s work marketplace' }}</h3>
                        <p class="text-secondary" style="font-size: 1rem; line-height: 1.6;">
                            {{ $affiliateProgram->why_content ?? 'Businesses and independent professionals from around the world come to Right Freelancer to grow their businesses, take control of their careers, and create meaningful work relationships.' }}
                        </p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="d-flex justify-content-center justify-content-lg-end">
                        <img src="{{ asset($affiliateProgram->why_image ?? 'assets/uploads/affiliate_img/business.png') }}"
                            style="border-radius:6px;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-area py-4 section-bg-2">
        <div class="container">
            <div class="row align-items-center flex-wrap g-5">

                <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-4">

                        <div class="card promote-card border-0 shadow-sm w-100"
                            style="max-width: 280px; height: -webkit-fill-available;">
                            <img src="{{ asset($affiliateProgram->promote_image ?? 'assets/uploads/affiliate_img/blog.jpg') }}"
                                alt="Person typing on a laptop with 'BLOG' on screen" class="card-img-top">
                            <div class="card-body p-3">
                                <h5 class="card-title mb-1" style="font-weight: 600;">{{ $affiliateProgram->promote_title ?? 'Blog posts' }}</h5>
                                <p class="card-text text-muted" style="font-size: 0.9rem;">{{ $affiliateProgram->promote_subtitle ?? 'From $250' }}</p>
                            </div>
                        </div>

                        <div class="card promote-card border-0 shadow-sm w-100" style="max-width: 280px;">
                            <div class="card-img-top d-flex align-items-center justify-content-center"
                                style="height: 120px; background-color: #E6F6ED;">

                            </div>
                            <div class="card-body text-center p-3 pt-0">
                                <img src="{{ asset($affiliateProgram->promote_avatar ?? 'assets/uploads/affiliatesimage/afaq.jpg') }}" alt="Afaq avatar"
                                    class="promote-card-avatar mx-auto d-block">
                                <h5 class="card-title mt-2 mb-1" style="font-weight: 600;">{{ $affiliateProgram->promote_name ?? 'Afaq' }}</h5>
                                <p class="card-text text-muted mb-2" style="font-size: 0.9rem;">{{ $affiliateProgram->promote_profession ?? 'Laravel Developer' }}</p>
                                <p class="card-text">
                                    <span class="single-project-content-review mt-2">&#9733; 5.0/5 </span><span
                                        class="text-muted">{{ $affiliateProgram->promote_reviews ?? '(124 jobs)' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="ps-lg-5 text-center text-lg-start">
                        <h3 class="mb-3" style="font-weight: 700; font-size: 2.5rem;">{{ $affiliateProgram->promote_title ?? 'Two ways to promote' }}</h3>
                        <p class="text-secondary" style="font-size: 1.1rem; line-height: 1.6;">
                            {{ $affiliateProgram->promote_content ?? 'Everyone comes to Right Freelancer with a vision in mind. Project Catalog™ and Talent Marketplace™ give your audience two ways to fulfill their visions.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section class="about-area py-4 section-bg-2">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                    <div class="pe-lg-5 text-center text-lg-start">
                        <h3 class="mb-3" style="font-weight: 700; font-size: 2rem;">{{ $affiliateProgram->jobs_title ?? 'More than 60k jobs posted every week' }}
                        </h3>
                        <p class="text-secondary" style="font-size: 1.1rem; line-height: 1.6;">
                            {{ $affiliateProgram->jobs_content ?? 'With thousands of opportunities to connect, Right Freelancer unlocks ways for business and independent professionals to work together that weren\'t possible before.' }}
                        </p>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="d-flex justify-content-center justify-content-lg-end">
                        <img src="{{ asset($affiliateProgram->jobs_image ?? 'assets/uploads/affiliate_img/developers.png') }}"
                            style="border-radius:6px;">
                    </div>
                </div>
                <hr class="mt-5">
            </div>
        </div>
    </section>

    <section class="about-area py-4 section-bg-2">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-4 mb-4 mb-lg-0">
                    <h3 class="mb-3" style="font-weight: 700; font-size: 2rem;">{{ $affiliateProgram->faq_title ?? 'Frequently asked questions' }}</h3>

                </div>
                <div class="col-12 col-lg-1"></div>

                <div class="col-12 col-lg-7">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs" id="faqTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active card-text" id="general-tab" data-bs-toggle="tab"
                                data-bs-target="#general" type="button" role="tab" aria-controls="general"
                                aria-selected="true">General</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="about-upwork-tab" data-bs-toggle="tab"
                                data-bs-target="#about-upwork" type="button" role="tab" aria-controls="about-upwork"
                                aria-selected="false">About Right Freelancer</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="commissions-tab" data-bs-toggle="tab" data-bs-target="#commissions"
                                type="button" role="tab" aria-controls="commissions"
                                aria-selected="false">Commissions</button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="faqTabContent">
                        <!-- General Tab Pane -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <!-- FAQ Item 1 -->
                            <div class="faq-item-content">
                                <h4>What is the Right Freelancer affiliate program?</h4>
                                <p class="mt-2">
                                    The Right Freelancer affiliate program gives partners an opportunity to earn commissions
                                    by promoting Right Freelancer on their websites through affiliate links.

                                </p>
                            </div>
                            <!-- FAQ Item 2 -->
                            <div class="faq-item-content">
                                <h4>How do I join the Right Freelancer affiliate program?</h4>
                                <p class="mt-2">
                                    Join the program by signing up <a href="{{ route('affiliate.register') }}">here</a>.
                                    <br>If you have any questions about joining the program or during the
                                </p>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="about-upwork" role="tabpanel" aria-labelledby="about-upwork-tab">
                            <div class="faq-item-content">
                                <h4>What is Right Freelancer?</h4>
                                <p class="mt-2">
                                    Upwork is the world's work marketplace, connecting businesses with independent talent.
                                </p>
                            </div>
                        </div>

                        <!-- Commissions Tab Pane (Placeholder) -->
                        <div class="tab-pane fade" id="commissions" role="tabpanel" aria-labelledby="commissions-tab">
                            <div class="faq-item-content">
                                <h4>How are commissions calculated?</h4>
                                <p class="mt-2">
                                    Commissions are calculated based on the first contract spend and repeat contracts, as
                                    outlined in the program terms.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section class="about-area py-4 section-bg-2">
        <div class="container">
            <div class="marketplace-cta-box">

                <div class="row align-items-center gx-md-5">
                    <!-- Left Column: Illustration -->
                    <div class="col-12 col-md-auto mb-4 mb-md-0">
                        <img src="{{ asset($affiliateProgram->cta_image ?? 'assets/uploads/affiliate_img/dollar.png') }}"
                            alt="Funnel illustration with money and invoice" class="illustration">
                    </div>

                    <div class="col-12 col-md col-text">
                        <h3 class="mb-3" style="font-weight: 700; font-size: 2rem;">{{ $affiliateProgram->cta_title ?? 'Join the world\'s work marketplace as an Right Freelancer Affiliate today.' }}</h3>

                        <div class="">
                            <a href="{{ route('affiliate.register') }}" class="btn btn-freelancer-green btn-profile">{{ $affiliateProgram->cta_button_text ?? 'Start Earning' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About area end -->
    <div class="credit-area">
        <div class="container">
            <div class="credit-wrapper border-bottom pat-50 pab-100" data-padding-top="50" data-padding-bottom="100"
                style="background-color:">
                <div class="row g-4">
                    <div class="col-lg-4 col-sm-6">
                        <div class="credit-item text-center">
                            <h3 class="credit-item-title">
                                <span class="credit-item-title-heading">{{ $affiliateProgram->stats1_number ?? '49K' }}</span>
                            </h3>
                            <p class="credit-item-para">{{ $affiliateProgram->stats1_text ?? 'Jobs we have handled in our Right Freelancer platform' }}</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="credit-item text-center">
                            <h3 class="credit-item-title">
                                <span class="credit-item-title-heading">{{ $affiliateProgram->stats2_number ?? '$50M' }}</span>
                            </h3>
                            <p class="credit-item-para">{{ $affiliateProgram->stats2_text ?? 'Earned by Freelancers in our platform till date' }}</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="credit-item text-center">
                            <h3 class="credit-item-title">
                                <span class="credit-item-title-heading">{{ $affiliateProgram->stats3_number ?? '09X' }}</span>
                            </h3>
                            <p class="credit-item-para">{{ $affiliateProgram->stats3_text ?? 'Awards received in IT for excellence in service' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection