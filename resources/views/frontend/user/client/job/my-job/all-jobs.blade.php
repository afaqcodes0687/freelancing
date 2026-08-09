@extends('frontend.layout.master')
@section('site_title',__('All Jobs'))
@section('style')
    <x-summernote.summernote-css/>
    <x-select2.select2-css/>
    <style>
        .disabled-link {
            background-color: #ccc !important;
            pointer-events: none;
            cursor: default;
        }
        .slider-container { position: relative; overflow: hidden; }
        .slider-item { display: none; transition: opacity 0.5s; }
        .slider-item.active { display: block; }
        .slider-nav-wrapper { text-align: center; margin-top: 4px; }
        .slider-nav {
            background-color: #309400;
            padding: 8px;
            border-radius: 30px;
            display: inline-block;
        }
        .slider-nav button {
            background: rgba(0, 0, 0, 0.4);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 16px;
            line-height: 30px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
            margin: 0 8px;
        }
        .slider-text {
            font-size: 14px;
            color: white;
            margin: 0 8px;
        }
    </style>
@endsection
@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('My Jobs')" :innerTitle="__('My Jobs')"/>
        <!-- Profile Details area Starts -->
        <div class="profile-area pat-100 pab-100 section-bg-2">
            <div class="container">
                <div class="row gy-4 justify-content-center">
                    <div class="@if(get_static_option('project_enable_disable') != 'disable') col-xl-8 col-lg-9 @else col-12 @endif">
                        <div class="profile-wrapper sticky_top_lg">
                            @include('frontend.user.client.job.my-job.header')
                            <div class="search_result">
                                @include('frontend.user.client.job.my-job.search-result')
                            </div>
                        </div>
                    </div>
                    @if(get_static_option('project_enable_disable') != 'disable')
                    <div class="col-xl-4 col-lg-7">
                        <div class="profile-details-widget sticky_top_lg">
                            <div class="file-wrapper-item-flex flex-between align-items-center profile-border-bottom">
                                <h4 class="profile-wrapper-item-title"> {{ __('Project Catalogues') }} </h4>
                                <a href="{{ route('projects.all') }}" class="profile-wrapper-item-browse-btn"> {{ __('Browse All ') }}</a>
                            </div>
                            @if($top_projects->count() > 0)
                                @foreach($top_projects as $project)
                                    <div class="project-category-item radius-10">
                                        <div class="single-project project-catalogue">
                                            <div class="single-project-thumb">
                                                @php
                                                    if (!empty($project->image)) {
                                                        $decoded = json_decode($project->image, true);
                                                        if (is_array($decoded)) {
                                                            $projectFiles = $decoded;
                                                        } elseif (is_string($project->image)) {
                                                            $projectFiles = [$project->image];
                                                        } else {
                                                            $projectFiles = [];
                                                        }
                                                    } else {
                                                        $projectFiles = [];
                                                    }
                                                    $defaultImage = asset('assets/uploads/project/project-default-logo.jpeg');
                                                    $isCloud = cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']);
                                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'svg', 'bmp', 'tiff', 'webp'];
                                                    $videoExtensions = ['mp4', 'mov', 'avi', 'mkv'];
                                                    $docExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];
                                                @endphp
                                                @if(count($projectFiles) > 1)
                                                    <div class="slider-container" id="slider-{{ $project->id }}">
                                                        @foreach($projectFiles as $projectImage)
                                                            <div class="slider-item {{ $loop->first ? 'active' : '' }}">
                                                                <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                                                    @php
                                                                        $fileExtension = strtolower(pathinfo($projectImage, PATHINFO_EXTENSION));
                                                                        $filePath = $isCloud
                                                                            ? render_frontend_cloud_image_if_module_exists('project/' . $projectImage, load_from: $project->load_from)
                                                                            : asset('assets/uploads/project/' . $projectImage);
                                                                    @endphp
                                                                    @if(in_array($fileExtension, $imageExtensions))
                                                                        <img src="{{ $filePath }}" alt="{{ $project->title }}" style="max-height:160px;object-fit:cover;width:100%;">
                                                                    @elseif(in_array($fileExtension, $videoExtensions))
                                                                        <video muted loop autoplay style="max-height:160px;width:100%;object-fit:cover;">
                                                                            <source src="{{ $filePath }}" type="video/{{ $fileExtension }}">
                                                                            {{ __('Your browser does not support the video tag.') }}
                                                                        </video>
                                                                    @elseif(in_array($fileExtension, $docExtensions))
                                                                        <div class="file-name-box" style="height:100%;text-align:center;">
                                                                            <i class="fa fa-file-alt me-2" style="font-size: 48px; color: #2b7a78;"></i><br>
                                                                            <span class="file-name-text">{{ basename($projectImage) }}</span>
                                                                        </div>
                                                                    @else
                                                                        <img src="{{ $defaultImage }}" alt="default-avatar" style="max-height:160px;object-fit:cover;width:100%;">
                                                                    @endif
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif(count($projectFiles) === 1)
                                                    @php
                                                        $projectImage = $projectFiles[0];
                                                        $fileExtension = strtolower(pathinfo($projectImage, PATHINFO_EXTENSION));
                                                        $filePath = $isCloud
                                                            ? render_frontend_cloud_image_if_module_exists('project/' . $projectImage, load_from: $project->load_from)
                                                            : asset('assets/uploads/project/' . $projectImage);
                                                    @endphp
                                                    <div class="single-file-display">
                                                        <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                                            @if(in_array($fileExtension, $imageExtensions))
                                                                <img src="{{ $filePath }}" alt="{{ $project->title }}" style="max-height:160px;object-fit:cover;width:100%;">
                                                            @elseif(in_array($fileExtension, $videoExtensions))
                                                                <video muted loop autoplay style="max-height:160px;width:100%;object-fit:cover;">
                                                                    <source src="{{ $filePath }}" type="video/{{ $fileExtension }}">
                                                                    {{ __('Your browser does not support the video tag.') }}
                                                                </video>
                                                            @elseif(in_array($fileExtension, $docExtensions))
                                                                <div class="file-name-box" style="height:100%;text-align:center;">
                                                                    <i class="fa fa-file-alt me-2" style="font-size: 48px; color: #2b7a78;"></i><br>
                                                                    <span class="file-name-text">{{ basename($projectImage) }}</span>
                                                                </div>
                                                            @else
                                                                <img src="{{ $defaultImage }}" alt="default-avatar" style="max-height:160px;object-fit:cover;width:100%;">
                                                            @endif
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="single-file-display">
                                                        <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                                            <img src="{{ $defaultImage }}" alt="default-avatar" style="max-height:160px;object-fit:cover;width:100%;">
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            @if(count($projectFiles) > 1)
                                                <div class="slider-nav-wrapper">
                                                    <div class="slider-nav">
                                                        <button class="slider-prev" onclick="navigateSlide('slider-{{ $project->id }}', -1)">&#10094;</button>
                                                        <span class="slider-text swipe-btn">{{ __('Swipe') }}</span>
                                                        <button class="slider-next" onclick="navigateSlide('slider-{{ $project->id }}', 1)">&#10095;</button>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="single-project-content">
                                                <div class="single-project-content-top align-items-center flex-between">
                                                    {!! project_rating($project->id) !!}
                                                </div>
                                                <h4 class="single-project-content-title">
                                                    <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}"> {{ $project->title }} </a>
                                                </h4>
                                            </div>
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
                                                    <span class="single-project-delivery-icon"> <i class="fa-regular fa-clock"></i>{{ __('Delivery') }}</span>
                                                    <span class="single-project-delivery-days"> {{ $project->basic_delivery }} </span>
                                                </div>
                                            </div>
                                            <div class="project-category-item-bottom profile-border-top">
                                                <div class="project-category-item-bottom-flex flex-between align-items-center">
                                                    <div class="project-category-right-flex flex-btn">
                                                        <x-frontend.bookmark :identity="$project->id" :type="'project'" />
                                                    </div>
                                                    <div class="project-category-item-btn flex-btn">
                                                        @if(moduleExists('SecurityManage'))
                                                            @if(Auth::guard('web')->user()->freeze_order_create == 'freeze')
                                                                <a href="#" class="btn-profile btn-outline-1 @if(Auth::guard('web')->user()->freeze_order_create == 'freeze') disabled-link @endif"> {{ __('Order Now') }} </a>
                                                            @else
                                                                <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}" class="btn-profile btn-outline-1"> {{ __('Order Now') }} </a>
                                                            @endif
                                                        @else
                                                            <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}" class="btn-profile btn-outline-1"> {{ __('Order Now') }} </a>
                                                        @endif
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
    <x-summernote.summernote-js/>
    <x-select2.select2-js/>
    <x-sweet-alert.sweet-alert2-js/>
    <script>
        let mainPageUrl = {href: window.location.href};
    </script>

    @include('frontend.user.client.job.my-job.all-jobs-js')
    <script>
    function navigateSlide(sliderId, direction) {
        var container = document.getElementById(sliderId);
        if (!container) return;
        var items = container.querySelectorAll('.slider-item');
        var activeIdx = Array.from(items).findIndex(item => item.classList.contains('active'));
        items[activeIdx].classList.remove('active');
        var nextIdx = (activeIdx + direction + items.length) % items.length;
        items[nextIdx].classList.add('active');
    }
    // Auto-slide for all sliders
    setInterval(function() {
        document.querySelectorAll('.slider-container').forEach(function(container) {
            var items = container.querySelectorAll('.slider-item');
            if (items.length <= 1) return;
            var activeIdx = Array.from(items).findIndex(item => item.classList.contains('active'));
            items[activeIdx].classList.remove('active');
            var nextIdx = (activeIdx + 1) % items.length;
            items[nextIdx].classList.add('active');
        });
    }, 4000);
    </script>
@endsection
