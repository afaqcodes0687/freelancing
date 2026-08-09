@php use plugins\PageBuilder\PageBuilderSetup; @endphp
@extends('frontend.layout.master')
@section('site_title', $aboutUs->meta_title ?? __('About Us'))
@section('meta_title', $aboutUs->meta_title ?? __('About Us - Right Freelancer | Global Freelancing Platform'))
@section('meta_description', $aboutUs->meta_description ?? __('Right Freelancer is a global freelancing platform connecting skilled professionals with businesses worldwide. Discover how we help freelancers grow and succeed.'))

@section('content')
    <div class="banner-inner-area border-top pat-20">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="banner-inner-contents">
                        <ul class="inner-menu">
                            <li class="list"><a href="https://www.rightfreelancer.com/">Home </a></li>
                            <li class="list"> About Us </li>
                        </ul>
                        <h2 class="banner-inner-title"> About Us </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fazal Miran Section Starts -->
    <section class="about-us-ceo-area" style="background:#f9fafb; padding:60px 0;">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="about-us-content" style="background:#ffffff; padding:30px; border-left:6px solid #28a745;
                        border-radius:12px; box-shadow:0 8px 25px rgba(0,0,0,0.08);">

                        <h2 style="font-size:32px; font-weight:700; margin-bottom:20px; color:#222;">About Us</h2>
                        <p style="font-size:16px; line-height:1.8; color:#555; margin-bottom:0;">
                            Fazal Miran is president and CEO of Right Freelancer, the world’s human and  IT Service Management for the Provision and Support of an Online Employment (Freelance) Platform., where Fortune 3 companies through small businesses and highly skilled independent professionals from 190 countries connect and work together.
                            <br><br>
                            Fazal Miran joined Right Freelancer in 2019 as a product manager and held roles running Right Freelancer’s marketplace business, as chief product officer, and as chief marketing officer before being named CEO in January 2026.
                        </p>
                    </div>
                </div>

                <div class="col-lg-5 text-center">
                    <div style="position:relative; display:inline-block;">

                        <img src="{{ asset('assets/frontend/img/photofazalmiran.png') }}"
                            alt="Fazal Miran - President & CEO" style="max-width:64%; height:auto; border-radius:18px; box-shadow:0 12px 35px rgba(0,0,0,0.15);">

                          <div style="position:absolute; bottom:20px; left:20px; background:rgba(255,255,255,0.95);
                                padding:14px 22px; border-left:5px solid #28a745; border-radius:8px;
                                text-align:left; box-shadow:0 6px 18px rgba(0,0,0,0.15);">

                        <h4 style="margin:0; font-size:20px; font-weight:700; color:#222;">Fazal Miran</h4>
                        <span style="font-size:14px; font-weight:500; color:#28a745;">President & Chief Executive Officer</span>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Fazal Miran Section Ends -->
    <!-- About area starts -->
    <section class="about-area section-bg-2 pat-100 pab-100" data-padding-top="100" data-padding-bottom="100"
        style="background-color:#F5F5F5">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-xxl-8 col-lg-8">
                    <div class="about-wrapper-left">
                        <div class="section-title text-left">
                            <h2 class="title">Great Opportunity For Rising Freelancers</h2>
                            <p class="section-para">
                            <p style="text-align:left;"><span style="font-weight:normal;">Start Today For a Great
                                    Future</span></p>

                            <p style="text-align:left;"><span style="font-weight:normal;">Right Freelancer is one of the
                                    talent outsourcing companies that allows the talent from all over the world to showcase
                                    their skills, expertise and portfolios at mere platform. <span><br></span></span></p>

                            <p style="text-align:left;"><span style="font-weight:normal;">In today’s digital era, where
                                    there is too much competition in job hiring, there are so many other opportunities that
                                    we are unaware of. Be it Graphic Designers, Business Analysts, Content Marketers, Seo
                                    Experts, Web Designers, Web Developers, Sales Copywriters or Digital Marketers from
                                    anywhere across the globe can join Right Freelancer for free, get hired and work from
                                    the comfort of their home. We are the helping hand to those proficient freelancers who
                                    are intelligent enough to work independently, collaboratively or as an
                                    agency.</span><br></p>

                            </p>
                            <p style="text-align:left;"><span style="font-weight:normal;">
                                    From the last 5 years, we have introduced over freshers and highly experienced 57826
                                    freelancers to the employers and different agencies irrespective of race, social status,
                                    religion and ethnicity. Our talent pool of freelancers will take your business to the
                                    next level with their orthodox flair.
                            </p>


                        </div>
                        <div class="about-counter mt-5">
                            <div class="about-counter-item">
                                <h3 class="about-counter-item-title">
                                    <!-- <span class="about-counter-item-title-heading">1K</span> -->
                                </h3>
                                <p class="about-counter-item-para">Clients working with us</p>
                            </div>
                            <div class="about-counter-item">
                                <h3 class="about-counter-item-title">
                                    <!-- <span class="about-counter-item-title-heading">2K</span> -->
                                </h3>
                                <p class="about-counter-item-para">Freelancers working with us</p>
                            </div>
                            <div class="about-counter-item">
                                <h3 class="about-counter-item-title">
                                    <!-- <span class="about-counter-item-title-heading">2K</span> -->
                                </h3>
                                <p class="about-counter-item-para">Orders processed</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-lg-4">
                    <div class="about-wrapper-right">
                        <div class="about-wrapper-thumb">
                            <div class="about-wrapper-thumb-item position-relative" style="cursor:pointer;"
                                data-bs-toggle="modal" data-bs-target="#aboutVideoModal"
                                data-video="https://www.youtube.com/embed/3XUTzdMLVqg?autoplay=1">
                                <img src="https://www.rightfreelancer.com/assets/frontend/img/rightfreelancer_tutorial.png"
                                    alt="" class="img-fluid" style="display:block; margin:auto; border-radius:10px;" />
                                <span class="aboutWhat-wrapper-icon about-video video_play"
                                    style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:48px;color:white;background:rgba(0,0,0,0.5);border-radius:50%;padding:20px;">
                                    <i class="fa-solid fa-play"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About area end --><!-- About What area starts -->
    <section class="aboutWhat-area pat-100 pab-50" data-padding-top="100" data-padding-bottom="50"
        style="background-color:rgb(255, 255, 255)">
        <div class="container">
            <div class="section-title text-center">
                <h2 class="title">What we do?</h2>
                <p class="section-para">Right Freelancer is your seamless gateway to connect clients with exceptional
                    freelancers. We curate a diverse talent pool, streamline collaboration with efficient tools.</p>
            </div>
            <div class="row g-4 mt-4">
                <div class="col-lg-12 d-flex justify-content-center">
                    <div class="aboutWhat-wrapper">
                        <div class="aboutWhat-wrapper-thumb position-relative" style="cursor:pointer;"
                            data-bs-toggle="modal" data-bs-target="#aboutVideoModal"
                            data-video="https://www.youtube.com/embed/3XUTzdMLVqg?autoplay=1">
                            <img src="assets/frontend/img/rightfreelancer_tutorial.png" alt="" class="img-fluid"
                                style="display:block; margin:auto;" />
                            <span class="aboutWhat-wrapper-icon about-video video_play"
                                style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:48px;color:white;background:rgba(0,0,0,0.5);border-radius:50%;padding:20px;">
                                <i class="fa-solid fa-play"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About What area end -->

    <div class="credit-area">
        <div class="container">
            <div class="credit-wrapper border-bottom pat-50 pab-100" data-padding-top="50" data-padding-bottom="100"
                style="background-color:">
                <div class="row g-4">
                    <div class="col-lg-4 col-sm-6">
                        <div class="credit-item text-center">
                            <h3 class="credit-item-title">
                                <span class="credit-item-title-heading">49K</span>
                            </h3>
                            <p class="credit-item-para"> Jobs we have handled in our Right Freelancer platform</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="credit-item text-center">
                            <h3 class="credit-item-title">
                                <span class="credit-item-title-heading">$50M</span>
                            </h3>
                            <p class="credit-item-para"> Earned by Freelancers in our platform till date</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="credit-item text-center">
                            <h3 class="credit-item-title">
                                <span class="credit-item-title-heading">09X</span>
                            </h3>
                            <p class="credit-item-para"> Awards received in IT for excellence in service</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $testimonialWidget = \App\Models\PageBuilder::where('addon_name', 'TestimonialOne')->first();
    @endphp

    <!-- Testimonial area starts -->
    {!! PageBuilderSetup::render_widgets_by_name_for_frontend(PageBuilderSetup::getWidgetArgs($testimonialWidget)) !!}
    <!-- Testimonial area starts -->

    <!-- About Team area starts -->
    <section class="aboutTeam-area pat-100 pab-100" data-padding-top="100" data-padding-bottom="100"
        style="background-color:">
        <div class="container">
            <div class="section-title text-left append-flex">
                <h2 class="title">Meet our hardworking team</h2>
                <div class="append-team"></div>
            </div>
            <div class="row g-4 mt-4">
                <div class="col-lg-12">
                    <div class="global-slick-init attraction-slider nav-style-one slider-inner-margin" data-rtl=""
                        data-appendArrows=".append-team" data-arrows="true" data-infinite="true" data-dots="false"
                        data-slidesToShow="4" data-swipeToSlide="true" data-autoplay="false" data-autoplaySpeed="2500"
                        data-prevArrow='<div class="prev-icon"><i class="fa-solid fa-arrow-left"></i></div>'
                        data-nextArrow='<div class="next-icon"><i class="fa-solid fa-arrow-right"></i></div>'
                        data-responsive='[{"breakpoint": 1400,"settings": {"slidesToShow": 4}},{"breakpoint": 1200,"settings": {"slidesToShow": 3}},{"breakpoint": 992,"settings": {"slidesToShow": 2}},{"breakpoint": 768,"settings": {"slidesToShow": 2}},{"breakpoint": 576, "settings": {"slidesToShow": 1} }]'>
                        <div class="slider-item">
                            <div class="aboutTeam-item">
                                <div class="aboutTeam-item-thumb">
                                    <img src="{{ asset('assets/frontend/img/photofazalmiran.png') }}" alt="" />
                                </div>
                                <div class="aboutTeam-item-contents mt-3">
                                    <h6 class="aboutTeam-item-title">Fazal Miran</h6>
                                    <p class="aboutTeam-item-para">President & Chief Executive Officer</p>
                                </div>
                            </div>
                        </div>
                        <div class="slider-item">
                            <div class="aboutTeam-item">
                                <div class="aboutTeam-item-thumb">
                                    <img src="{{ asset('assets/frontend/img/shahid.jpeg') }}" alt="" />
                                </div>
                                <div class="aboutTeam-item-contents mt-3">
                                    <h6 class="aboutTeam-item-title">Shahid Javed</h6>
                                    <p class="aboutTeam-item-para">Project Manager</p>
                                </div>
                            </div>
                        </div>
                        <div class="slider-item">
                            <div class="aboutTeam-item">
                                <div class="aboutTeam-item-thumb">
                                    <img src="{{ asset('assets/frontend/img/afaq.jpeg') }}" alt="" />
                                </div>
                                <div class="aboutTeam-item-contents mt-3">
                                    <h6 class="aboutTeam-item-title">Afaq Tahir</h6>
                                    <p class="aboutTeam-item-para">Sr. Software Engineer</p>
                                </div>
                            </div>
                        </div>
                        <div class="slider-item">
                            <div class="aboutTeam-item">
                                <div class="aboutTeam-item-thumb">
                                    <img src="{{ asset('assets/frontend/img/haris.jpeg') }}" alt="" />
                                </div>
                                <div class="aboutTeam-item-contents mt-3">
                                    <h6 class="aboutTeam-item-title">Muhammad Haris</h6>
                                    <p class="aboutTeam-item-para">Sr. Software Engineer</p>
                                </div>
                            </div>
                        </div>
                        <div class="slider-item">
                            <div class="aboutTeam-item">
                                <div class="aboutTeam-item-thumb">
                                    <img src="{{ asset('assets/frontend/img/adnan.jpeg') }}" alt="" />
                                </div>
                                <div class="aboutTeam-item-contents mt-3">
                                    <h6 class="aboutTeam-item-title">Adnan Ahmad</h6>
                                    <p class="aboutTeam-item-para">WordPress Developer</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Certifications Section -->
    <section class="certifications-area pat-100 pab-100" data-padding-top="100" data-padding-bottom="100"
        style="background-color:#F7FAF9;">
        <div class="container">
            <div class="row align-items-center g-5">
                <!-- Left side: heading + text -->
                <div class="col-lg-6 col-md-12">
                    <div class="section-title text-left">
                        <h2 class="title">Certifications</h2>
                        <p class="section-para mt-3">
                            We’re extremely delighted to be recognized for the great work we do in and out of the office.
                        </p>
                    </div>
                </div>

                <!-- Right side: certification images -->
                <div class="col-lg-6 col-md-12">
                    <div class="row justify-content-center">
                       <div class="col-3 text-center mb-4">
                            <a href="{{ route('iso-certificate') }}" target="_blank">
                                <img src="{{ asset('assets/frontend/img/iso-img.avif') }}" 
                                    alt="ISO Certification"
                                    class="img-fluid"
                                    style="max-width:120px; border-radius:50%; background:#fff; box-shadow:0 0 10px rgba(0,0,0,0.05);">
                            </a>
                        </div>

                       <div class="col-3 text-center mb-4">
                            <a href="https://www.ssl.com/" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('assets/frontend/img/images.jpg') }}" 
                                    alt="SSL Certification"
                                    class="img-fluid"
                                    style="max-width:120px; border-radius:50%; background:#fff; box-shadow:0 0 10px rgba(0,0,0,0.05);">
                            </a>
                        </div>

                        <div class="col-3 text-center mb-4">
                            <img src="{{ asset('assets/frontend/img/llc.jpg') }}" alt="LLC Certification"
                                class="img-fluid"
                                style="max-width:120px; border-radius:50%; background:#fff; box-shadow:0 0 10px rgba(0,0,0,0.05);">
                        </div>


                        <div class="col-3 text-center mb-4">
                            <a href="{{ route('secp-certificate') }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('assets/frontend/img/secp.png') }}" 
                                    alt="SECP Certification"
                                    class="img-fluid"
                                    style="max-width:120px; border-radius:50%; background:#fff; box-shadow:0 0 10px rgba(0,0,0,0.05);">
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Certifications Section -->

    <!-- About Team area end -->

    <!-- Only one Video Modal should exist in the file -->
    <div class="modal fade" id="aboutVideoModal" tabindex="-1" aria-labelledby="aboutVideoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="modal-header border-0 p-2"
                    style="justify-content: flex-end; position: absolute; right: 0; top: 0; z-index: 10;">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        style="filter: invert(1); background:rgba(0,0,0,0.5); border-radius:50%; padding:10px;"></button>
                </div>
                <div class="modal-body p-0"
                    style="display:flex; align-items:center; justify-content:center; min-height:60vh;">
                    <div class="ratio ratio-16x9 w-100">
                        <iframe id="aboutVideoIframe" src="" title="YouTube video" allowfullscreen allow="autoplay"
                            style="border-radius:12px;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var videoModal = document.getElementById('aboutVideoModal');
        var videoIframe = document.getElementById('aboutVideoIframe');
        // Use event delegation for multiple triggers
        document.body.addEventListener('click', function (e) {
            var trigger = e.target.closest('[data-bs-toggle="modal"][data-bs-target="#aboutVideoModal"][data-video]');
            if (trigger) {
                var videoUrl = trigger.getAttribute('data-video');
                videoIframe.src = videoUrl;
            }
        });
        if (videoModal) {
            videoModal.addEventListener('hidden.bs.modal', function () {
                videoIframe.src = '';
            });
        }
    });
</script>