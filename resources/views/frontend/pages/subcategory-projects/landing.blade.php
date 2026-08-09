@extends('frontend.layout.master')
@section('site_title') {{ $subcategory->sub_category ?? __('Subcategory') }} @endsection
@section('meta_title'){{ $metaTitle ?? $subcategory->meta_title ?? '' }}@endsection
@section('meta_description'){{ $metaDescription ?? $subcategory->meta_description ?? '' }}@endsection
@section('canonical', $canonicalUrl ?? route('subcategory.landing', ['category_slug' => $subcategory->category->slug ?? '', 'sub_slug' => $subcategory->slug]))

@section('style')
<x-select2.select2-css />
<style>
/* ═══ Hero ═══ */
.cat-hero {
    position: relative;
    background: linear-gradient(135deg, var(--main-color-one) 0%, var(--main-color-two, #6c63ff) 100%);
    padding: 80px 0 70px;
    border-radius: 16px;
    overflow: hidden;
    margin-top: 30px;
    color: #fff;
}
.cat-hero-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    opacity: 0.13;
    mix-blend-mode: multiply;
}
.cat-hero-body {
    position: relative;
    z-index: 2;
    text-align: center;
}
.cat-hero h1 {
    font-size: 2.8rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 14px;
    line-height: 1.2;
}
.cat-hero p.lead {
    font-size: 1.1rem;
    opacity: 1;
    color: #fff;
    max-width: 680px;
    line-height: 1.65;
    margin: 0 auto 24px;
}
.cat-hero-btn {
    display: inline-block;
    background: #fff;
    color: var(--main-color-one);
    padding: 12px 32px;
    border-radius: 30px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}
.cat-hero-btn:hover {
    background: rgba(255,255,255,0.9);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* ═══ Section Headers ═══ */
.hub-section-head {
    margin-bottom: 28px;
}
.hub-section-head h2 {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--heading-color);
    margin: 0;
    position: relative;
    padding-bottom: 12px;
}
.hub-section-head h2::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0;
    width: 48px; height: 3px;
    background: var(--main-color-one);
    border-radius: 2px;
}
.hub-section-head p {
    margin-top: 10px;
    color: var(--paragraph-color);
    font-size: .96rem;
}

/* ═══ Child Category Cards ═══ */
.child-cat-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
}

@media (max-width: 1200px) {
    .child-cat-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 992px) {
    .child-cat-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .child-cat-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .child-cat-grid {
        grid-template-columns: 1fr;
    }
}

.child-cat-card {
    background: #edeef1;
    /* background: #f7f8fc; */
    border: 1.5px solid transparent;
    border-radius: 10px;
    padding: 16px 18px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    text-decoration: none;
    transition: all .25s ease;
    cursor: pointer;
    min-height: 56px;
    height: 56px;
}

.child-cat-card-name {
    font-size: .95rem;
    font-weight: 600;
    margin: 0;
    width: 100%;
    max-width: 100%;
    text-align: center;
    white-space: normal;
    word-break: break-word;
    overflow-wrap: break-word;
}

.child-cat-card:hover {
    background: #fff;
    border-color: var(--main-color-one);
    box-shadow: 0 6px 22px rgba(0,0,0,.08);
    transform: translateY(-3px);
}

.child-cat-card:hover .child-cat-card-name {
    color: var(--main-color-one);
}

/* Hide arrow */
.child-cat-arrow {
    display: none !important;
}
/* ═══ FAQs ═══ */
.faq-section { }
.faq-accordion .accordion-item {
    border: 1px solid var(--border-color);
    border-radius: 10px !important;
    margin-bottom: 10px;
    overflow: hidden;
}
.faq-accordion .accordion-button {
    font-weight: 600;
    font-size: 1rem;
    color: var(--heading-color);
    background: #fff;
    padding: 18px 24px;
    border-radius: 10px !important;
    box-shadow: none;
}
.faq-accordion .accordion-button:not(.collapsed) {
    color: var(--main-color-one);
    background: #f7f8fc;
    border-bottom: 1px solid var(--border-color);
}
.faq-accordion .accordion-button::after {
    filter: none;
}
.faq-accordion .accordion-body {
    font-size: .96rem;
    color: var(--paragraph-color);
    line-height: 1.75;
    padding: 18px 24px;
    background: #fff;
}
.faq-accordion .accordion-button:focus { box-shadow: none; }

.pro-profile-badge {
    position: absolute;
    right: -10px;
    top: -10px;
    border-radius:20px;
    background: #FAF5FF;
    color: #9e4cf4;
    font-weight: 600;
}
.pro-icon-background {
    display: flex;
    justify-content: center;
    align-items: center;
    background: #9e4cf4;
    padding: 3px;
    border-radius: 50%;
    color: #fff;
    font-size: 12px;
}
.project-category-item .single-project {
    position: relative;
}
</style>
@endsection

@include('components.seo.meta-tags')

{{-- FAQ Schema.org structured data for SEO --}}
@if(isset($faqs) && $faqs->count() > 0)
@push('head_scripts')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    @foreach($faqs as $i => $faq)
    {
      "@type": "Question",
      "name": "{{ addslashes($faq->question) }}",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "{{ addslashes(strip_tags($faq->answer ?? '')) }}"
      }
    }{{ $loop->last ? '' : ',' }}
    @endforeach
  ]
}
</script>
@endpush
@endif

