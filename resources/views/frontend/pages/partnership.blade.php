@php use plugins\PageBuilder\PageBuilderSetup; @endphp
@extends('frontend.layout.master')

@section('site_title', $partnership->title ?? __('Partnership'))
@section('meta_title')
    {{ $partnership->meta_title ?? __('Partnership - Right Freelancer | Build Strong Collaborations') }}
@endsection

@section('meta_description')
    {{ $partnership->meta_description ?? __('Explore partnership opportunities with Right Freelancer. Join hands to grow together through trust, support, and innovation.') }}
@endsection

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

    .opportunities-section {
        border: 2px solid #309400;
        border-radius: 12px;
    }

    .circle-number {
        width: 36px;
        height: 36px;
        background-color: #309400;
        color: white;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
</style>

@section('content')
    <!-- Breadcrumb -->
    <div class="banner-inner-area border-top pat-20 pab-20">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="banner-inner-contents">
                        <ul class="inner-menu">
                            <li class="list"><a href="{{ url('/') }}">Home</a></li>
                            <li class="list">{{ $partnership->title ?? 'Partnership' }}</li>
                        </ul>
                        <h2 class="banner-inner-title">{{ $partnership->title ?? 'Partnership' }}</h2>
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
                                    <h2 class="escrow-title text-start">
                                        {{ $partnership->escrow_title ?? 'Building Success Together Partnership With Us' }}
                                    </h2>
                                    <p class="text-start" style="font-size: 16px; color: #fff;">
                                        {{ $partnership->escrow_description ?? "At Right Freelancer, That's a great approach! Partnerships can indeed be a powerful way to drive growth, innovation, and success. By collaborating with industry leaders, educational institutions, technology partners, and corporate entities, Right Freelancer can Expand its reach and offerings, Foster innovation and stay ahead of industry trends, Develop new skills and talent pipelines, Enhance its ecosystem and services, Build strong relationships and networks" }}
                                    </p>
                                </div>
                                <!-- Right 4 columns: Image -->
                                <div class="col-lg-4 text-center">
                                    <div class="img-fluid w-100">
                                        @if (!empty($partnership->escrow_image))
                                            <img src="{{ asset($partnership->escrow_image) }}" alt="Partnership"
                                                style="height:100%; border-radius:10px">
                                        @else
                                            <img src="{{ asset('assets/uploads/partnerimage/partnerwithus.png') }}"
                                                alt="Partnership" style="height:100%; border-radius:10px">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="centered-section py-5 about-area section-bg-2 pat-100 pab-100" data-padding-top="100"
        data-padding-bottom="100" style="background-color:#F5F5F5">

        <div class="container">
            <div class="row justify-content-center text-center mt-2">
                <div class="col-lg-12">
                    <h2 class="section-title mb-4">
                        {{ $partnership->why_partner_title ?? 'Why Partner with Right Freelancer?' }}
                    </h2>
                    @if(!empty($partnership->why_partner_description))
                        {!! $partnership->why_partner_description !!}
                    @else
                        <h4>Unlock Global Talent</h4>
                        <p class="mt-2">
                            Tap into a diverse pool of skilled professionals worldwide. Streamlined Project Management: Leverage
                            innovative tools for efficient project execution. Secure Payments: Enjoy protected payments and
                            financial security. Verified Freelancers, Collaborate with trusted vetted professionals. Growing
                            Network: Expand your reach and opportunities through our expanding network.
                        </p>
                        <h4>Benefits for Businesses Access to Expertis</h4>
                        <p class="mt-2">
                            Find specialized talent for specific projects, Increased Efficiency: Enhance productivity with
                            streamlined project management, Cost-Effective: Reduce costs with competitive pricing and secure
                            payments.
                        </p>
                        <h4 class="mt-2">Benefits for Freelancers Global Opportunities</h4>
                        <p class="mt-2">
                            Access a wide range of projects and clients, Secure Payments: Receive protected payments for work
                            completed,
                            Professional Growth: Develop skills and portfolio with diverse projects.
                        </p>
                        <h4 class="mt-2">Call-to-Action Get Started</h4>
                        <p class="mt-2">
                            Encourage businesses and freelancers to sign up, Provide additional information on partnership
                            benefits,
                            These ideas highlight the value proposition of partnering with Right Freelancer.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="row">
                <!-- Left Column: Text -->
                <div class="col-lg-6">
                    <h3>{{ $partnership->expand_talent_title ?? 'Expand Your Talent Pool' }}</h3>
                    @if(!empty($partnership->expand_talent_description))
                        {!! $partnership->expand_talent_description !!}
                    @else
                        <h5 class="mt-2">Find the Right Expertise:</h5>
                        <p class="mt-2">
                            Discover specialized talent for specific projects or industries, Scale
                            Your Business: Quickly access a pool of skilled freelancers to meet growing demands, Flexible Team
                            Solutions: Build a project team or find individual specialists to fit your needs.
                        </p>
                        <h5>Benefits of Right Freelancer's Talent Pool</h5>
                        <p class="mt-2">
                            Verified Professionals: Collaborate with trusted, vetted freelancers, Diverse Skill Sets: Access a
                            wide range of skills and expertise, Time-Saving: Efficiently find and hire talent without extensive
                            recruitment processes.
                        </p>
                        <h5>Industries We Serve</h5>
                        <p class="mt-2">
                            Tech and Development: Find experts in software development, data science, and more. Creative
                            Services: Discover talent in design, writing, and multimedia production, Business and Consulting:
                            Access professionals with expertise in strategy, marketing, and finance.
                        </p>
                    @endif
                </div>
                <!-- Right Column: Image -->
                <div class="col-lg-6 text-center">
                    <div class="img-fluid w-100">
                        @if(!empty($partnership->expand_talent_image))
                            <img src="{{ asset($partnership->expand_talent_image) }}" alt="Expand Talent"
                                style="height:100%; border-radius:10px">
                        @else
                            <img src="{{ asset('assets/uploads/partnerimage/Expand-Your-Talent-Pool.jpg') }}"
                                alt="Expand Talent" style="height:100%; border-radius:10px">
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="row">
                <!-- Left Column: Image -->
                <div class="col-lg-6 text-center">
                    <div class="img-fluid w-100">
                        @if(!empty($partnership->foster_innovation_image))
                            <img src="{{ asset($partnership->foster_innovation_image) }}" alt="Foster Innovation"
                                style="height:100%; border-radius:10px">
                        @else
                            <img src="{{ asset('assets/uploads/partnerimage/Contribute-to-Economic-Empowerment.jpg') }}"
                                alt="Foster Innovation" style="height:100%; border-radius:10px">
                        @endif
                    </div>
                </div>
                <!-- Right Column: Text -->
                <div class="col-lg-6">
                    <h3>{{ $partnership->foster_innovation_title ?? 'Foster Innovation & Growth' }}</h3>
                    @if(!empty($partnership->foster_innovation_description))
                        {!! $partnership->foster_innovation_description !!}
                    @else
                        <h5 class="mt-2">Innovation Initiatives</h5>
                        <p class="mt-2">
                            Remote Work Solutions: Developing tools and strategies for effective remote collaboration, Project
                            Efficiency: Enhancing workflows and processes for smoother project execution, Seamless
                            Collaboration: Fostering stronger relationships between businesses and freelancers.
                        </p>
                        <h5>Benefits of Partnership</h5>
                        <p class="mt-2">
                            Shape the Future: Contribute to innovative solutions that benefit the freelance industry, Industry
                            Leadership: Stay ahead of the curve with cutting-edge solutions, Collaborative Growth: Partner with
                            us to drive growth and success for both businesses and freelancers.
                        </p>
                        <h5>Call to Action</h5>
                        <p class="mt-2">
                            Join the Movement: Partner with us to shape the future of freelance work, Explore Opportunities:
                            Learn more about our innovation initiatives and partnership opportunities.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="row">
                <!-- Left Column: Text -->
                <div class="col-lg-6">
                    <h3 class="mt-2">{{ $partnership->market_presence_title ?? 'Strengthen Your Market Presence' }}</h3>
                    @if(!empty($partnership->market_presence_description))
                        {!! $partnership->market_presence_description !!}
                    @else
                        <h5 class="mt-2">Partner Benefits</h5>
                        <p class="mt-2">
                            - Boost Visibility: Enhance your brand's presence in freelance and outsourcing markets.
                        </p>
                        <p class="mt-2">
                            - Generate Leads: Leverage our platform for high-quality leads.
                        </p>
                        <p class="mt-2">
                            - Showcase Brand: Reach a broad audience of professionals and businesses.
                        </p>
                        <h4>Grow with Us</h4>
                        <p class="mt-2">
                            - Drive Business Growth: Collaborate for success.
                        </p>
                        <p class="mt-2">
                            - Expand Market Reach: Establish your brand as an industry leader.
                        </p>
                    @endif
                </div>
                <!-- Right Column: Image -->
                <div class="col-lg-6 text-center">
                    <div class="img-fluid w-100">
                        @if(!empty($partnership->market_presence_image))
                            <img src="{{ asset($partnership->market_presence_image) }}" alt="Market Presence"
                                style="height:100%; border-radius:10px">
                        @else
                            <img src="{{ asset('assets/uploads/partnerimage/Foster-Innovation-&-Growth.jpg') }}"
                                alt="Market Presence" style="height:100%; border-radius:10px">
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="row">
                <!-- Left Column: Image -->
                <div class="col-lg-6 text-center">
                    <div class="img-fluid w-100">
                        @if(!empty($partnership->economic_empowerment_image))
                            <img src="{{ asset($partnership->economic_empowerment_image) }}" alt="Economic Empowerment"
                                style="height:100%; border-radius:10px">
                        @else
                            <img src="{{ asset('assets/uploads/partnerimage/Strengthen-Your-Market-Presence.jpg') }}"
                                alt="Economic Empowerment" style="height:100%; border-radius:10px">
                        @endif
                    </div>
                </div>
                <!-- Right Column: Text -->
                <div class="col-lg-6">
                    <h5 class="mt-2">Contribute to Economic Empowerment</h5>
                    @if(!empty($partnership->economic_empowerment_description))
                        {!! $partnership->economic_empowerment_description !!}
                    @else
                        <p class="mt-2">
                            When you partner with us, you’re supporting not just business growth but also social impact. By
                            providing more opportunities for skilled professionals in all over the world, you help drive
                            economic development and promote financial independence within the community.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="row justify-content-center text-center mt-2">
                <div class="col-lg-12">
                    <h2 class="section-title mb-4">Partnership Opportunities</h2>
                    <p class="mt-2">
                        "Explore ways to collaborate for mutual growth. Working together brings shared benefits and new
                        possibilities."
                    </p>
                </div>
            </div>

            <div class="row mt-5 g-4">
                @if(!empty($partnership->opportunities) && is_array($partnership->opportunities))
                    @foreach($partnership->opportunities as $opt)
                        <div class="col-md-6 col-lg-3">
                            <div class="p-4 h-100 bg-light shadow-sm opportunities-section"
                                style="border: 2px solid #309400; border-radius: 12px;">
                                <div class="d-flex align-items-start">
                                    @if(!empty($opt['icon']))
                                        <div class="me-3 flex-shrink-0">
                                            <img src="{{ asset($opt['icon']) }}" alt="Icon" width="40">
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-2">{{ $opt['title'] ?? '' }}</h6>
                                    </div>
                                </div>
                                <p class="mb-0 text-start small mt-2">
                                    {{ $opt['description'] ?? '' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-md-6 col-lg-3">
                        <div class="p-4 h-100 bg-light shadow-sm opportunities-section"
                            style="border: 2px solid #309400; border-radius: 12px;">
                            <div class="d-flex align-items-start">
                                <!-- Image on the left -->
                                <div class="me-3 flex-shrink-0">
                                    <img src="{{ asset('assets/uploads/partnerimage/Strategic-Business-Partnerships.png') }}"
                                        alt="Handshake Icon" width="40">
                                </div>
                                <!-- Text content on the right -->
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-2">Strategic Business Partnerships</h6>
                                </div>
                            </div>
                            <p class="mb-0 text-start small mt-2">
                                It sounds like you're looking to position your business as a partner for companies aiming to
                                expand their service offerings or tap into a pool of high-quality freelancers. This kind of
                                strategic partnership could be very valuable, especially if you're offering a platform with
                                robust tools or services that companies can integrate into their existing operations.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="p-4 h-100 bg-light shadow-sm opportunities-section"
                            style="border: 2px solid #309400; border-radius: 12px;">
                            <div class="d-flex align-items-start">
                                <div class="me-3 flex-shrink-0">
                                    <img src="{{ asset('assets/uploads/partnerimage/Educational-&-Training-Partnerships2.png') }}"
                                        alt="Training Icon" width="40">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-2">Educational & Training Partnerships</h6>
                                </div>
                            </div>
                            <p class="mb-0 text-start small mt-2">
                                We partner with educational institutions and training providers to equip freelancers with
                                essential skills, helping bridge the skills gap for the digital economy.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="p-4 h-100 bg-light shadow-sm opportunities-section"
                            style="border: 2px solid #309400; border-radius: 12px;">
                            <div class="d-flex align-items-start">
                                <div class="me-3 flex-shrink-0">
                                    <img src="{{ asset('assets/uploads/partnerimage/Technology-&-Integration-Partnerships.png') }}"
                                        alt="Tech Icon" width="40">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-2">Technology & Integration Partnerships</h6>
                                </div>
                            </div>
                            <p class="mb-0 text-start small mt-2">
                                Perfect for companies wanting to enhance their services or tap into a pool of skilled
                                freelancers. Partner with us to offer value-added services, utilize our platform’s features, and
                                explore joint ventures.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="p-4 h-100 bg-light shadow-sm opportunities-section"
                            style="border: 2px solid #309400; border-radius: 12px;">
                            <div class="d-flex align-items-start">
                                <div class="me-3 flex-shrink-0">
                                    <img src="{{ asset('assets/uploads/partnerimage/Corporate-&-Enterprise-Partnerships2.png') }}"
                                        alt="Enterprise Icon" width="40">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-2">Corporate & Enterprise Partnerships</h6>
                                </div>
                            </div>
                            <p class="mb-0 text-start small mt-2">
                                Tailored for large enterprises seeking flexible staffing and specialized freelance talent, our
                                partnerships provide a seamless, scalable solution for managing remote teams and handling
                                complex projects.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="container mt-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4">Our Partnership Process</h2>
                </div>
                <div class="col-lg-6">
                    <p>
                        We keep our process simple, cooperative, and geared towards mutual success. Here's how it works:
                    </p>
                </div>
            </div>

            <div class="row mt-5 g-4">
                @if(!empty($partnership->process) && is_array($partnership->process))
                    @foreach($partnership->process as $index => $proc)
                        <div class="col-md-6 col-lg-3">
                            <div class="mb-3">
                                <div class="circle-number">{{ $index + 1 }}</div>
                            </div>
                            <h6 class="fw-bold" style="color: #309400;">{{ $proc['title'] ?? '' }}</h6>
                            <p class="small">
                                {{ $proc['description'] ?? '' }}
                            </p>
                        </div>
                    @endforeach
                @else
                    <div class="col-md-6 col-lg-3">
                        <div class="mb-3">
                            <div class="circle-number">1</div>
                        </div>
                        <h6 class="fw-bold" style="color: #309400;">Initial Consultation & Alignment</h6>
                        <p class="small">
                            We begin by understanding your goals, values, and expectations. Our team conducts a detailed needs
                            analysis to uncover synergies and opportunities for collaboration.
                        </p>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="circle-number  mb-3">
                            <div class="circle-number">2</div>
                        </div>
                        <h6 class="fw-bold" style="color: #309400;">Customized Partnership Proposal</h6>
                        <p class="small">
                            We’ll craft a tailored proposal based on your goals, detailing the partnership's scope, benefits,
                            and structure
                        </p>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="mb-3">
                            <div class="circle-number">3</div>
                        </div>
                        <h6 class="fw-bold" style="color: #309400;">Implementation & Launch</h6>
                        <p class="small">
                            After finalizing the proposal, we move to implementation. Our dedicated team collaborates with you
                            to establish the partnership smoothly.
                        </p>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="mb-3">
                            <div class="circle-number">4</div>
                        </div>
                        <h6 class="fw-bold" style="color: #309400;">Ongoing Support & Optimization</h6>
                        <p class="small">
                            Our support doesn’t end at launch. We offer continuous assistance and periodic performance
                            assessments to ensure long-term success.
                        </p>
                    </div>
                @endif
            </div>
        </div>
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-lg-12 text-center">
                    <h3 class="mb-3">Let’s shape the future of freelancing—together!</h3>
                    <p class="mb-2" style="font-size: 18px;">Ready to start a conversation?</p>
                    <p class="mb-3" style="font-size: 16px;">Contact our partnerships team today to explore how we can
                        create success together.</p>
                    <div class="mb-2">
                        <a href="mailto:{{ $partnership->contact_email ?? 'partnerships@rightfreelancer.com' }}"
                            style="font-size: 18px; color: #309400; font-weight: 600; text-decoration: underline;">{{
                            $partnership->contact_email ?? 'partnerships@rightfreelancer.com' }}</a>
                    </div>
                    <div>
                        <a href="tel:{{ $partnership->contact_phone ?? '+1 (406) 225-1210' }}"
                            style="font-size: 18px; color: #309400; font-weight: 600; text-decoration: underline;">{{ $partnership->contact_phone ?? '+1 (406) 225-1210' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection