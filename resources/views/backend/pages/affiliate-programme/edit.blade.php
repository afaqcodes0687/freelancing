@extends('backend.layout.master')
@section('title', __('Affiliate Programme Settings'))
@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="customMarkup__single__item__flex">
                            <h4 class="customMarkup__single__title">{{ __('Affiliate Programme Settings') }}</h4>
                        </div>
                        <div class="customMarkup__single__inner mt-4">
                            <form action="{{ route('admin.affiliate-programme.update') }}" method="POST">
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
                                                    <input type="text" name="title" id="title"  class="form-control" 
                                                           value="{{ $affiliateProgram->title ?? 'Affiliate Programme' }}"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="meta_title">{{ __('Meta Title') }}</label>
                                                    <input type="text" name="meta_title" id="meta_title" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->meta_title ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="meta_description">{{ __('Meta Description') }}</label>
                                                    <textarea name="meta_description" id="meta_description" 
                                                              class="form-control" rows="3">{{ $affiliateProgram->meta_description ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="meta_keywords">{{ __('Meta Keywords') }}</label>
                                                    <input type="text" name="meta_keywords" id="meta_keywords" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->meta_keywords ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hero Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ __('Hero Section') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="banner_title">{{ __('Banner Title') }}</label>
                                                    <input type="text" name="banner_title" id="banner_title" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->banner_title ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="hero_button_text">{{ __('Hero Button Text') }}</label>
                                                    <input type="text" name="hero_button_text" id="hero_button_text" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->hero_button_text ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="hero_title">{{ __('Hero Title') }}</label>
                                                    <input type="text" name="hero_title" id="hero_title" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->hero_title ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="hero_subtitle">{{ __('Hero Subtitle') }}</label>
                                                    <textarea name="hero_subtitle" id="hero_subtitle" 
                                                              class="form-control" rows="3">{{ $affiliateProgram->hero_subtitle ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="hero_image">{{ __('Hero Image') }}</label>
                                                    <input type="text" name="hero_image" id="hero_image" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->hero_image ?? '' }}">
                                                    <small class="text-muted">{{ __('Image path or URL') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Steps Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ __('Easy Start Steps') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="easy_start_title">{{ __('Easy Start Title') }}</label>
                                                    <input type="text" name="easy_start_title" id="easy_start_title" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->easy_start_title ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <h6 class="mt-4 mb-3">{{ __('Step 1' )}}</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="step1_title">{{ __('Step 1 Title') }}</label>
                                                    <input type="text" name="step1_title" id="step1_title" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->step1_title ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="step1_subtitle">{{ __('Step 1 Subtitle') }}</label>
                                                    <input type="text" name="step1_subtitle" id="step1_subtitle" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->step1_subtitle ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="step1_image">{{ __('Step 1 Image') }}</label>
                                                    <input type="text" name="step1_image" id="step1_image" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->step1_image ?? '' }}">
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="mt-4 mb-3">{{ __('Step 2' )}}</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="step2_title">{{ __('Step 2 Title') }}</label>
                                                    <input type="text" name="step2_title" id="step2_title" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->step2_title ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="step2_subtitle">{{ __('Step 2 Subtitle') }}</label>
                                                    <input type="text" name="step2_subtitle" id="step2_subtitle" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->step2_subtitle ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="step2_image">{{ __('Step 2 Image') }}</label>
                                                    <input type="text" name="step2_image" id="step2_image" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->step2_image ?? '' }}">
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="mt-4 mb-3">{{ __('Step 3' )}}</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="step3_title">{{ __('Step 3 Title') }}</label>
                                                    <input type="text" name="step3_title" id="step3_title" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->step3_title ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="step3_subtitle">{{ __('Step 3 Subtitle') }}</label>
                                                    <input type="text" name="step3_subtitle" id="step3_subtitle" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->step3_subtitle ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="step3_image">{{ __('Step 3 Image') }}</label>
                                                    <input type="text" name="step3_image" id="step3_image" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->step3_image ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Benefits Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ __('Benefits Section') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="benefits_title">{{ __('Benefits Title') }}</label>
                                                    <input type="text" name="benefits_title" id="benefits_title" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->benefits_title ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <h6 class="mt-4 mb-3">{{ __('Commissions' )}}</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="commission_title">{{ __('Commission Title') }}</label>
                                                    <input type="text" name="commission_title" id="commission_title" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->commission_title ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="commission_content">{{ __('Commission Content') }}</label>
                                                    <textarea name="commission_content" id="commission_content" 
                                                              class="form-control" rows="3">{{ $affiliateProgram->commission_content ?? '' }}</textarea>
                                                    <small class="text-muted">{{ __('HTML allowed') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="commission_image">{{ __('Commission Image') }}</label>
                                                    <input type="text" name="commission_image" id="commission_image" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->commission_image ?? '' }}">
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="mt-4 mb-3">{{ __('Support' )}}</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="support_title">{{ __('Support Title') }}</label>
                                                    <input type="text" name="support_title" id="support_title" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->support_title ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="support_content">{{ __('Support Content') }}</label>
                                                    <textarea name="support_content" id="support_content" 
                                                              class="form-control" rows="3">{{ $affiliateProgram->support_content ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="support_image">{{ __('Support Image') }}</label>
                                                    <input type="text" name="support_image" id="support_image" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->support_image ?? '' }}">
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="mt-4 mb-3">{{ __('Resources' )}}</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="resources_title">{{ __('Resources Title') }}</label>
                                                    <input type="text" name="resources_title" id="resources_title" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->resources_title ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="resources_content">{{ __('Resources Content') }}</label>
                                                    <textarea name="resources_content" id="resources_content" 
                                                              class="form-control" rows="3">{{ $affiliateProgram->resources_content ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="resources_image">{{ __('Resources Image') }}</label>
                                                    <input type="text" name="resources_image" id="resources_image" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->resources_image ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Why Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ __('Why Right Freelancer Section') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="why_title">{{ __('Why Title') }}</label>
                                                    <input type="text" name="why_title" id="why_title" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->why_title ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="why_subtitle">{{ __('Why Subtitle') }}</label>
                                                    <input type="text" name="why_subtitle" id="why_subtitle" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->why_subtitle ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="why_content">{{ __('Why Content') }}</label>
                                                    <textarea name="why_content" id="why_content" 
                                                              class="form-control" rows="3">{{ $affiliateProgram->why_content ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="why_image">{{ __('Why Image') }}</label>
                                                    <input type="text" name="why_image" id="why_image" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->why_image ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Statistics Section -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ __('Statistics Section') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stats1_number">{{ __('Stats 1 Number') }}</label>
                                                    <input type="text" name="stats1_number" id="stats1_number" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->stats1_number ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stats1_text">{{ __('Stats 1 Text') }}</label>
                                                    <input type="text" name="stats1_text" id="stats1_text" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->stats1_text ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stats2_number">{{ __('Stats 2 Number') }}</label>
                                                    <input type="text" name="stats2_number" id="stats2_number" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->stats2_number ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stats2_text">{{ __('Stats 2 Text') }}</label>
                                                    <input type="text" name="stats2_text" id="stats2_text" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->stats2_text ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stats3_number">{{ __('Stats 3 Number') }}</label>
                                                    <input type="text" name="stats3_number" id="stats3_number" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->stats3_number ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stats3_text">{{ __('Stats 3 Text') }}</label>
                                                    <input type="text" name="stats3_text" id="stats3_text" 
                                                           class="form-control" 
                                                           value="{{ $affiliateProgram->stats3_text ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> {{ __('Save Changes') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
