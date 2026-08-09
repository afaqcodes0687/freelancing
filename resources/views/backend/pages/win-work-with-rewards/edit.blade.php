@extends('backend.layout.master')
@section('title', __('Win Work With Rewards Settings'))
@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="customMarkup__single__item__flex">
                            <h4 class="customMarkup__single__title">{{ __('Win Work With Rewards Settings') }}</h4>
                        </div>
                        <div class="customMarkup__single__inner mt-4">
                            <form action="{{ route('admin.win-work-with-rewards.update') }}" method="POST">
                                @csrf
                                
                                <!-- SEO Meta Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ __('SEO Meta Information') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="title">{{ __('Page Title') }}</label>
                                                    <input type="text" name="title" id="title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->title ?? 'Win Work With Reward' }}"
                                                           required>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="meta_title">{{ __('Meta Title') }}</label>
                                                    <input type="text" name="meta_title" id="meta_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->meta_title ?? '' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="meta_description">{{ __('Meta Description') }}</label>
                                                    <textarea name="meta_description" id="meta_description" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->meta_description ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="meta_keywords">{{ __('Meta Keywords') }}</label>
                                                    <input type="text" name="meta_keywords" id="meta_keywords" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->meta_keywords ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Banner Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ __('Banner Section') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="banner_title">{{ __('Banner Title') }}</label>
                                                    <input type="text" name="banner_title" id="banner_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->banner_title ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Main Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ __('Main Section') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="main_title">{{ __('Main Title') }}</label>
                                                    <input type="text" name="main_title" id="main_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->main_title ?? '' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="main_subtitle">{{ __('Main Subtitle') }}</label>
                                                    <textarea name="main_subtitle" id="main_subtitle" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->main_subtitle ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="clients_count">{{ __('Clients Count') }}</label>
                                                    <input type="text" name="clients_count" id="clients_count" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->clients_count ?? '' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="clients_text">{{ __('Clients Text') }}</label>
                                                    <input type="text" name="clients_text" id="clients_text" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->clients_text ?? '' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="freelancers_count">{{ __('Freelancers Count') }}</label>
                                                    <input type="text" name="freelancers_count" id="freelancers_count" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->freelancers_count ?? '' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="freelancers_text">{{ __('Freelancers Text') }}</label>
                                                    <input type="text" name="freelancers_text" id="freelancers_text" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->freelancers_text ?? '' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="orders_count">{{ __('Orders Count') }}</label>
                                                    <input type="text" name="orders_count" id="orders_count" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->orders_count ?? '' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="orders_text">{{ __('Orders Text') }}</label>
                                                    <input type="text" name="orders_text" id="orders_text" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->orders_text ?? '' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="main_image">{{ __('Main Image') }}</label>
                                                    <input type="text" name="main_image" id="main_image" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->main_image ?? '' }}"
                                                           placeholder="assets/frontend/img/boosted.png">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Solutions Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ __('Solutions Section') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="solutions_title">{{ __('Solutions Title') }}</label>
                                                    <input type="text" name="solutions_title" id="solutions_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->solutions_title ?? '' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="solutions_subtitle">{{ __('Solutions Subtitle') }}</label>
                                                    <textarea name="solutions_subtitle" id="solutions_subtitle" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->solutions_subtitle ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ad Products Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ __('Ad Products Section') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Boosted Profile -->
                                            <div class="col-md-6">
                                                <h6 class="mb-3">{{ __('Boosted Profile') }}</h6>
                                                <div class="form-group">
                                                    <label for="boosted_profile_title">{{ __('Title') }}</label>
                                                    <input type="text" name="boosted_profile_title" id="boosted_profile_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->boosted_profile_title ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="boosted_profile_subtitle">{{ __('Subtitle') }}</label>
                                                    <input type="text" name="boosted_profile_subtitle" id="boosted_profile_subtitle" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->boosted_profile_subtitle ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="boosted_profile_content">{{ __('Content') }}</label>
                                                    <textarea name="boosted_profile_content" id="boosted_profile_content" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->boosted_profile_content ?? '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="boosted_profile_image">{{ __('Image') }}</label>
                                                    <input type="text" name="boosted_profile_image" id="boosted_profile_image" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->boosted_profile_image ?? '' }}"
                                                           placeholder="assets/frontend/img/boosted-1.jpg">
                                                </div>
                                            </div>
                                            
                                            <!-- Availability Badge -->
                                            <div class="col-md-6">
                                                <h6 class="mb-3">{{ __('Availability Badge') }}</h6>
                                                <div class="form-group">
                                                    <label for="availability_badge_title">{{ __('Title') }}</label>
                                                    <input type="text" name="availability_badge_title" id="availability_badge_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->availability_badge_title ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="availability_badge_subtitle">{{ __('Subtitle') }}</label>
                                                    <input type="text" name="availability_badge_subtitle" id="availability_badge_subtitle" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->availability_badge_subtitle ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="availability_badge_content">{{ __('Content') }}</label>
                                                    <textarea name="availability_badge_content" id="availability_badge_content" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->availability_badge_content ?? '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="availability_badge_image">{{ __('Image') }}</label>
                                                    <input type="text" name="availability_badge_image" id="availability_badge_image" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->availability_badge_image ?? '' }}"
                                                           placeholder="assets/frontend/img/boosted-2.jpg">
                                                </div>
                                            </div>
                                            
                                            <!-- Enhanced Proposals -->
                                            <div class="col-md-6">
                                                <h6 class="mb-3">{{ __('Enhanced Proposals') }}</h6>
                                                <div class="form-group">
                                                    <label for="enhanced_proposals_title">{{ __('Title') }}</label>
                                                    <input type="text" name="enhanced_proposals_title" id="enhanced_proposals_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->enhanced_proposals_title ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="enhanced_proposals_subtitle">{{ __('Subtitle') }}</label>
                                                    <input type="text" name="enhanced_proposals_subtitle" id="enhanced_proposals_subtitle" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->enhanced_proposals_subtitle ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="enhanced_proposals_content">{{ __('Content') }}</label>
                                                    <textarea name="enhanced_proposals_content" id="enhanced_proposals_content" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->enhanced_proposals_content ?? '' }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="enhanced_proposals_image">{{ __('Image') }}</label>
                                                    <input type="text" name="enhanced_proposals_image" id="enhanced_proposals_image" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->enhanced_proposals_image ?? '' }}"
                                                           placeholder="assets/frontend/img/boosted-3.jpg">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Information Sections -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ __('Information Sections') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="payment_title">{{ __('Payment Title') }}</label>
                                                    <input type="text" name="payment_title" id="payment_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->payment_title ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="payment_subtitle">{{ __('Payment Subtitle') }}</label>
                                                    <input type="text" name="payment_subtitle" id="payment_subtitle" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->payment_subtitle ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="payment_content">{{ __('Payment Content') }}</label>
                                                    <textarea name="payment_content" id="payment_content" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->payment_content ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="why_use_title">{{ __('Why Use Title') }}</label>
                                                    <input type="text" name="why_use_title" id="why_use_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->why_use_title ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="why_use_content">{{ __('Why Use Content') }}</label>
                                                    <textarea name="why_use_content" id="why_use_content" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->why_use_content ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="getting_started_title">{{ __('Getting Started Title') }}</label>
                                                    <input type="text" name="getting_started_title" id="getting_started_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->getting_started_title ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="getting_started_content">{{ __('Getting Started Content') }}</label>
                                                    <textarea name="getting_started_content" id="getting_started_content" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->getting_started_content ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="place_bid_title">{{ __('Place Bid Title') }}</label>
                                                    <input type="text" name="place_bid_title" id="place_bid_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->place_bid_title ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="place_bid_content">{{ __('Place Bid Content') }}</label>
                                                    <textarea name="place_bid_content" id="place_bid_content" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->place_bid_content ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Sections -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ __('Additional Sections') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="advertising_options_title">{{ __('Advertising Options Title') }}</label>
                                                    <input type="text" name="advertising_options_title" id="advertising_options_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->advertising_options_title ?? '' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">{{ __('Advertising Options List') }}</label>
                                                    <div class="alert alert-info mb-3">
                                                        <small class="text-muted">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            {{ __('Add multiple advertising options that will be displayed as a list on the frontend page.') }}
                                                        </small>
                                                    </div>
                                                    <div id="advertising-options-points">
                                                        @if($winWorkWithRewards && $winWorkWithRewards->advertising_options)
                                                            @foreach($winWorkWithRewards->advertising_options as $index => $option)
                                                                <div class="input-group mb-2">
                                                                    <span class="input-group-text">
                                                                        <i class="fas fa-list"></i>
                                                                    </span>
                                                                    <input type="text" name="advertising_options[]" 
                                                                           class="form-control" 
                                                                           style="max-width: 250px;"
                                                                           value="{{ $option }}"
                                                                           placeholder="Option {{ $index + 1 }}">
                                                                    <button type="button" class="btn btn-outline-danger remove-option" title="Remove">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    <button type="button" class="btn btn-primary" id="add-advertising-option" style="background-color: #309400; border-color: #309400;">
                                                        <i class="fas fa-plus"></i> Add Point
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <hr class="my-4">
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="helpful_resources_title">{{ __('Helpful Resources Title') }}</label>
                                                    <input type="text" name="helpful_resources_title" id="helpful_resources_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->helpful_resources_title ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="helpful_resources_content">{{ __('Helpful Resources Content') }}</label>
                                                    <textarea name="helpful_resources_content" id="helpful_resources_content" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->helpful_resources_content ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ads_guide_title">{{ __('Ads Guide Title') }}</label>
                                                    <input type="text" name="ads_guide_title" id="ads_guide_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->ads_guide_title ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="ads_guide_content">{{ __('Ads Guide Content') }}</label>
                                                    <textarea name="ads_guide_content" id="ads_guide_content" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->ads_guide_content ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="master_ads_title">{{ __('Master Ads Title') }}</label>
                                                    <input type="text" name="master_ads_title" id="master_ads_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->master_ads_title ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="master_ads_content">{{ __('Master Ads Content') }}</label>
                                                    <textarea name="master_ads_content" id="master_ads_content" 
                                                              class="form-control" 
                                                              rows="3">{{ $winWorkWithRewards->master_ads_content ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cta_title">{{ __('CTA Title') }}</label>
                                                    <input type="text" name="cta_title" id="cta_title" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->cta_title ?? '' }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="cta_button_text">{{ __('CTA Button Text') }}</label>
                                                    <input type="text" name="cta_button_text" id="cta_button_text" 
                                                           class="form-control" 
                                                           value="{{ $winWorkWithRewards->cta_button_text ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">{{ __('Save Settings') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Add new advertising option point
        $(document).on('click', '#add-advertising-option', function() {
            var optionIndex = $('#advertising-options-points .input-group').length + 1;
            var newOption = $('<div class="input-group mb-2">' +
                '<span class="input-group-text">' +
                '<i class="fas fa-list"></i>' +
                '</span>' +
                '<input type="text" name="advertising_options[]" ' +
                'class="form-control" ' +
                'style="max-width: 250px;" ' +
                'placeholder="Option ' + optionIndex + '">' +
                '<button type="button" class="btn btn-outline-danger remove-option" title="Remove">' +
                '<i class="fas fa-trash"></i>' +
                '</button>' +
                '</div>');
            $('#advertising-options-points').append(newOption);
        });

        // Remove advertising option point
        $(document).on('click', '.remove-option', function() {
            $(this).closest('.input-group').remove();
        });
    </script>
@endsection