@push('head_scripts')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Service",
  "name": "{{ $subcategory->sub_category }}",
  "description": "{{ $metaDescription ?? $subcategory->meta_description ?? '' }}",
  "provider": {
    "@type": "Organization",
    "name": "{{ get_static_option('site_title') }}"
  },
  "areaServed": "Worldwide",
  "serviceType": "{{ $subcategory->sub_category }}"
}
</script>
@endpush

@section('content')
<main>
    @if(moduleExists('CoinPaymentGateway'))@else<x-frontend.category.category/>@endif
        <div class="breadcrumb-area border-top">
            <div class="container custom-container-one">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="breadcrumb-contents">
                            <h4 class="breadcrumb-contents-title"> {{ $subcategory->sub_category ?? __('Project Category') }} </h4>
                            <ul class="breadcrumb-contents-list list-style-none" style="flex-wrap: wrap; row-gap: 8px;">
                                <li class="breadcrumb-contents-list-item"> 
                                    <a href="{{ route('homepage') }}" class="breadcrumb-contents-list-item-link"> {{ __('Home') }} </a> 
                                </li>
                                <li class="breadcrumb-contents-list-item"> 
                                    <a href="{{ route('category.projects', $subcategory->category->slug ?? '') }}" class="breadcrumb-contents-list-item-link"> {{ $subcategory->category->category ?? '' }} </a> 
                                </li>
                                <li class="breadcrumb-contents-list-item"> 
                                    {{ $subcategory->sub_category ?? '' }} 
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="preview-area section-bg-2 pat-30 pab-100">
        <div class="container">
            {{-- ══════════ HERO BANNER ══════════ --}}
            <div class="cat-hero mb-4">
                @if(!empty($subcategory->image))
                    <div class="cat-hero-bg" style="background-image:url('{{ asset('assets/uploads/subcategory/'.$subcategory->image) }}');"></div>
                @endif
                <div class="cat-hero-body px-5">
                    <h1>{{ $subcategory->sub_category }}</h1>
                    @if(!empty($subcategory->short_description))
                        <p class="lead">{{ $subcategory->short_description }}</p>
                    @endif
                    <a href="{{ $subcategory->hero_cta_anchor ?? '#professionals' }}" class="cat-hero-btn">{{ $subcategory->hero_cta_text ?? __('Browse Professionals') }}</a>
                </div>
            </div>

            {{-- ══════════ SEO CONTENT ══════════ --}}
            <!-- @if(!empty($subcategory->seo_content))
            <div class="mb-5 content-area">
                {!! $subcategory->seo_content !!}
            </div>
            @endif -->

            {{-- ══════════ EXPLORE SERVICES (Child Categories) ══════════ --}}
            @if($subcategory->child_categories->count() > 0)
            <div class="mb-5">
                <div class="hub-section-head">
                    <h2>{{ __('Explore More') }} {{ $subcategory->sub_category }}</h2>
                </div>
                <div class="child-cat-grid">
                    @foreach($subcategory->child_categories as $child)
                        <a href="{{ route('child_category.landing', ['category_slug' => $subcategory->category->slug ?? '', 'sub_slug' => $subcategory->slug, 'child_slug' => $child->slug]) }}" class="child-cat-card">
                            <span class="child-cat-card-name">{{ $child->name }}</span>
                            <i class="fas fa-chevron-right child-cat-arrow"></i>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ══════════ DYNAMIC FAQS ══════════ --}}
            

            {{-- ══════════ PROFESSIONALS LISTING ══════════ --}}
            <div class="mb-5" id="professionals">
                <div class="hub-section-head">
                    <h2>{{ __('Browse Professionals') }}</h2>
                </div>
                
                <div class="row g-4">
                    @if(moduleExists('PromoteFreelancer'))
                        <div class="profile-wrapper-right-flex flex-btn text-right">
                            <span class="profile-wrapper-switch-title">{{ __('Pro Projects') }}</span>
                            <div class="profile-wrapper-switch-custom display_work_availability">
                                <label class="custom_switch">
                                    <input type="checkbox" id="get_pro_projects" value="0">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    @endif
                    <div class="col-lg-12">
                        <div class="categoryWrap-wrapper">
                            <div class="shop-contents-wrapper responsive-lg">
                                <div class="shop-icon">
                                    <div class="shop-icon-sidebar">
                                        <i class="fas fa-bars"></i>
                                    </div>
                                </div>

                                @include('frontend.pages.subcategory-projects.sidebar')
                                <input type="hidden" id="subcategory_id" value="{{$subcategory->id ?? ''}}">
                                <input type="hidden" id="child_category_id" value="">
                                <div class="shop-contents-wrapper-right search_subcategory_result">
                                    @include('frontend.pages.subcategory-projects.search-subcategory-result')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($faqs) && $faqs->count() > 0)
            <div class="faq-section mb-5">
                <div class="hub-section-head">
                    <h2>{{ __('Frequently Asked Questions') }}</h2>
                    <p>{{ __('Everything you need to know about') }} {{ $subcategory->sub_category }}</p>
                </div>

                <div class="accordion faq-accordion" id="faqAccordion">
                    @foreach($faqs as $i => $faq)
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faqHead{{ $i }}">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#faqBody{{ $i }}"
                                    aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                                    aria-controls="faqBody{{ $i }}">
                                {{ $faq->question }}
                            </button>
                        </h3>
                        <div id="faqBody{{ $i }}"
                             class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                             aria-labelledby="faqHead{{ $i }}"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {!! nl2br(e($faq->answer)) !!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</main>
@endsection

@section('script')
    @include('frontend.pages.subcategory-projects.subcategory-project-filter-js')
    <x-select2.select2-js />
@endsection
