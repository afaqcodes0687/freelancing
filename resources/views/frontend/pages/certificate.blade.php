@extends('frontend.layout.master')
@section('site_title', __('Certificate '))

@section('meta_title') {{ __('Certificate - Right Freelancer | Global Freelancing Platform') }}@endsection
@section('meta_description')
{{ __('Right Freelancer is a global freelancing platform connecting skilled professionals with businesses worldwide. Discover how we help freelancers grow and succeed.') }}@endsection

<style>
    .iso-certificates-section {
        background: #f9fafb;
    }

    .iso-certificates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        justify-items: center;
        align-items: center;
    }

    .certificate-item {
        text-align: center;
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .certificate-item:hover {
        transform: translateY(-6px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
    }

    .certificate-item img {
        width: 100%;
        height: auto;
    }
</style>
@section('content')
    <div class="banner-inner-area border-top pat-20 pab-20">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="banner-inner-contents">
                        <ul class="inner-menu">
                            <li class="list"><a href="https://www.rightfreelancer.com/">Home </a></li>
                            <li class="list"> Certificate </li>
                        </ul>
                        <h2 class="banner-inner-title"> Certificate </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="iso-certificates-section py-5">
        <div class="container">
            <h2 class="mb-4 fw-bold list" style="text-align:center">Right Freelancer LLC Certificates</h2>
            <p class="text-muted mb-5" style="text-align:center">
                Right Freelancer LLC is officially certified to ensure high-quality freelance services,
                data security, and client trust. Below is our verified certification.
            </p>
            <div class="iso-certificates-grid">
                <div class="certificate-item">
                    <img src="{{ asset('assets/uploads/certificates/Right-Freelancer-LLC.jpg') }}"
                        alt="ISO 9001 Certificate">
                </div>
            </div>
        </div>
    </section>
@endsection