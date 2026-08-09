@php use plugins\PageBuilder\PageBuilderSetup; @endphp
@extends('frontend.layout.master')

@section('site_title', $benefit->meta_title ?? $benefit->title)

@section('meta_title')
    {{ $benefit->meta_title ?? $benefit->title }}
@endsection

@section('meta_description')
    {{ $benefit->meta_description ?? '' }}
@endsection

@section('content')

    <style>
        /* ================= Professional Client Benefits Styling ================= */

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

        .benefit-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #309400;
        }

        .benefit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .benefit-card h3 {
            color: #309400;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .benefit-card h3 i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .benefit-card p {
            color: #6c757d;
            line-height: 1.6;
            font-size: 1rem;
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

        .accordion-button:not(.collapsed)::after {
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

        .benefit-content {
            margin-top: 40px;
        }

        .benefit-content h4 {
            color: #2c3e50;
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .benefit-content p {
            color: #6c757d;
            line-height: 1.7;
            font-size: 1rem;
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

            .benefit-card {
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
                            <li class="list">{{ __('Client Benefits') }}</li>
                        </ul>
                        <h2 class="banner-inner-title">{{ __('Client Benefits') }}</h2>
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
                    <h1 class="animate-fade-in">{{ $benefit->heading ?? $benefit->title }}</h1>
                    @if(!empty($benefit->short_description))
                        <p class="animate-fade-in">{{ $benefit->short_description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- ================= Benefits Section ================= -->
    @if(!empty($benefit->benefits) && is_array($benefit->benefits))
        <section class="benefits-section">
            <div class="container">
                <div class="section-title">
                    <h2>Key Benefits for Clients</h2>
                    <p>Discover why clients choose Right Freelancer LLC for their projects</p>
                </div>

                <div class="row">
                    @foreach($benefit->benefits as $index => $benefit_item)
                        <div class="col-lg-6 col-md-6 mb-4">
                            <div class="benefit-card animate-fade-in" style="animation-delay: {{ $index * 0.1 }}s">
                                <h3>
                                    <i class="fas {{ $benefit_item['icon'] ?? 'fa-check-circle' }}" style="color:#309400"></i>
                                    {{ $benefit_item['title'] ?? '' }}
                                </h3>
                                <p>{{ $benefit_item['description'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ================= Dynamic Content Section ================= -->
    @if(!empty($benefit->content))
        <section class="benefits-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="benefit-content">
                            {!! $benefit->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- ================= FAQ Section ================= -->
    @if(!empty($benefit->faqs) && is_array($benefit->faqs))
        <section class="faq-section">
            <div class="container">
                <div class="section-title">
                    <h2>Frequently Asked Questions</h2>
                    <p>Get answers to common questions about our services</p>
                </div>

                <div class="faq-accordion">
                    <div class="accordion" id="faqAccordion">
                        @foreach($benefit->faqs as $index => $faq)
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

    <!-- ================= Legacy FAQ Section ================= -->
    @if(!empty($benefit->faq_content) && (empty($benefit->faqs) || !is_array($benefit->faqs)))
        <section class="faq-section">
            <div class="container">
                <div class="section-title">
                    <h2>Frequently Asked Questions</h2>
                    <p>Get answers to common questions about our services</p>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="benefit-content">
                            {!! $benefit->faq_content !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

@endsection