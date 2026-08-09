@extends('frontend.layout.master')
@section('site_title') 
    {{ $child_category->name . ' - ' . ($subcategory->sub_category ?? '') }} 
@endsection
@section('meta_title') 
    {{ $metaTitle ?? $child_category->meta_title ?? $child_category->name }} 
@endsection
@section('meta_description') 
    {{ $metaDescription ?? $child_category->meta_description ?? '' }} 
@endsection
@section('canonical', $canonicalUrl ?? route('child_category.landing', ['category_slug' => $subcategory->category->slug ?? '', 'sub_slug' => $subcategory->slug, 'child_slug' => $child_category->slug]))

@section('style')
    <x-select2.select2-css />
    <style>
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
        .disabled-link {
            background-color: #ccc !important;
            pointer-events: none;
            cursor: default;
            border:none;
        }
        /* ── Child Category Filter Bar ── */
        .child-filter-bar {
            background: var(--white);
            border-bottom: 1px solid var(--border-color);
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 9;
        }
        .child-filter-inner {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            padding: 14px 0;
            scrollbar-width: none;
        }
        .child-filter-inner::-webkit-scrollbar { display: none; }
        .child-filter-tab {
            flex-shrink: 0;
            padding: 9px 22px;
            border-radius: 22px;
            background: transparent;
            border: 1.5px solid var(--border-color);
            color: var(--paragraph-color);
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.25s ease;
            white-space: nowrap;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .child-filter-tab:hover {
            border-color: var(--main-color-one);
            color: var(--main-color-one);
            background: rgba(var(--main-color-one-rgb, 0,0,0), 0.04);
        }
        .child-filter-tab.active {
            background: var(--main-color-one);
            border-color: var(--main-color-one);
            color: #fff;
        }
        .child-filter-tab.active:hover {
            color: #fff;
        }
        .child-filter-scroll-btn {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--white);
            border: 1.5px solid var(--border-color);
            color: var(--paragraph-color);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.25s ease;
            z-index: 10;
        }
        .child-filter-scroll-btn:hover {
            border-color: var(--main-color-one);
            color: var(--main-color-one);
        }
        .child-filter-scroll-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        /* ═══ FAQs ═══ */
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
  "name": "{{ $child_category->name }}",
  "description": "{{ $metaDescription ?? $child_category->meta_description ?? '' }}",
  "provider": {
    "@type": "Organization",
    "name": "{{ get_static_option('site_title') }}"
  },
  "areaServed": "Worldwide",
  "serviceType": "{{ $child_category->name }}"
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
                            <h4 class="breadcrumb-contents-title"> {{ $child_category->name }} </h4>
                            <ul class="breadcrumb-contents-list list-style-none" style="flex-wrap: wrap; row-gap: 8px;">
                                <li class="breadcrumb-contents-list-item"> 
                                    <a href="{{ route('homepage') }}" class="breadcrumb-contents-list-item-link"> {{ __('Home') }} </a> 
                                </li>
                                <li class="breadcrumb-contents-list-item"> 
                                    <a href="{{ route('category.projects', $subcategory->category->slug ?? '') }}" class="breadcrumb-contents-list-item-link"> {{ $subcategory->category->category ?? '' }} </a> 
                                </li>
                                <li class="breadcrumb-contents-list-item"> 
                                    <a href="{{ route('subcategory.landing', ['category_slug' => $subcategory->category->slug ?? '', 'sub_slug' => $subcategory->slug]) }}" class="breadcrumb-contents-list-item-link"> {{ $subcategory->sub_category ?? '' }} </a> 
                                </li>
                                <li class="breadcrumb-contents-list-item"> 
                                    {{ $child_category->name ?? '' }} 
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Child Category Filter Bar ── --}}
        @if(isset($subcategory) && $subcategory->child_categories->count() > 0)
            <div class="child-filter-bar">
                <div class="container">
                    <div class="child-filter-inner">
                        {{-- Left scroll button --}}
                        <button type="button" class="child-filter-scroll-btn" id="child-filter-scroll-left">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        <div class="child-filter-tabs-wrapper" style="display: flex; gap: 8px; overflow-x: auto; scrollbar-width: none; flex: 1;">
                            {{-- "All" tab --}}
                            <button type="button"
                                class="child-filter-tab"
                                data-child-id=""
                                data-child-slug="">
                                <i class="fas fa-th-large"></i> {{ __('All') }}
                            </button>

                            {{-- Per child category tab --}}
                            @foreach($subcategory->child_categories as $child_cat)
                                <button type="button"
                                    class="child-filter-tab {{ isset($child_category) && $child_category->id == $child_cat->id ? 'active' : '' }}"
                                    data-child-id="{{ $child_cat->id }}"
                                    data-child-slug="{{ $child_cat->slug }}">
                                    {{ $child_cat->name }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Right scroll button --}}
                        <button type="button" class="child-filter-scroll-btn" id="child-filter-scroll-right">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Project preview area --}}
        <div class="preview-area section-bg-2 pat-50 pab-100">
            <div class="container">
                <div class="row g-4" id="professionals">
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
                                <input type="hidden" id="child_category_id" value="{{$child_category->id ?? ''}}">
                                <div class="shop-contents-wrapper-right search_subcategory_result">
                                    @include('frontend.pages.subcategory-projects.search-subcategory-result')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════ DYNAMIC FAQS ══════════ --}}
                @if(isset($faqs) && $faqs->count() > 0)
                <div class="faq-section mt-5">
                    <div class="section-title mb-4">
                        <h3 class="title">{{ __('Frequently Asked Questions') }}</h3>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollLeftBtn = document.getElementById('child-filter-scroll-left');
            const scrollRightBtn = document.getElementById('child-filter-scroll-right');
            const tabsWrapper = document.querySelector('.child-filter-tabs-wrapper');
            
            if (scrollLeftBtn && scrollRightBtn && tabsWrapper) {
                const scrollAmount = 200;
                
                scrollLeftBtn.addEventListener('click', function() {
                    tabsWrapper.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                });
                
                scrollRightBtn.addEventListener('click', function() {
                    tabsWrapper.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                });
                
                tabsWrapper.addEventListener('scroll', function() {
                    scrollLeftBtn.disabled = tabsWrapper.scrollLeft <= 0;
                    scrollRightBtn.disabled = tabsWrapper.scrollLeft + tabsWrapper.clientWidth >= tabsWrapper.scrollWidth - 1;
                });
                
                scrollLeftBtn.disabled = true;
                scrollRightBtn.disabled = tabsWrapper.scrollWidth <= tabsWrapper.clientWidth;
            }

            // Child category tab click handler
            const childTabs = document.querySelectorAll('.child-filter-tab');
            const subcategoryIdInput = document.getElementById('subcategory_id');
            const childCategoryIdInput = document.getElementById('child_category_id');
            const resultContainer = document.querySelector('.search_subcategory_result');
            
            childTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Update active state
                    childTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    const childId = this.getAttribute('data-child-id');
                    
                    // Update hidden input
                    if (childCategoryIdInput) {
                        childCategoryIdInput.value = childId;
                    }
                    
                    // Load projects via AJAX
                    if (subcategoryIdInput && resultContainer) {
                        const params = new URLSearchParams();
                        params.append('subcategory_id', subcategoryIdInput.value);
                        if (childId) {
                            params.append('child_category_id', childId);
                        }
                        
                        fetch('{{ route('subcategory.projects.filter') }}?' + params.toString(), {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                return response.json().then(json => ({ type: 'json', data: json }));
                            }
                            return response.text().then(html => ({ type: 'html', data: html }));
                        })
                        .then(result => {
                            if (result.type === 'json' && result.data.status) {
                                // No projects found - show empty state
                                resultContainer.innerHTML = `
                                    <div class="col-12">
                                        <div class="notFoundParent project-category-item radius-10 text-center">
                                            <div class="notFound-wrapper">
                                                <div class="notFoundThumb">
                                                    <img src="{{ asset('assets/static/img/no-jobs-projects/no-project.svg') }}" alt="">
                                                </div>
                                                <div class="notFound-contents mt-3">
                                                    <h4 class="notFoundTitle">{{ __('No Projects') }}</h4>
                                                    <p class="notFoundPara mt-3">{{ __("Sorry, We couldn't find any projects in this category try checking on other categories") }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            } else if (result.type === 'html') {
                                // HTML response with projects
                                resultContainer.innerHTML = result.data;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            resultContainer.innerHTML = `
                                <div class="col-12 text-center">
                                    <p>{{ __('Error loading projects. Please try again.') }}</p>
                                </div>
                            `;
                        });
                    }
                });
            });
        });
    </script>
@endsection
