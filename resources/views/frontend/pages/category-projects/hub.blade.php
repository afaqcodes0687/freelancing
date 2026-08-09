@extends('frontend.layout.master')
@section('site_title') {{ $category->category ?? __('Category Hub') }} @endsection
@section('meta_title'){{ $category->meta_title ?? '' }}@endsection
@section('meta_description'){{ $category->meta_description ?? '' }}@endsection

@section('style')
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

/* ═══ Stats Bar ═══ */
.stats-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 0;
    background: #fff;
    border-radius: 14px;
    border: 1px solid var(--border-color);
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    margin-top: -28px;
    position: relative;
    z-index: 10;
}
.stats-bar-item {
    flex: 1;
    min-width: 160px;
    padding: 24px 28px;
    border-right: 1px solid var(--border-color);
    text-align: center;
}
.stats-bar-item:last-child { border-right: none; }
.stats-bar-item .stat-num {
    font-size: 2rem;
    font-weight: 800;
    color: var(--main-color-one);
    display: block;
    line-height: 1;
    margin-bottom: 6px;
}
.stats-bar-item .stat-label {
    font-size: .85rem;
    color: var(--paragraph-color);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: .04em;
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
    min-height: 56px;
    height: 56px;
}

.child-cat-card-name {
    width: 100%;
    text-align: center;
    margin: 0;
    max-width: 100%;
    font-weight: 600;
    word-break: break-word;
    overflow-wrap: break-word;
    white-space: normal;
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
.child-cat-arrow {
    font-size: 12px;
    color: #bbb;
    transition: all .25s;
    flex-shrink: 0;
    margin-left: 10px;
}
.child-cat-card:hover .child-cat-arrow {
    color: var(--main-color-one);
    transform: translateX(4px);
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

/* ═══ Empty State ═══ */
.hub-empty {
    text-align: center;
    padding: 80px 20px;
    background: #fff;
    border-radius: 14px;
    border: 1.5px dashed var(--border-color);
}
.hub-empty i { font-size: 48px; color: #ccc; display: block; margin-bottom: 16px; }
</style>
@endsection

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

@section('content')
<main>
    @if(moduleExists('CoinPaymentGateway'))@else<x-frontend.category.category/>@endif
    <x-breadcrumb.user-profile-breadcrumb :title="$category->category ?? __('Category')" :innerTitle="$category->category ?? ''"/>

    <div class="preview-area section-bg-2 pat-30 pab-100">
        <div class="container">

            {{-- ══════════ HERO BANNER ══════════ --}}
            <div class="cat-hero mb-4">
                @if(!empty($category->image))
                    <div class="cat-hero-bg" style="background-image:url('{{ asset('assets/uploads/category/'.$category->image) }}');"></div>
                @endif
                <div class="cat-hero-body px-5">
                    <h1>{{ $category->category }}</h1>
                    @if(!empty($category->short_description))
                        <p class="lead">{{ $category->short_description }}</p>
                    @endif
                    <a href="#how-it-works" class="cat-hero-btn"># How Right Freelance Work</a>
                </div>
            </div>

            {{-- ══════════ STATS BAR ══════════ --}}
            <!-- @if(isset($project_count) || isset($freelancer_count))
            <div class="stats-bar mb-5">
                <div class="stats-bar-item">
                    <span class="stat-num">{{ number_format($project_count ?? 0) }}+</span>
                    <span class="stat-label">{{ __('Services') }}</span>
                </div>
                <div class="stats-bar-item">
                    <span class="stat-num">{{ number_format($freelancer_count ?? 0) }}+</span>
                    <span class="stat-label">{{ __('Sellers') }}</span>
                </div>
                <div class="stats-bar-item">
                    <span class="stat-num"><i class="fas fa-star" style="font-size:1.4rem;color:var(--main-color-one)"></i></span>
                    <span class="stat-label">{{ __('Top Rated') }}</span>
                </div>
                <div class="stats-bar-item">
                    <span class="stat-num"><i class="fas fa-shield-alt" style="font-size:1.4rem;color:var(--main-color-one)"></i></span>
                    <span class="stat-label">{{ __('100% Secure') }}</span>
                </div>
            </div>
            @endif -->

            {{-- ══════════ EXPLORE SERVICES (Flat Child Categories) ══════════ --}}
            <div class="mb-5">
                <div class="hub-section-head">
                    <h2>{{ __('Explore Services') }}</h2>
                    <p>{{ __('Browse all services available in') }} {{ $category->category }}</p>
                </div>

              <div class="child-cat-grid">
    @foreach($category->sub_categories as $sub)
        <a href="{{ route('subcategory.landing', ['category_slug' => $category->slug ?? '', 'sub_slug' => $sub->slug]) }}" class="child-cat-card">
            <span class="child-cat-card-name">{{ $sub->sub_category }}</span>
        </a>
    @endforeach
</div>
            </div>

            {{-- ══════════ DYNAMIC FAQS ══════════ --}}
            @if(isset($faqs) && $faqs->count() > 0)
            <div class="faq-section mb-5">
                <div class="hub-section-head">
                    <h2>{{ __('Frequently Asked Questions') }}</h2>
                    <p>{{ __('Everything you need to know about') }} {{ $category->category }}</p>
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
