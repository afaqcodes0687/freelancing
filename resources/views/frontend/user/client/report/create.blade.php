@extends('frontend.layout.master')

@section('site_title',__('Submit Report'))

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('Submit Report')" :innerTitle="__('Submit Report')"/>
        <!-- Profile Settings area Starts -->
        <div class="responsive-overlay"></div>
        <div class="profile-settings-area pat-100 pab-100 section-bg-2">
            <div class="container">
                <div class="row g-4">
                    @include('frontend.user.layout.partials.sidebar')
                    <div class="col-xl-9 col-lg-8">
                        <div class="profile-settings-wrapper">
                            <div class="single-profile-settings">
                                <div class="single-profile-settings-header">
                                    <div class="single-profile-settings-header-flex">
                                        <x-form.form-title :title="__('Submit Report to Admin')" :class="'single-profile-settings-header-title'" />
                                    </div>
                                </div>
                                <div class="single-profile-settings-inner profile-border-top">
                                    <form action="{{ route('client.report.store') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group mb-4">
                                                    <label for="title">{{ __('Report Title') }} <span class="text-danger">*</span></label>
                                                    <input type="text" 
                                                           id="title" 
                                                           name="title" 
                                                           class="form-control @error('title') is-invalid @enderror" 
                                                           placeholder="{{ __('e.g., Payment issue') }}" 
                                                           value="{{ old('title') }}" 
                                                           required>
                                                    @error('title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-12">
                                                <div class="form-group mb-4">
                                                    <label for="description">{{ __('Description') }} <span class="text-danger">*</span></label>
                                                    <textarea id="description" 
                                                              name="description" 
                                                              class="form-control @error('description') is-invalid @enderror" 
                                                              placeholder="{{ __('Please provide details about the issue...') }}" 
                                                              rows="5"
                                                              required>{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-12">
                                                <div class="form-group mb-4">
                                                    <label for="order_id">{{ __('Related Order (Optional)') }}</label>
                                                    <input type="text" 
                                                           id="order_id" 
                                                           name="order_id" 
                                                           class="form-control @error('order_id') is-invalid @enderror" 
                                                           placeholder="{{ __('Enter Order ID') }}" 
                                                           value="{{ old('order_id') }}">
                                                    @error('order_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary">{{ __('Submit Report') }}</button>
                                                    <a href="{{ route('client.reports.all') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Profile Settings area end -->
    </main>
@endsection
