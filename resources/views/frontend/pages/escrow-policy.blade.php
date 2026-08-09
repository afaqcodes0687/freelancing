@php use plugins\PageBuilder\PageBuilderSetup; @endphp
@extends('frontend.layout.master')

@section('site_title', __('Escrow Policy'))
@section('meta_title')
    {{ __('Escrow & Payment Protection - Right Freelancer | Secure Freelance Transactions') }}
@endsection

@section('meta_description')
    {{ __('Learn how Right Freelancer protects your project funds using secure escrow services. Our Escrow Policy outlines dispute handling, payment releases, and client-freelancer fund safety.') }}
@endsection

<style>
    .escrow-policy-banner {
        background-color: #2B7667;
        padding: 90px 0;
        text-align: center;
        color: white;
    }

    .escrow-policy-banner .escrow-title {
        font-size: 70px;
        font-weight: 700;
        margin-bottom: 20px;
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

</style>
@section('content')
 <div class="banner-inner-area border-top pat-20 pab-20">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="banner-inner-contents">
                        <ul class="inner-menu">
                            <li class="list"><a href="https://www.rightfreelancer.com">Home </a></li>
                            <li class="list"> Escrow Policy </li>
                        </ul>
                        <h2 class="banner-inner-title">Escrow Policy </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="banner-inner-area border-top pat-20">
        <div class="container-fulid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="escrow-policy-banner ">
                        <div class="container text-center">
                            <h1 class="escrow-title">Escrow Policy</h1>
                            <span class="effective-date">
                                Effective Date: {{ \Carbon\Carbon::now()->format('jS F Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About area starts -->
    <section class="about-area section-bg-2 pat-100 pab-100" data-padding-top="100" data-padding-bottom="100" style="background-color:#F5F5F5">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-xxl-12 col-lg-12">
                    <div class="about-wrapper-left">
                        <div class="section-title text-left">
                            <h2 class="title">Escrow Policy</h2>
                            
                            <p class="section-para"><p style="text-align:left;"><span style="font-weight:normal;">Right Freelancer LLC. provides this data to allow you to have a look at our policies and procedures concerning the gathering, use and speech act of knowledge through https://www.rightfreelancer.com/ (the “Site”), and the other websites, features, applications, widgets or online services that are owned or controlled by RightFreelancer which post a link to the current Escrow Policy (together with the location, the “Service”), additionally as any data RightFreelancer collects offline in reference to the Service. It additionally describes the alternatives accessible to you concerning the utilization of, your access to, and the way to update and proper your personal data. Note that we tend to mix the knowledge we collect from you from the location, through the Service usually.</span></p>

                            <p style="text-align:left;"><span style="font-weight:normal;">Please note that bound options or services documented during this Escrow Policy might not be offered on the Service the least bit times. Please additionally review our Terms of Service, that governs your use of the Service, and that is accessible at the location. We also have provided short summaries during this Escrow Policy to assist you to perceive what data we tend to collect; however, we tend to use it, and what choices or rights you have got. Whereas these summaries facilitate a number of the ideas in a less complicated manner. Therefore, we tend to encourage you to scan the complete Escrow Policy to own a more robust understanding of our best knowledge practices.</span><br></p>
                            <span><br></span>
                            
                            <h2 class="title">Automatic Data Assortment</h2>
                            <p class="section-para"><p style="text-align:left;"><span style="font-weight:normal;">Like alternative online corporations, we tend to receive technical data once you use our Services. We tend to use our latest technologies to investigate however individuals use our Services, to enhance however our website functions, to save lots of your log-in data for future sessions, and to serve you with advertisements which will interest you.</span></p>

                            <p style="text-align:left;"><span style="font-weight:normal;"><span><br></span></span></p>

                            <p style="text-align:left;"><span style="font-weight:normal;">We and our third-party service suppliers, together with analytics and third-party content suppliers, might mechanically collect bound data from you whenever you access or act with our Service. This data might embrace, among alternative data, the browser and OS you're exploitation, the URL or publicity that referred you to the Service, the search terms you entered into a probe engine that junction rectifier you to the Service, areas among the Service that you just visited, what links you clicked on, that pages or content you viewed and for the way long, alternative similar data and statistics regarding your interactions, like content response times, transfer errors and length of visits to bound pages and alternative data unremarkably shared once browsers communicate with websites. We tend to might conjoin this mechanically collected log data with alternative information we collect regarding you. we tend to do that to enhance the services we provide you, and to enhance promoting, analytics, and website practicality.</span><br></p>
                            <span><br></span>
                                   
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </section>
@endsection
