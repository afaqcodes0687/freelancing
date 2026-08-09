@php use plugins\PageBuilder\PageBuilderSetup; @endphp
@extends('frontend.layout.master')
@section('site_title', $investor_relation->title ?? __('Investor Relation'))
@section('meta_title', $investor_relation->meta_title ?? __('Investor Relation - Financial Insights and Business Strategy | Right Freelancer'))
@section('meta_description', $investor_relation->meta_description ?? __('Explore investor relations at Right Freelancer. Get financial reports, company strategy, and investment opportunities all in one place.'))

@section('content')
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
            padding: 40px 0;
            background: #f8f9fa;
        }

        .benefit-content {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
            margin-bottom: 40px;
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

    <div class="banner-inner-area border-top pat-20">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="banner-inner-contents">
                        <ul class="inner-menu">
                            <li class="list"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                            <li class="list">{{ $investor_relation->title ?? __('Investor Relations') }}</li>
                        </ul>
                        <h2 class="banner-inner-title">{{ $investor_relation->title ?? __('Investor Relations') }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= Hero Section ================= -->
    <section class="benefit-hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1 class="animate-fade-in">{{ $investor_relation->title ?? __('Investor Relations') }}</h1>
                    @if(!empty($investor_relation->meta_description))
                        <p class="animate-fade-in">{{ $investor_relation->meta_description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- ================= Dynamic Content Section ================= -->
    <section class="benefits-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="benefit-content text-left">
                        @if(!empty($investor_relation->content))
                            {!! $investor_relation->content !!}
                        @else
                            <p class="section-para"><p style="text-align:left;"><span style="font-weight:normal;"> All investors are obliged to fulfill legal and social rules in their profession and personal conduct, with employees of Right Freelancer, as well as the public. They are expected to understand that the company holds a zero tolerance policy for any behavioral misconduct on the part of the shareholders within the company. The policy briefly instructs:</span></p>

                            <p class="section-para"><p style="text-align:left;"><span style="font-weight:normal;">means any services provided by Freelancers.</span></p>
                            <ul>
                             <li>- Abstinence from sexual harassment</li>
                              <li>- No involvement in any sort of criminal or fraudulent activity</li>
                               <li>- No engagement in bribery, corporate espionage or oligarchical decision making</li>
                                <li>- Prohibition of engagement in any pressurizing or threatening relating to the business operations</li>
                                 <li>- Prohibition of involvement in prejudice, hate crimes or shaming individuals</li>
                                 <li>- Promise to uphold decency and act without malice for one another within the scope of business</li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
