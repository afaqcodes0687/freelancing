@extends('frontend.layout.master')
@section('site_title', __('My Orders'))
@section('style')
    <x-select2.select2-css />
@endsection
@section('content')
    <main>
        @if(moduleExists('CoinPaymentGateway'))@else<x-frontend.category.category/>@endif
        <x-breadcrumb.user-profile-breadcrumb :title="__('My Orders')" :innerTitle="__('My Orders')" />

        <!-- Profile Details area Starts -->
        <div class="profile-area pat-100 pab-100 section-bg-2">
            <div class="container">
                <div class="row gy-4 justify-content-center">
                    <div class="@if(get_static_option('project_enable_disable') != 'disable') col-xl-8 col-lg-9 @else col-12 @endif">
                        <div class="shop-contents-wrapper-right">
                            <div class="myOrder-wrapper">
                                <div class="myOrder-wrapper-tabs">
                                    <div class="tabs">
                                        @include('frontend.user.client.order.order-count')
                                    </div>
                                    <div class="myOrder-tab-content">
                                        <div class="tab-content-item active">
                                            <x-notice.general-notice :description="__('Notice: The admin has the ability to update the payment status for transactions that are pending.')" />
                                            <div class="search_result">
                                                @include('frontend.user.client.order.search-result')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(get_static_option('project_enable_disable') != 'disable')
                    <div class="col-xl-4 col-lg-7">
                        <div class="profile-details-widget sticky_top_lg">
                            <div class="file-wrapper-item-flex flex-between align-items-center profile-border-bottom">
                                <h4 class="profile-wrapper-item-title"> {{ __('Project Catalogues') }} </h4>
                                <a href="{{ route('projects.all') }}" class="profile-wrapper-item-browse-btn"> {{ __('Browse All') }}</a>
                            </div>
                             @if($top_projects->count() > 0)
                                @foreach($top_projects as $project)
                                    <div class="project-category-item radius-10">
                                        <div class="single-project project-catalogue">

                                            {{-- Project Thumbnail --}}
                                            <div class="single-project-thumb">
                                                @php
                                                    $raw = $project->image;
                                                    $files = is_array($raw)
                                                        ? $raw
                                                        : (json_decode($raw) !== null && json_last_error() === JSON_ERROR_NONE
                                                            ? json_decode($raw, true)
                                                            : [$raw]);
                                                    $isCloud = cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3','cloudFlareR2','wasabi']);
                                                @endphp

                                                @if(!empty($files))
                                                    @foreach($files as $index => $file)
                                                        @php
                                                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                            $fileUrl = $isCloud
                                                                ? render_frontend_cloud_image_if_module_exists('project/' . $file, load_from: $project->load_from ?? '')
                                                                : asset('assets/uploads/project/' . $file);

                                                            // Show only first file in card
                                                            if ($index > 0) break;
                                                        @endphp

                                                        {{-- Show image --}}
                                                        @if(in_array($ext, ['jpg','jpeg','png','webp','svg']))
                                                            <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                                                <img src="{{ $fileUrl }}" alt="{{ $project->title ?? '' }}" style="width:100%; height:240px; object-fit:cover;">
                                                            </a>

                                                        {{-- Show video --}}
                                                        @elseif(in_array($ext, ['mp4','mov','webm','mkv']))
                                                            <video controls style="width:100%; height:240px; object-fit:cover;">
                                                                <source src="{{ $fileUrl }}" type="video/{{ $ext }}">
                                                                {{ __('Your browser does not support the video tag.') }}
                                                            </video>

                                                        {{-- Show doc/pdf/excel --}}
                                                        @else
                                                            <a href="{{ $fileUrl }}" target="_blank" class="d-block text-center p-3 border rounded bg-light">
                                                                @if($ext == 'pdf')
                                                                    <img src="{{ asset('assets/icons/pdf-icon.png') }}" alt="PDF" style="width:60px;">
                                                                @elseif(in_array($ext, ['doc','docx']))
                                                                    <img src="{{ asset('assets/icons/word-icon.png') }}" alt="Word" style="width:60px;">
                                                                @elseif(in_array($ext, ['xls','xlsx','csv']))
                                                                    <img src="{{ asset('assets/icons/excel-icon.png') }}" alt="Excel" style="width:60px;">
                                                                @else
                                                                    <i class="fa fa-file text-muted fa-2x"></i><br>
                                                                    <small>{{ $file }}</small>
                                                                @endif
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <img src="{{ asset('assets/uploads/project/project-default-logo.jpeg') }}" alt="default" style="width:100%; height:240px; object-fit:cover;">
                                                @endif
                                            </div>

                                            {{-- Project Content --}}
                                            <div class="single-project-content">
                                                <div class="single-project-content-top align-items-center flex-between">
                                                    {!! project_rating($project->id) !!}
                                                </div>
                                                <h4 class="single-project-content-title">
                                                    <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                                        {{ $project->title }}
                                                    </a>
                                                </h4>
                                            </div>

                                            {{-- Price + Delivery --}}
                                            <div class="single-project-bottom flex-between">
                                                <span class="single-project-content-price">
                                                    @if($project->basic_discount_charge)
                                                        {{ float_amount_with_currency_symbol($project->basic_discount_charge) }}
                                                        <s>{{ float_amount_with_currency_symbol($project->basic_regular_charge) }}</s>
                                                    @else
                                                        {{ float_amount_with_currency_symbol($project->basic_regular_charge) }}
                                                    @endif
                                                </span>
                                                <div class="single-project-delivery">
                                                    <span class="single-project-delivery-icon">
                                                        <i class="fa-regular fa-clock"></i>{{ __('Delivery') }}
                                                    </span>
                                                    <span class="single-project-delivery-days">{{ $project->basic_delivery }}</span>
                                                </div>
                                            </div>

                                            {{-- Bottom Actions --}}
                                            <div class="project-category-item-bottom profile-border-top">
                                                <div class="project-category-item-bottom-flex flex-between align-items-center">
                                                    <div class="project-category-right-flex flex-btn">
                                                        <x-frontend.bookmark :identity="$project->id" :type="'project'" />
                                                    </div>
                                                    <div class="project-category-item-btn flex-btn">
                                                        <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}" class="btn-profile btn-outline-1">
                                                            {{ __('Order Now') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Profile Details area end -->
    </main>
@endsection

@section('script')
    <x-sweet-alert.sweet-alert2-js />
    <x-select2.select2-js />
    <script src="{{ asset('assets/frontend/js/mdb.min.js') }}"></script>
    @include('frontend.user.client.order.order-js')
@endsection
