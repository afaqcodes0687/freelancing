@php use plugins\PageBuilder\PageBuilderSetup; @endphp
@extends('frontend.layout.master')
@section('site_title', $aboutUs->meta_title ?? __('About Us'))
@section('meta_title', $aboutUs->meta_title ?? __('About Us - Right Freelancer | Global Freelancing Platform'))
@section('meta_description', $aboutUs->meta_description ?? __('Right Freelancer is a global freelancing platform connecting skilled professionals with businesses worldwide.'))

<style>
    .aboutTeam-item-thumb {
        width: 100%; height: 380px; overflow: hidden; border-radius: 12px; display: flex; align-items: center; justify-content: center;
    }
    .aboutTeam-item-thumb img { width: 100%; height: 100%; object-fit: cover; object-position: top; }
</style>

@section('content')
    @php
        $aboutUs = \App\Models\AboutUs::first();
    @endphp
    
    <div class="banner-inner-area border-top pat-20">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="banner-inner-contents">
                        <ul class="inner-menu">
                            <li class="list"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                            <li class="list">{{ __('About Us') }}</li>
                        </ul>
                        <h2 class="banner-inner-title">{{ __('About Us') }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($aboutUs)
        <!-- CEO Section -->
        @if($aboutUs->ceo_name)
            <section class="about-us-ceo-area" style="background:#f9fafb; padding:60px 0;">
                <div class="container">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-7">
                            <div class="about-us-content" style="background:#ffffff; padding:30px; border-left:6px solid #28a745; border-radius:12px; box-shadow:0 8px 25px rgba(0,0,0,0.08);">
                                <h2 style="font-size:32px; font-weight:700; margin-bottom:20px; color:#222;">{{ $aboutUs->main_title ?? __('About Us') }}</h2>
                                <p style="font-size:16px; line-height:1.8; color:#555; margin-bottom:0;">
                                    {!! $aboutUs->ceo_description !!}
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-5 text-center">
                            <div style="position:relative; display:inline-block;">
                                @if($aboutUs->ceo_image)
                                    <img src="{{ asset('assets/frontend/img/' . $aboutUs->ceo_image) }}" 
                                         alt="{{ $aboutUs->ceo_name }}" style="max-width:64%; height:auto; border-radius:18px; box-shadow:0 12px 35px rgba(0,0,0,0.15);">
                                @endif
                                <div style="position:absolute; bottom:20px; left:20px; background:rgba(255,255,255,0.95); padding:14px 22px; border-left:5px solid #28a745; border-radius:8px; text-align:left; box-shadow:0 6px 18px rgba(0,0,0,0.15);">
                                    <h4 style="margin:0; font-size:20px; font-weight:700; color:#222;">{{ $aboutUs->ceo_name }}</h4>
                                    <span style="font-size:14px; font-weight:500; color:#28a745;">{{ $aboutUs->ceo_title }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <!-- Main About Section -->
        @if($aboutUs->main_description)
            <section class="about-area section-bg-2 pat-100 pab-100" style="background-color:#F5F5F5">
                <div class="container">
                    <div class="row g-4 justify-content-between">
                        <div class="col-xxl-8 col-lg-8">
                            <div class="about-wrapper-left">
                                <div class="section-title text-left">
                                    <h2 class="title">{{ $aboutUs->main_title ?? __('Great Opportunity For Rising Freelancers') }}</h2>
                                    <p class="section-para">{!! $aboutUs->main_description !!}</p>
                                    @if($aboutUs->opportunity_text)
                                        <p>{!! $aboutUs->opportunity_text !!}</p>
                                    @endif
                                </div>
                                @if($aboutUs->clients_count || $aboutUs->freelancers_count || $aboutUs->orders_count)
                                    <div class="about-counter mt-5">
                                        @if($aboutUs->clients_count)
                                            <div class="about-counter-item">
                                                <h3 class="about-counter-item-title">{{ $aboutUs->clients_count }}</h3>
                                                <p class="about-counter-item-para">{{ __('Clients working with us') }}</p>
                                            </div>
                                        @endif
                                        @if($aboutUs->freelancers_count)
                                            <div class="about-counter-item">
                                                <h3 class="about-counter-item-title">{{ $aboutUs->freelancers_count }}</h3>
                                                <p class="about-counter-item-para">{{ __('Freelancers working with us') }}</p>
                                            </div>
                                        @endif
                                        @if($aboutUs->orders_count)
                                            <div class="about-counter-item">
                                                <h3 class="about-counter-item-title">{{ $aboutUs->orders_count }}</h3>
                                                <p class="about-counter-item-para">{{ __('Orders processed') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if($aboutUs->video_url)
                            <div class="col-xxl-4 col-lg-4">
                                <div class="about-wrapper-right">
                                    <div class="about-wrapper-thumb">
                                        <div class="about-wrapper-thumb-item position-relative" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#aboutVideoModal" data-video="{{ $aboutUs->video_url }}">
                                            <img src="{{ $aboutUs->video_thumbnail ? asset('assets/frontend/img/' . $aboutUs->video_thumbnail) : asset('assets/frontend/img/rightfreelancer_tutorial.png') }}" alt="" class="img-fluid" style="display:block; margin:auto; border-radius:10px;" />
                                            <span class="aboutWhat-wrapper-icon about-video video_play" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:48px;color:white;background:rgba(0,0,0,0.5);border-radius:50%;padding:20px;">
                                                <i class="fa-solid fa-play"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        <!-- What We Do Section -->
        @if($aboutUs->what_we_do_title)
            <section class="aboutWhat-area pat-100 pab-50" style="background-color:rgb(255, 255, 255)">
                <div class="container">
                    <div class="section-title text-center">
                        <h2 class="title">{{ $aboutUs->what_we_do_title }}</h2>
                        @if($aboutUs->what_we_do_description)
                            <p class="section-para">{!! $aboutUs->what_we_do_description !!}</p>
                        @endif
                    </div>
                    @if($aboutUs->video_url)
                        <div class="row g-4 mt-4">
                            <div class="col-lg-12 d-flex justify-content-center">
                                <div class="aboutWhat-wrapper">
                                    <div class="aboutWhat-wrapper-thumb position-relative" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#aboutVideoModal" data-video="{{ $aboutUs->video_url }}">
                                        <img src="{{ $aboutUs->video_thumbnail ? asset('assets/frontend/img/' . $aboutUs->video_thumbnail) : asset('assets/frontend/img/rightfreelancer_tutorial.png') }}" alt="" class="img-fluid" style="display:block; margin:auto;" />
                                        <span class="aboutWhat-wrapper-icon about-video video_play" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:48px;color:white;background:rgba(0,0,0,0.5);border-radius:50%;padding:20px;">
                                            <i class="fa-solid fa-play"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        <!-- Statistics Section -->
        @if($aboutUs->jobs_handled || $aboutUs->earned_amount || $aboutUs->awards_count)
            <div class="credit-area">
                <div class="container">
                    <div class="credit-wrapper border-bottom pat-50 pab-100" style="background-color:;">
                        <div class="row g-4">
                            @if($aboutUs->jobs_handled)
                                <div class="col-lg-4 col-sm-6">
                                    <div class="credit-item text-center">
                                        <h3 class="credit-item-title">
                                            <span class="credit-item-title-heading">{{ $aboutUs->jobs_handled }}</span>
                                        </h3>
                                        <p class="credit-item-para">{{ __('Jobs we have handled in our Right Freelancer platform') }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($aboutUs->earned_amount)
                                <div class="col-lg-4 col-sm-6">
                                    <div class="credit-item text-center">
                                        <h3 class="credit-item-title">
                                            <span class="credit-item-title-heading">{{ $aboutUs->earned_amount }}</span>
                                        </h3>
                                        <p class="credit-item-para">{{ __('Earned by Freelancers in our platform till date') }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($aboutUs->awards_count)
                                <div class="col-lg-4 col-sm-6">
                                    <div class="credit-item text-center">
                                        <h3 class="credit-item-title">
                                            <span class="credit-item-title-heading">{{ $aboutUs->awards_count }}</span>
                                        </h3>
                                        <p class="credit-item-para">{{ __('Awards received in IT for excellence in service') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        
    @php
        $testimonialWidget = \App\Models\PageBuilder::where('addon_name', 'TestimonialOne')->first();
    @endphp

    <!-- Testimonial area starts -->
    {!! PageBuilderSetup::render_widgets_by_name_for_frontend(PageBuilderSetup::getWidgetArgs($testimonialWidget)) !!}
        <!-- Team Section -->
        @if($aboutUs->team_members && count(json_decode($aboutUs->team_members, true)) > 0)
            <section class="aboutTeam-area pat-100 pab-100" style="background-color:;">
                <div class="container">
                    <div class="section-title text-left append-flex">
                        <h2 class="title">{{ $aboutUs->team_title ?? __('Meet our hardworking team') }}</h2>
                        <div class="append-team"></div>
                    </div>
                    <div class="row g-4 mt-4">
                        <div class="col-lg-12">
                            <div class="global-slick-init attraction-slider nav-style-one slider-inner-margin" data-rtl="" data-appendArrows=".append-team" data-arrows="true" data-infinite="true" data-dots="false" data-slidesToShow="4" data-swipeToSlide="true" data-autoplay="false" data-autoplaySpeed="2500" data-prevArrow='<div class="prev-icon"><i class="fa-solid fa-arrow-left"></i></div>' data-nextArrow='<div class="next-icon"><i class="fa-solid fa-arrow-right"></i></div>' data-responsive='[{"breakpoint": 1400,"settings": {"slidesToShow": 4}},{"breakpoint": 1200,"settings": {"slidesToShow": 3}},{"breakpoint": 992,"settings": {"slidesToShow": 2}},{"breakpoint": 768,"settings": {"slidesToShow": 2}},{"breakpoint": 576, "settings": {"slidesToShow": 1} }]'>
                                @foreach(json_decode($aboutUs->team_members, true) as $member)
                                    <div class="slider-item">
                                        <div class="aboutTeam-item">
                                            <div class="aboutTeam-item-thumb">
                                                @if($member['image'])
                                                    <img src="{{ asset('assets/frontend/img/' . $member['image']) }}" alt="{{ $member['name'] }}" />
                                                @else
                                                    <img src="{{ asset('assets/frontend/img/author.jpg') }}" alt="{{ $member['name'] }}" />
                                                @endif
                                            </div>
                                            <div class="aboutTeam-item-contents mt-3">
                                                <h6 class="aboutTeam-item-title">{{ $member['name'] }}</h6>
                                                <p class="aboutTeam-item-para">{{ $member['position'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <!-- Certifications Section -->
        @if($aboutUs->certifications && count(json_decode($aboutUs->certifications, true)) > 0)
            <section class="certifications-area pat-100 pab-100" data-padding-top="100" data-padding-bottom="100" style="background-color:#F7FAF9;">
                <div class="container">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-6 col-md-12">
                            <div class="section-title text-left">
                                <h2 class="title">{{ $aboutUs->certifications_title ?? __('Certifications') }}</h2>
                                @if($aboutUs->certifications_description)
                                    <p class="section-para mt-3">{!! $aboutUs->certifications_description !!}</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="row justify-content-center">
                                @foreach(json_decode($aboutUs->certifications, true) as $cert)
                                    <div class="col-3 text-center mb-4">
                                        @if($cert['link'])
                                            <a href="{{ $cert['link'] }}" target="_blank">
                                                @if($cert['image'])
                                                    <img src="{{ asset('assets/frontend/img/' . $cert['image']) }}" alt="{{ $cert['title'] }}" class="img-fluid" style="max-width:120px; border-radius:50%; background:#fff; box-shadow:0 0 10px rgba(0,0,0,0.05);">
                                                @else
                                                    <span class="d-inline-block text-center" style="width:120px; height:120px; line-height:120px; border-radius:50%; background:#fff; box-shadow:0 0 10px rgba(0,0,0,0.05);">{{ $cert['title'] }}</span>
                                                @endif
                                            </a>
                                        @else
                                            @if($cert['image'])
                                                <img src="{{ asset('assets/frontend/img/' . $cert['image']) }}" alt="{{ $cert['title'] }}" class="img-fluid" style="max-width:120px; border-radius:50%; background:#fff; box-shadow:0 0 10px rgba(0,0,0,0.05);">
                                            @else
                                                <span class="d-inline-block text-center" style="width:120px; height:120px; line-height:120px; border-radius:50%; background:#fff; box-shadow:0 0 10px rgba(0,0,0,0.05);">{{ $cert['title'] }}</span>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <!-- Video Modal -->
        @if($aboutUs->video_url)
            <div class="modal fade" id="aboutVideoModal" tabindex="-1" aria-labelledby="aboutVideoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content bg-transparent border-0 shadow-none">
                        <div class="modal-header border-0 p-2" style="justify-content: flex-end; position: absolute; right: 0; top: 0; z-index: 10;">
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1); background:rgba(0,0,0,0.5); border-radius:50%; padding:10px;"></button>
                        </div>
                        <div class="modal-body p-0" style="display:flex; align-items:center; justify-content:center; min-height:60vh;">
                            <div class="ratio ratio-16x9 w-100">
                                <iframe id="aboutVideoIframe" src="" title="YouTube video" allowfullscreen style="border-radius:12px;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- Fallback content if no data exists -->
        <div class="container py-5">
            <div class="text-center">
                <h2>{{ __('About Us') }}</h2>
                <p>{{ __('About Us content is being updated. Please check back soon.') }}</p>
            </div>
        </div>
    @endif
@endsection

@if(isset($aboutUs) && $aboutUs->video_url)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var videoModal = document.getElementById('aboutVideoModal');
        var videoIframe = document.getElementById('aboutVideoIframe');
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
@endif
