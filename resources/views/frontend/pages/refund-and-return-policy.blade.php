@php use plugins\PageBuilder\PageBuilderSetup; @endphp
@extends('frontend.layout.master')

@section('site_title', $policy->meta_title ?? $policy->title ?? __('Refund or Return Policy'))

@section('meta_title')
    {{ $policy->meta_title ?? $policy->title ?? __('Refund or Return Policy - Right Freelancer') }}
@endsection

@section('meta_description')
    {{ $policy->meta_description ?? '' }}
@endsection

@section('content')

    <style>
        .benefit-hero {
            background: linear-gradient(135deg, #309400 0%, #206400 100%);
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

        .faq-section {
            padding: 40px 0;
            background: white;
        }

        .faq-accordion {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-accordion .accordion-item {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-accordion .accordion-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .faq-accordion .accordion-button {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 20px 25px;
            border: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .faq-accordion .accordion-button:not(.collapsed) {
            background: #309400;
            color: white;
            box-shadow: none;
        }

        .faq-accordion .accordion-button::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23309400'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
            transition: transform 0.2s ease-in-out;
        }

        .faq-accordion .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }

        .faq-accordion .accordion-body {
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
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .benefit-content h2 {
            color: #2c3e50;
            font-size: 1.8rem;
            font-weight: 700;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .benefit-content h3 {
            color: #2c3e50;
            font-size: 1.4rem;
            font-weight: 600;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .benefit-content p {
            color: #6c757d;
            line-height: 1.8;
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

        .policy-badge {
            background-color: rgba(48, 148, 0, 0.1);
            color: #309400;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }

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
                            <li class="list">{{ $policy->heading ?? $policy->title ?? __('Refund or Return Policy') }}</li>
                        </ul>
                        <h2 class="banner-inner-title">{{ $policy->heading ?? $policy->title ?? __('Refund or Return Policy') }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="benefit-hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1 class="animate-fade-in">{{ $policy->heading ?? $policy->title ?? __('Refund or Return Policy') }}</h1>
                    @if(!empty($policy->short_description))
                        <p class="animate-fade-in">{{ $policy->short_description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="benefits-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="benefit-content">
                        @if(!empty($policy->content))
                            {!! $policy->content !!}
                        @else
                            <p class="text-center py-5">Refund or Return Policy content is currently empty. Please add content from the admin panel.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(!empty($policy->faqs) && is_array($policy->faqs) && count($policy->faqs))
        <section class="faq-section">
            <div class="container">
                <div class="section-title">
                    <h2>{{ $policy->faq_content ?? __('Frequently Asked Questions') }}</h2>
                    <p>{{ __('Frequently asked questions regarding refunds, returns, and dispute management on our platform.') }}</p>
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
