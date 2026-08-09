@php use plugins\PageBuilder\PageBuilderSetup; @endphp
@extends('frontend.layout.master')

@section('site_title', $policy->meta_title ?? $policy->title ?? __('Terms of Service'))

@section('meta_title')
    {{ $policy->meta_title ?? $policy->title ?? __('Terms of Service - Right Freelancer | Know Your Rights & Responsibilities') }}
@endsection

@section('meta_description')
    {{ $policy->meta_description ?? '' }}
@endsection

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

        .faq-section {
            padding: 40px 0;
            background: white;
        }

        .faq-accordion {
            max-width: 800px;
            margin: 0 auto;
        }

        .accordion-item {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .accordion-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .accordion-button {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 20px 25px;
            border: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .accordion-button:not(.collapsed) {
            background: #28a745;
            color: white;
            box-shadow: none;
        }

        .accordion-button::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23667eea'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
            transition: transform 0.2s ease-in-out;
        }

        .accordion-button:not(.collapsed):after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }

        .accordion-body {
            padding: 25px;
            background: white;
            color: #6c757d;
            line-height: 1.7;
            font-size: 1rem;
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
                            <li class="list">{{ __('Terms of Service') }}</li>
                        </ul>
                        <h2 class="banner-inner-title">{{ __('Terms of Service') }}</h2>
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
                    <h1 class="animate-fade-in">{{ $policy->heading ?? $policy->title ?? __('Terms of Service') }}</h1>
                    @if(!empty($policy->short_description))
                        <p class="animate-fade-in">{{ $policy->short_description }}</p>
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
                    <div class="benefit-content">
                        @if(!empty($policy->content))
                            {!! $policy->content !!}
                        @else
                            <p class="text-center py-5">Terms of Service content is currently empty. Please add content from the
                                admin panel.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= FAQ Section ================= -->
    @if(!empty($policy->faqs) && is_array($policy->faqs))
        <section class="faq-section">
            <div class="container">
                <div class="section-title">
                    <h2>Frequently Asked Questions</h2>
                    <p>Have questions about our Terms of Service? Read our FAQs.</p>
                </div>

                <div class="faq-accordion">
                    <div class="accordion" id="faqAccordion">
                        @foreach($policy->faqs as $index => $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $index }}">
                                    <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                                        aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                        {{ $faq['question'] ?? '' }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                    aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        {{ $faq['answer'] ?? '' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

@endsection