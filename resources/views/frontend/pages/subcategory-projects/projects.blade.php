@extends('frontend.layout.master')
@section('site_title') 
    {{ isset($child_category) ? ($child_category->name . ' - ' . ($subcategory->sub_category ?? '')) : ($subcategory->sub_category ?? __('Subcategory Project')) }} 
@endsection
@section('meta_title') 
    {{ isset($child_category) ? ($child_category->meta_title ?? $child_category->name) : ($subcategory->meta_title ?? '') }} 
@endsection
@section('meta_description') 
    {{ isset($child_category) ? ($child_category->meta_description ?? '') : ($subcategory->meta_description ?? '') }} 
@endsection
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
            z-index: 100;
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
    </style>
@endsection

@section('content')
    <main>
        @if(moduleExists('CoinPaymentGateway'))@else<x-frontend.category.category/>@endif

        <x-breadcrumb.user-profile-breadcrumb 
            :title="isset($child_category) ? ($child_category->name . ' — ' . ($subcategory->sub_category ?? '')) : ($subcategory->sub_category ?? __('Project Category'))" 
            :innerTitle="$subcategory->sub_category ?? '' "/>

        {{-- ── Child Category Filter Bar ── --}}
        @if(isset($subcategory) && $subcategory->child_categories->count() > 0)
            <div class="child-filter-bar">
                <div class="container">
                    <div class="child-filter-inner">
                        {{-- Left scroll button --}}
                        <button type="button" class="child-filter-scroll-btn" id="child-filter-scroll-left">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        {{-- Tabs container --}}
                        <div class="child-filter-tabs-wrapper" style="display: flex; gap: 8px; overflow-x: auto; scrollbar-width: none; flex: 1;">
                            {{-- "All" tab --}}
                            <button type="button"
                                class="child-filter-tab {{ !isset($child_category) ? 'active' : '' }}"
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
        <div class="preview-area section-bg-2 pat-100 pab-100">
            <div class="container">
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
                                <input type="hidden" id="child_category_id" value="{{$child_category->id ?? ''}}">
                                <div class="shop-contents-wrapper-right search_subcategory_result">
                                    @include('frontend.pages.subcategory-projects.search-subcategory-result')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                
                // Update button states on scroll
                tabsWrapper.addEventListener('scroll', function() {
                    scrollLeftBtn.disabled = tabsWrapper.scrollLeft <= 0;
                    scrollRightBtn.disabled = tabsWrapper.scrollLeft + tabsWrapper.clientWidth >= tabsWrapper.scrollWidth - 1;
                });
                
                // Initial button state
                scrollLeftBtn.disabled = true;
                scrollRightBtn.disabled = tabsWrapper.scrollWidth <= tabsWrapper.clientWidth;
            }
        });
    </script>
@endsection
