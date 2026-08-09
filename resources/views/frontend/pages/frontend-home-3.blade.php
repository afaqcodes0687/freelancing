@php use App\Models\JobPost;
    use App\Models\Project;
    use App\Models\User;
    use Illuminate\Support\Facades\Cache;
    use
    Illuminate\Support\Facades\DB;
    use Modules\Service\Entities\Category;
    use
    plugins\PageBuilder\Addons\Category\CategoryProjectOne;
use plugins\PageBuilder\PageBuilderSetup; @endphp
@extends('frontend.layout.master')

@section('style')
    <link rel="preload" as="image" href="/assets/static/img/banner/rating-star.webp" type="image/webp" fetchpriority="high">
@endsection

@section('content')
  
    <!-- Banner area Starts -->
    <div class="banner-area banner-area-padding section-bg-1" data-padding-top="64" data-padding-bottom="59" style="">
        <div class="container">
            <div class="row gy-5 align-items-center flex-column-reverse flex-lg-row">
                <div class="col-lg-7">
                    <div class="banner-single">
                        <div class="banner-single-content">
                            <h1 class="banner-single-content-title"> Hire Right Freelancer <br />At Right Time For Any
                                Job Online</h1>
                            <p class="banner-single-content-para">

                                Connect to over 1M freelancer experts with Worldâ€™s Fastest and most Affordable Freelance
                                platform. </p>

                            <div class="btn-wrapper flex-btn mt-5">
                                <a href="{{ Auth::guard('web')->check() ? '/jobs' : route('user.login') }}" class="cmn-btn btn-bg-1"> Find Job </a>
                                <a href="{{ Auth::guard('web')->check() ? '/projects' : route('user.login') }}" class="cmn-btn btn-outline-1 color-one"> Find Project </a>
                            </div>
                            <div class="banner-single-content-logo mt-5">
                                <h5 class="banner-single-content-logo-title"> Trusted by: </h5>
                                <ul class="banner-single-content-logo-list list-style-none my-4">
                                    <!-- <li class="banner-single-content-logo-list-item">
                                        <span class="banner-single-content-logo-list-link">
                                            <img src="{{asset('assets/frontend/img/trust1.jpg')}}" alt="" />
                                        </span>
                                    </li>
                                    <li class="banner-single-content-logo-list-item">
                                        <span class="banner-single-content-logo-list-link">
                                            <img src="{{asset('assets/frontend/img/trust2.jpg')}}" alt="" />
                                        </span>
                                    </li>
                                    <li class="banner-single-content-logo-list-item">
                                        <span class="banner-single-content-logo-list-link">
                                            <img src="{{asset('assets/frontend/img/trust3.jpg')}}" alt="" />
                                        </span>
                                    </li>
                                    <li class="banner-single-content-logo-list-item">
                                        <span class="banner-single-content-logo-list-link">
                                            <img src="{{asset('assets/frontend/img/trust4.jpg')}}" alt="" />
                                        </span>
                                    </li> -->
                                    <!-- <li class="banner-single-content-logo-list-item">
                                        <span class="banner-single-content-logo-list-link">
                                            <img src="{{asset('assets/frontend/img/trust1.jpg')}}" alt="" />
                                        </span>
                                    </li> -->                                     <li class="banner-single-content-logo-list-item">
                                        <a href="https://www.ssl.com/" target="_blank" class="banner-single-content-logo-list-link">
                                            <img src="{{ asset('assets/frontend/img/ssl-green.webp') }}" alt="SSL Secured" style="max-height: 40px;" width="40" height="40" loading="lazy" decoding="async" />
                                        </a>
                                    </li>

                                     <li class="banner-single-content-logo-list-item">
                                        <a href="{{ route('iso-certificate') }}" target="_blank" class="banner-single-content-logo-list-link">
                                            <img src="{{ asset('assets/frontend/img/iso-blue.webp') }}" alt="ISO Secured" style="max-height: 40px;" width="40" height="40" loading="lazy" decoding="async" />
                                        </a>
                                    </li>

                                     <li class="banner-single-content-logo-list-item">
                                        <a href="{{ route('secp-certificate') }}" target="_blank" class="banner-single-content-logo-list-link">
                                            <img src="{{ asset('assets/frontend/img/secp.png') }}" alt="SEC Certificate" style="max-height: 40px;" width="40" height="40" loading="lazy" decoding="async" />
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="banner-right-content">
                        <div class="banner-right-content-thumb">
                            <img src="/assets/static/img/banner/rating-star.webp" alt="img" fetchpriority="high" width="800" height="600" style="content-visibility: visible;">
                        </div>
                        <div class="banner-right-content-shape">
                            <img src="/assets/img-shape117035905251703684257.svg" alt="" width="50" height="50" />
                            <img src="/assets/img-shape217035905251703684257.svg" alt="" width="50" height="50" />
                        </div>

                        <div class="banner-right-content-top">
                            <div class="banner-right-content-rating">
                                <div class="banner-right-content-rating-icon">
                                    <img src="/assets/static/img/banner/rating.svg" alt="rating" width="120" height="24">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Banner area end -->

    <!-- Choose area starts -->
    <section class="choose-area" data-padding-top="102" data-padding-bottom="75">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-6">
                    <div class="choose-contents">
                        <div class="section-title">
                            <div class="subtitle"><span> Why choose us? </span></div>
                            <h2 class="title"> We value each relationship on our platform</h2>
                            <p class="section-para">Our Freelancers aren&#039;t Bots; they&#039;re human beings with a
                                sense of humour within the bounds of their job. We believe in forming long-term
                                relationships with both our Talent &amp; our Clients.</p>
                        </div>
                        <ul class="choose-contents-list mt-4">
                            <li class="choose-contents-list-item">Less fees</li>
                            <li class="choose-contents-list-item">Live support</li>
                            <li class="choose-contents-list-item">No fees for client</li>
                            <li class="choose-contents-list-item">Verified users</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="choose-wrapper">
                        <div class="choose-wrapper-thumb-shapes">
                            <img src="/assets/choose_thumb_shape1717413870.svg" alt="" loading="lazy" decoding="async" />
                        </div>
                        <div class="choose-wrapper-thumb">
                            <img src="/assets/choose_thumb1717413871.png" alt="" loading="lazy" decoding="async" />
                        </div>
                        <div class="choose-wrapper-shapes">
                            <img src="/assets/choose_shapes1717413916.png" alt="" loading="lazy" decoding="async" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Choose area ends -->

    @php
        $projectPromotionWidget = Cache::remember('addon_project_promotion', 3600, function() {
            return \App\Models\PageBuilder::where('addon_name', 'ProjectPromotion')->first();
        });
        $promotionProfile = Cache::remember('addon_profile_promotion', 3600, function() {
            return \App\Models\PageBuilder::where('addon_name', 'ProfilePromotion')->first();
        });
    @endphp
    {!! plugins\PageBuilder\PageBuilderSetup::render_widgets_by_name_for_frontend(plugins\PageBuilder\PageBuilderSetup::getWidgetArgs($projectPromotionWidget)) !!}
    {!! plugins\PageBuilder\PageBuilderSetup::render_widgets_by_name_for_frontend(plugins\PageBuilder\PageBuilderSetup::getWidgetArgs($promotionProfile)) !!}


    <!-- How it Works  Clients area starts -->
    <section class="category-area pat-50 pab-50 section-bg-1" data-padding-top="50" data-padding-bottom="50" style="">
        <div class="container">
            <div class="section-title center-text">
                <h2 class="title"> How it Works For Clients </h2>
                <p>SPost the work you want as a job offer. Allow freelancers to apply and fulfill your work. Be
                    satisfied. Pay them and give them a review.</p>
            </div>
            <div class="row gy-4 mt-4">
                <div class="col-lg-12">
                    <div class="heading">
                        <div class="how-to-sec">
                            <div class="how-to"><span class="how-icon"><i class="la la-search-plus"></i></span>
                                <h3>Post a job</h3>
                                <p>Post a job and the required skills. We will help you match with the right talent in
                                    no time.</p>
                            </div>
                            <div class="how-to"><span class="how-icon"><i class="la la-user"></i></span>
                                <h3>Hire an Right Applicant</h3>
                                <p>Get instant access to the Right Freelancer Profiles, compare the proposals, chat in
                                    real time and award the job that suits best to your needs.
                                </p>
                            </div>
                            <div class="how-to"><span class="how-icon"><i class="la la-suitcase"></i></span>
                                <h3>Get the Work Done</h3>
                                <p>Share your digital assets in Right Freelancer secure environment and get the work
                                    done on the agreed time.</p>
                            </div>
                            <div class="how-to"><span class="how-icon"><i class="la la-cc-mastercard"></i></span>
                                <h3>Pay and Review</h3>
                                <p>Pay the Right Freelancer for the work you authorize and satisfied through easier
                                    Paypal, Matercard, or visa global payments system.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="browse-all-cat"><a href="{{route('client.job.create')}}" title="">Post Your Job FREE</a>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- How it Works For Clients area end -->


    @php
        $categoryProjectOneWidget = Cache::remember('addon_category_project_one', 3600, function() {
            return \App\Models\PageBuilder::where('addon_name', 'CategoryProjectOne')->first();
        });
        $CategoryJobOne = Cache::remember('addon_category_job_one', 3600, function() {
            return \App\Models\PageBuilder::where('addon_name', 'CategoryJobOne')->first();
        });
    @endphp
    {!! PageBuilderSetup::render_widgets_by_name_for_frontend(PageBuilderSetup::getWidgetArgs($categoryProjectOneWidget)) !!}

    <!-- Category area starts -->
    <section class="category-area pat-50 pab-50" data-padding-top="50" data-padding-bottom="50" style="background-color:#fbfbfb;">
        <div class="container">
            <div class="section-title center-text">
            <h2 class="title"> How Projects (GIGS) Work </h2>
            <p>Post your Projects (gigs) as a freelancer, make sure they are right for the Clients, and then sit back to let them buy them and leave you reviews.</p>
            </div>
            <div class="row gy-4 mt-4">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 print-block print-block-left lrmargin mt-3">
                            <div class="w-print-block frist">
                                <div class="print-icon"><i><img src="{{asset('assets/frontend/img/static/icon-1.jpg')}}" alt="rating" style="max-width: 100%; height: auto;" loading="lazy" decoding="async"></i></div>
                                <div class="print-number"><span>01</span></div>
                                <div class="print-txt">
                                    <p>Freelancer Post</p>
                                </div>
                                <div class="print-title"><a href="#">Projects(gigs) They Offer</a></div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 print-block print-block-center lrmargin mt-3">
                            <div class="w-print-block">
                                <div class="print-icon"><i><img src="{{asset('assets/frontend/img/static/icon-2.jpg')}}" alt="rating" style="max-width: 100%; height: auto;" loading="lazy" decoding="async"></i></div>
                                <div class="print-number"><span>02</span></div>
                                <div class="print-txt">
                                    <p>Clients Will</p>
                                </div>
                                <div class="print-title"><a href="#">Hires(Buy) Them</a></div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 print-block print-block-right lrmargin mt-3">
                            <div class="w-print-block">
                                <div class="print-icon"><i><img src="{{asset('assets/frontend/img/static/icon-3.jpg')}}" alt="rating" style="max-width: 100%; height: auto;" loading="lazy" decoding="async"></i></div>
                                <div class="print-number"><span>03</span></div>
                                <div class="print-txt">
                                    <p>Clients Will Leave</p>
                                </div>
                                <div class="print-title"><a href="#">Feedback and Pay</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-lg-12">
                        <div class="browse-all-cat"><a href="{{route('client.job.create')}}" title="">Post Your Project Gigs FREE</a></div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Category area end -->

    {!! PageBuilderSetup::render_widgets_by_name_for_frontend(PageBuilderSetup::getWidgetArgs($CategoryJobOne)) !!}

    @php
        $explore_projects = Cache::remember('explore_projects_home', 3600, function () {
            return Project::select('id', 'title', 'slug', 'user_id', 'basic_regular_charge', 'basic_discount_charge', 'basic_delivery', 'description', 'image', 'load_from')
                ->where('project_on_off', '1')
                ->where('status', '1')
                ->with('project_creator:id,username,image,first_name,last_name')
                ->whereHas('project_creator')
                ->inRandomOrder()
                ->take(5)
                ->get();
        });
    @endphp
    <!-- Latest Project area starts -->
    <section class="project-area pat-50 pab-50 section-bg-1" data-padding-top="55" data-padding-bottom="52">
        <div class="container">

            <div class="section-title text-left append-flex">
                <h2 class="title"> Utilise Top Projects (gigs)</h2>
                <div class="d-flex flex-column gap-2 align-items-end">
                    <div>
                    </div>
                    <div class="append-project"></div>
                </div>
            </div>
            <p>Filtered for your needs (gigs)</p>
            <p>Top Projects (gigs) allow applicants to offer their Projects to the end user. These can be beyond the
                scope of digital media and online work. For example, now you can hire designer, developer, seo expert,
                plumbing and housekeeping Projects on competitive rates at Right Freelancer. Choose from the biggest
                network of freelancers and professionals in various fields.</p>


            <div class="row mt-5">
                <div class="col-12">
                    <div class="global-slick-init project-slider nav-style-one slider-inner-margin" data-rtl="false"
                        data-appendArrows=".append-project" data-arrows="true" data-infinite="true" data-dots="false"
                        data-slidesToShow="3" data-swipeToSlide="true" data-autoplaySpeed="2500"
                        data-prevArrow='<div class="prev-icon"><i class="fa-solid fa-arrow-left"></i></div>'
                        data-nextArrow='<div class="next-icon"><i class="fa-solid fa-arrow-right"></i></div>'
                        data-responsive='[{"breakpoint": 1400,"settings": {"slidesToShow": 3}},{"breakpoint": 1200,"settings": {"slidesToShow": 2}},{"breakpoint": 992,"settings": {"slidesToShow": 2}},{"breakpoint": 768,"settings": {"slidesToShow": 1}},{"breakpoint": 576, "settings": {"slidesToShow": 1} }]'>

                        @foreach($explore_projects as $project)
                            <div class="project-item wow fadeInUp" data-wow-delay=".1s">
                                <div class="single-project radius-10">
                                    <div class="single-project-thumb">
                                       <a href="/assets/qixer-service-provider-buyer-mobile-appp">
                                          @php
                                            $raw = $project->image;
                                            $files = is_array($raw)
                                                ? $raw
                                                : (json_decode($raw) !== null && json_last_error() === JSON_ERROR_NONE
                                                    ? json_decode($raw, true)
                                                    : [$raw]);
                                            $isCloud = cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']);
                                        @endphp

                                            @if(!empty($files))
                                                @php
                                                    $firstFile = $files[0] ?? null;
                                                    $ext = strtolower(pathinfo($firstFile, PATHINFO_EXTENSION));
                                                    $fileUrl = $isCloud
                                                        ? render_frontend_cloud_image_if_module_exists('project/' . $firstFile, load_from: $project->load_from)
                                                        : asset('assets/uploads/project/' . $firstFile);
                                                @endphp

                                                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg']))
                                                    <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                                        <img src="{{ $fileUrl }}" alt="{{ $project->title }}" style="width: 100%; height: 240px; object-fit: cover;" width="400" height="240" loading="lazy" decoding="async">
                                                    </a>
                                                @elseif(in_array($ext, ['mp4', 'mov', 'webm', 'mkv']))
                                                    <video controls style="width: 100%; height: 240px; object-fit: cover;">
                                                        <source src="{{ $fileUrl }}" type="video/{{ $ext }}">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @else
                                                    <a href="{{ $fileUrl }}" target="_blank" class="d-block text-center p-3 border rounded bg-light">
                                                        @if($ext == 'pdf')
                                                            <img src="{{ asset('assets/icons/pdf-icon.png') }}" alt="PDF" style="width: 60px;">
                                                        @elseif(in_array($ext, ['doc', 'docx']))
                                                            <img src="{{ asset('assets/icons/word-icon.png') }}" alt="Word" style="width: 60px;">
                                                        @elseif(in_array($ext, ['xls', 'xlsx', 'csv']))
                                                            <img src="{{ asset('assets/icons/excel-icon.png') }}" alt="Excel" style="width: 60px;">
                                                        @else
                                                            <i class="fa fa-file text-muted fa-2x"></i><br>
                                                            <small>{{ $firstFile }}</small>
                                                        @endif
                                                    </a>
                                                @endif
                                            @else
                                                <img src="{{ asset('assets/uploads/project/project-default-logo.jpeg') }}" alt="Default Project Image" style="width: 100%; height: 240px; object-fit: cover;">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="single-project-content">
                                        {!! project_rating($project->id) !!}
                                        <h4 class="single-project-content-title" style=" overflow: hidden; text-overflow: ellipsis; display: block; width: 100%;">
                                            <a
                                                href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                                {{\Illuminate\Support\Str::limit($project->title, 60)}}
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="single-project-bottom flex-between">
                                        <span class="single-project-content-price">
                                        @if($project->basic_discount_charge)
                                            {{float_amount_with_currency_symbol($project->basic_discount_charge)}}
                                            <s>{{float_amount_with_currency_symbol($project->basic_regular_charge)}}</s>
                                        @else
                                            {{float_amount_with_currency_symbol($project->basic_regular_charge)}}
                                        @endif
                                        </span>
                                        <div class="single-project-delivery">
                                            <span class="single-project-delivery-icon"> <i class="fa-regular fa-clock"></i>
                                                Delivery </span>
                                            <span class="single-project-delivery-days"> {{$project->basic_delivery}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Latest Project area end -->

    <section class="wt-haslayout wt-main-section wt-paddingnull wt-bannerholdervtwo"
        style="background-image: url('{{asset('assets/frontend/img/static/1557484284-banner.jpg')}}'); background-size: cover; background-position: center; background-repeat: no-repeat;" loading="lazy">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="wt-companydetails d-flex flex-column flex-lg-row gap-5">

                        <!-- Agency Block -->
                        <div class="wt-companycontent w-100">
                            <div class="wt-companyinfotitle">
                                <h2>Begin As An Agency</h2>
                            </div>
                            <div class="wt-description">
                                <p>Whether, you are a web design/web development company,
                                    digital marketing agency, woo-commerce, e-commerce, fashion industry or textile
                                    firm, on our platform, you will get the right employers and freelancers who can work
                                    easily in flexible and a cooperative manner with you. Your all creative designing,
                                    creative writing and digital marketing needs will be met up to your requirements.
                                    Our freelancers will make your job successful faster than you could have possibly
                                    imagined before. Find them right here- right now and leave the rest on us.</p>
                            </div>
                            <div class="wt-btnarea">
                                <a href="{{ route('user.register') }}" class="wt-btn">Join Now</a>
                            </div>
                        </div>

                        <!-- Freelancer Block -->
                        <div class="wt-companycontent w-100">
                            <div class="wt-companyinfotitle">
                                <h2>Begin As A Freelancer</h2>
                            </div>
                            <div class="wt-description">
                                <p>From all around the world, over 1 Million freelancers have
                                    joined RightFreelancer to showcase their talent, skills and earn million bucks
                                    online and now they are hunting the joyful life through that freelance earned money.
                                    Yes ! With RightFreelancer hourly or fixed price jobs, you can also skyrocket your
                                    independent career. If you join this good company, you will be among the best
                                    freelancers around the world and get cash flow to grow your business as an
                                    independent entrepreneur sooner than later.</p>
                            </div>
                            <div class="wt-btnarea">
                                <a href="{{ route('user.register') }}" class="wt-btn">Join Now</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>


    
@endsection
