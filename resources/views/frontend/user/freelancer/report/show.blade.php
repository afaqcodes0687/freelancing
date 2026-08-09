@extends('frontend.layout.master')

@section('site_title',__('Report Details'))

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('Report Details')" :innerTitle="__('Report Details')"/>
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
                                        <x-form.form-title :title="__('Report Details')" :class="'single-profile-settings-header-title'" />
                                    </div>
                                </div>
                                <div class="single-profile-settings-inner profile-border-top">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group mb-4">
                                                <label>{{ __('Report ID') }}</label>
                                                <p class="form-control">#{{ $report->id }}</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group mb-4">
                                                <label>{{ __('Title') }}</label>
                                                <p class="form-control">{{ $report->title }}</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group mb-4">
                                                <label>{{ __('Description') }}</label>
                                                <p class="form-control">{{ $report->description }}</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group mb-4">
                                                <label>{{ __('Status') }}</label>
                                                <p class="form-control">
                                                    @if($report->status == 0)
                                                        <span class="badge bg-warning">{{ __('In Review') }}</span>
                                                    @elseif($report->status == 1)
                                                        <span class="badge bg-success">{{ __('Closed') }}</span>
                                                    @elseif($report->status == 2)
                                                        <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group mb-4">
                                                <label>{{ __('Submitted On') }}</label>
                                                <p class="form-control">{{ $report->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                        @if($report->order)
                                        <div class="col-lg-6">
                                            <div class="form-group mb-4">
                                                <label>{{ __('Related Order') }}</label>
                                                <p class="form-control">{{ $report->order->order_number }}</p>
                                            </div>
                                        </div>
                                        @endif
                                        @if($report->note)
                                        <div class="col-lg-12">
                                            <div class="form-group mb-4">
                                                <label>{{ __('Admin Note') }}</label>
                                                <p class="form-control">{{ $report->note }}</p>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <a href="{{ route('freelancer.reports.all') }}" class="btn btn-secondary">{{ __('Back to Reports') }}</a>
                                            </div>
                                        </div>
                                    </div>
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
