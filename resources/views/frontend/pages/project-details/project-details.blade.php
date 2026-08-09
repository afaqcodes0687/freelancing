@extends('frontend.layout.master')
@section('meta')
    @if(!empty($project))
        <meta name="title" content="{{$project->meta_title}}">
        <meta name="description" content="{{$project->meta_description}}">
    @endif
    {{-- Open Graph tags for social sharing (LinkedIn, Facebook, etc) --}}
    <meta property="og:title" content="{{ $project->meta_title ?? $project->title }}">
    <meta property="og:description"
        content="{{ $project->meta_description ?? Str::limit(strip_tags($project->description), 150) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @php
        $ogFiles = json_decode($project->image, true);
        $ogImage = null;
        $defaultProjectImage = asset('assets/static/img/project-default.png'); // Make sure this exists or use any default project image
        if (!empty($ogFiles) && is_array($ogFiles)) {
            $first = $ogFiles[0];
            $ext = strtolower(pathinfo($first, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'bmp', 'tiff', 'svg', 'webp'])) {
                $ogImage = (cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                    ? render_frontend_cloud_image_if_module_exists('project/' . $first, load_from: $project->load_from)
                    : asset('assets/uploads/project/' . $first);
            } else {
                $ogImage = $defaultProjectImage;
            }
        } else {
            $ogImage = $defaultProjectImage;
        }
    @endphp
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="{{ $project->title }}">
@endsection
@section('site_title')
    {{ $project->title ?? __('Project Preview') }}
@endsection
@if(isset($project->meta_title) && !empty($project->meta_title))
@section('meta_title', $project->meta_title)
@endif

@if(isset($project->meta_description) && !empty($project->meta_description))
@section('meta_description', $project->meta_description)
@endif
@section('style')
    <x-summernote.summernote-css />
    <style>
        .rating_profile_details {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @if(get_static_option('profile_page_badge_settings') == 'enable')
            .level-badge-wrapper {
                top: 10px;
                right: 10px;
            }

            .jobFilter-proposal-author-contents-subtitle {
                padding-left: 16px;
            }

        @endif .disabled-link {
            background-color: #ccc !important;
            pointer-events: none;
            cursor: default;
        }

        .pricing-wrapper-left {
            .pricing-wrapper-card-bottom-list ul li {
                max-height: 50px;
                min-width: 205px;
                align-items: unset;

                span {
                    margin: auto 0;
                }
            }
        }

        [data-star] {
            text-align: left;
            font-style: normal;
            display: inline-block;
            position: relative;
            unicode-bidi: bidi-override;
        }

        [data-star]::before {
            display: block;
            content: "\f005" "\f005" "\f005" "\f005" "\f005";
            width: 100%;
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 15px;
            color: var(--body-color);
        }

        [data-star]::after {
            white-space: nowrap;
            position: absolute;
            top: 0;
            left: 0;
            content: "\f005" "\f005" "\f005" "\f005" "\f005";
            width: 100%;
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 15px;
            ;
            width: 0;
            color: var(--secondary-color);
            overflow: hidden;
            height: 100%;
        }

        [data-star^="0.1"]::after {
            width: 2%
        }

        [data-star^="0.2"]::after {
            width: 4%
        }

        [data-star^="0.3"]::after {
            width: 6%
        }

        [data-star^="0.4"]::after {
            width: 8%
        }

        [data-star^="0.5"]::after {
            width: 10%
        }

        [data-star^="0.6"]::after {
            width: 12%
        }

        [data-star^="0.7"]::after {
            width: 14%
        }

        [data-star^="0.8"]::after {
            width: 16%
        }

        [data-star^="0.9"]::after {
            width: 18%
        }

        [data-star^="1"]::after {
            width: 20%
        }

        [data-star^="1.1"]::after {
            width: 22%
        }

        [data-star^="1.2"]::after {
            width: 24%
        }

        [data-star^="1.3"]::after {
            width: 26%
        }

        [data-star^="1.4"]::after {
            width: 28%
        }

        [data-star^="1.5"]::after {
            width: 30%
        }

        [data-star^="1.6"]::after {
            width: 32%
        }

        [data-star^="1.7"]::after {
            width: 34%
        }

        [data-star^="1.8"]::after {
            width: 36%
        }

        [data-star^="1.9"]::after {
            width: 38%
        }

        [data-star^="2"]::after {
            width: 40%
        }

        [data-star^="2.1"]::after {
            width: 42%
        }

        [data-star^="2.2"]::after {
            width: 44%
        }

        [data-star^="2.3"]::after {
            width: 46%
        }

        [data-star^="2.4"]::after {
            width: 48%
        }

        [data-star^="2.5"]::after {
            width: 50%
        }

        [data-star^="2.6"]::after {
            width: 52%
        }

        [data-star^="2.7"]::after {
            width: 54%
        }

        [data-star^="2.8"]::after {
            width: 56%
        }

        [data-star^="2.9"]::after {
            width: 58%
        }

        [data-star^="3"]::after {
            width: 60%
        }

        [data-star^="3.1"]::after {
            width: 62%
        }

        [data-star^="3.2"]::after {
            width: 64%
        }

        [data-star^="3.3"]::after {
            width: 66%
        }

        [data-star^="3.4"]::after {
            width: 68%
        }

        [data-star^="3.5"]::after {
            width: 70%
        }

        [data-star^="3.6"]::after {
            width: 72%
        }

        [data-star^="3.7"]::after {
            width: 74%
        }

        [data-star^="3.8"]::after {
            width: 76%
        }

        [data-star^="3.9"]::after {
            width: 78%
        }

        [data-star^="4"]::after {
            width: 80%
        }

        [data-star^="4.1"]::after {
            width: 82%
        }

        [data-star^="4.2"]::after {
            width: 84%
        }

        [data-star^="4.3"]::after {
            width: 86%
        }

        [data-star^="4.4"]::after {
            width: 88%
        }

        [data-star^="4.5"]::after {
            width: 90%
        }

        [data-star^="4.6"]::after {
            width: 92%
        }

        [data-star^="4.7"]::after {
            width: 94%
        }

        [data-star^="4.8"]::after {
            width: 96%
        }

        [data-star^="4.9"]::after {
            width: 98%
        }

        [data-star^="5"]::after {
            width: 100%
        }

        /* Professional Gallery Styles */
        .project-gallery {
            position: relative;
            margin-bottom: 2rem;
        }

        .gallery-main {
            position: relative;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1rem;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gallery-main img,
        .gallery-main video {
            max-width: 100%;
            max-height: 500px;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .gallery-main embed {
            width: 100%;
            height: 500px;
            border-radius: 8px;
        }

        .gallery-main .file-preview {
            text-align: center;
            padding: 2rem;
        }

        .gallery-main .file-preview img {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
        }

        .gallery-main .file-preview p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }

        .gallery-thumbnails {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 8px 0;
            scrollbar-width: thin;
            scrollbar-color: #ddd transparent;
        }

        .gallery-thumbnails::-webkit-scrollbar {
            height: 6px;
        }

        .gallery-thumbnails::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .gallery-thumbnails::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .gallery-thumbnails::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .gallery-thumb {
            flex: 0 0 auto;
            width: 80px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }

        .gallery-thumb:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .gallery-thumb.active {
            border-color: #309400;
            box-shadow: 0 0 0 2px rgba(48, 148, 0, 0.2);
        }

        .gallery-thumb img,
        .gallery-thumb video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-thumb .file-icon {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            color: #666;
            font-size: 20px;
        }

        .gallery-controls {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            pointer-events: none;
            z-index: 10;
        }

        .gallery-controls button {
            position: absolute;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            pointer-events: all;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gallery-controls button:hover {
            background: #309400;
            color: white;
            transform: scale(1.1);
        }

        .gallery-controls .prev {
            left: 16px;
        }

        .gallery-controls .next {
            right: 16px;
        }

        .gallery-counter {
            position: absolute;
            bottom: 16px;
            right: 16px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .gallery-fullscreen {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .gallery-fullscreen:hover {
            background: rgba(0, 0, 0, 0.9);
            transform: scale(1.1);
        }

        /* Lightbox Styles */
        .lightbox {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .lightbox.active {
            display: flex;
        }

        .lightbox-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            text-align: center;
        }

        .lightbox-content img,
        .lightbox-content video {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
        }

        .lightbox-content embed {
            width: 80vw;
            height: 80vh;
        }

        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .lightbox-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            pointer-events: none;
        }

        .lightbox-nav button {
            position: absolute;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            cursor: pointer;
            pointer-events: all;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .lightbox-nav button:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .lightbox-nav .prev {
            left: 20px;
        }

        .lightbox-nav .next {
            right: 20px;
        }

        .lightbox-counter {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-size: 16px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .gallery-controls button {
                width: 40px;
                height: 40px;
            }

            .gallery-controls .prev {
                left: 8px;
            }

            .gallery-controls .next {
                right: 8px;
            }

            .lightbox-nav button {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }

        .modal-content.radius-10 {
            border-radius: 10px;
        }

        .btn-sm {
            padding: 3px 10px;
            font-size: 13px;
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            padding: 0;
            font-size: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endsection
@section('content')
    <main>
        @if(moduleExists('CoinPaymentGateway'))@else<x-frontend.category.category />@endif
        <x-breadcrumb.user-profile-breadcrumb :title="__('Project Details')" :innerTitle="__('Project Details')" />
        <!-- Project preview area Starts -->
        <div class="preview-area section-bg-2 pat-100 pab-100">
            <div class="container">
                <div class="row g-4">
                    <div class="col-xl-7 col-lg-7">
                        @php
                            $files = json_decode($project->image, true);
                            $cloud = cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']);
                            $dummy = 'https://ssl.gstatic.com/docs/doclist/images/mediatype/icon_1_document_x16.png';
                        @endphp

                        @if(!empty($files) && is_array($files))
                            <!-- Professional Gallery -->
                            <div class="project-gallery">
                                <div class="gallery-main" id="galleryMain">
                                    @php
                                        $firstFile = $files[0];
                                        $ext = strtolower(pathinfo($firstFile, PATHINFO_EXTENSION));
                                        $fp = $cloud
                                            ? render_frontend_cloud_image_if_module_exists('project/' . $firstFile, load_from: $project->load_from)
                                            : asset('assets/uploads/project/' . $firstFile);
                                    @endphp

                                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'bmp', 'tiff', 'svg', 'webp']))
                                        <img src="{{ $fp }}" alt="{{ $project->title }}" id="mainImage">
                                    @elseif(in_array($ext, ['mp4', 'mov', 'avi', 'wmv', 'mkv']))
                                        <video controls id="mainVideo">
                                            <source src="{{ $fp }}" type="video/{{ $ext }}">
                                        </video>
                                    @elseif($ext === 'pdf')
                                        <embed src="{{ $fp }}" type="application/pdf" id="mainPdf">
                                    @else
                                        <div class="file-preview">
                                            <img src="{{ $dummy }}" alt="File">
                                            <p>{{ basename($firstFile) }}</p>
                                        </div>
                                    @endif

                                    <div class="gallery-counter">
                                        <span id="currentIndex">1</span> / <span id="totalCount">{{ count($files) }}</span>
                                    </div>

                                    <button class="gallery-fullscreen" onclick="openLightbox(0)">
                                        <i class="fas fa-expand"></i>
                                    </button>
                                </div>

                                <div class="gallery-controls">
                                    <button class="prev" onclick="changeSlide(-1)">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button class="next" onclick="changeSlide(1)">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>

                                <div class="gallery-thumbnails">
                                    @foreach($files as $i => $file)
                                        @php
                                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $fp = $cloud
                                                ? render_frontend_cloud_image_if_module_exists('project/' . $file, load_from: $project->load_from)
                                                : asset('assets/uploads/project/' . $file);
                                        @endphp
                                        <div class="gallery-thumb {{ $i === 0 ? 'active' : '' }}"
                                            onclick="changeMainContent({{ $i }})" data-index="{{ $i }}">
                                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'bmp', 'tiff', 'svg', 'webp']))
                                                <img src="{{ $fp }}" alt="Thumbnail">
                                            @elseif(in_array($ext, ['mp4', 'mov', 'avi', 'wmv', 'mkv']))
                                                <video muted>
                                                    <source src="{{ $fp }}" type="video/{{ $ext }}">
                                                </video>
                                            @elseif($ext === 'pdf')
                                                <div class="file-icon">
                                                    <i class="fas fa-file-pdf"></i>
                                                </div>
                                            @elseif(in_array($ext, ['doc', 'docx']))
                                                <div class="file-icon">
                                                    <i class="fas fa-file-word"></i>
                                                </div>
                                            @elseif(in_array($ext, ['xls', 'xlsx']))
                                                <div class="file-icon">
                                                    <i class="fas fa-file-excel"></i>
                                                </div>
                                            @else
                                                <div class="file-icon">
                                                    <i class="fas fa-file"></i>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Project Content -->
                        <div class="project-preview-contents mt-4">
                            <div class="single-project-content-top align-items-center flex-between">
                                {!! project_rating($project->id) !!}
                            </div>
                            <h1 class="project-preview-contents-title mt-3 d-flex align-items-center">
                                {{ $project->title }}
                                {{-- Only show Share Project button if user is logged in AND freelancer is online --}}
                                @if(auth()->check() && Cache::has('user_is_online_' . $user->id))
                                    <button type="button" class="btn-profile btn-bg-1 btn-sm ms-3" data-bs-toggle="modal"
                                        data-bs-target="#shareProjectModal">
                                        <i class="fas fa-share-alt me-1"></i> {{ __('Share Project') }}
                                    </button>
                                @endif
                            </h1>
                            <!-- <h1 class="project-preview-contents-title mt-3">{{ $project->title }}</h1> -->
                            <div class="text-muted"
                                style="max-height:180px; overflow:auto; text-align:left; margin-bottom:10px;">
                                {!! $project->description !!}
                            </div>
                        </div>
                        <div class="project-preview">
                            <div class="myJob-wrapper-single-flex flex-between align-items-center">
                                <div class="myJob-wrapper-single-contents">
                                    <div class="jobFilter-proposal-author-flex">
                                        <div class="jobFilter-proposal-author-thumb position-relative">
                                            @if ($user->image)
                                                <a href="{{ route('freelancer.profile.details', $user->username) }}">
                                                    @if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                                                        <img src="{{ render_frontend_cloud_image_if_module_exists('profile/' . $user->image, load_from: $user->load_from) }}"
                                                            alt="{{ $user->first_name ?? '' }}">
                                                    @else
                                                        <img src="{{ asset('assets/uploads/profile/' . $user->image) }}"
                                                            alt="{{ $user->first_name }}">
                                                    @endif
                                                </a>
                                                @if(moduleExists('FreelancerLevel'))
                                                    @if(get_static_option('profile_page_badge_settings') == 'enable')
                                                        <div class="freelancer-level-badge position-absolute">
                                                            {!! freelancer_level($user->id, 'talent') ?? '' !!}
                                                        </div>
                                                    @endif
                                                @endif

                                                <div class="position-absolute" style="top: 60px; right: -18px;">
                                                    <div class="profile-status-icon" style="
                                                                            position: relative;
                                                                            width: 24px;
                                                                            height: 24px;
                                                                            border-radius: 50%;
                                                                            top: -60px;
                                                                            right: 66px;">
                                                        <i class="fas fa-circle" style="
                                                                                font-size: 12px;
                                                                                border: 3px solid #fff;
                                                                                border-radius:50%;
                                                                                color: {{ Cache::has('user_is_online_' . $user->id) ? '#28a745' : '#6c757d' }};
                                                                            "></i>
                                                    </div>
                                                </div>

                                            @else
                                                <a href="{{ route('freelancer.profile.details', $user->username) }}">
                                                    <img src="{{ asset('assets/static/img/author/author.jpg') }}"
                                                        alt="{{ __('AuthorImg') }}">
                                                </a>
                                                @if(moduleExists('FreelancerLevel'))
                                                    @if(get_static_option('profile_page_badge_settings') == 'enable')
                                                        <div class="freelancer-level-badge position-absolute">
                                                            {!! freelancer_level($user->id, 'talent') ?? '' !!}
                                                        </div>
                                                    @endif
                                                @endif
                                                <div class="position-absolute" style="top: 60px; right: -18px;">
                                                    <div class="profile-status-icon" style="
                                                                            position: relative;
                                                                            width: 24px;
                                                                            height: 24px;
                                                                            border-radius: 50%;
                                                                            top: -13px;
                                                                            right: 12px;">
                                                        <i class="fas fa-circle" style="
                                                                                font-size: 12px;
                                                                                border: 3px solid #fff;
                                                                                border-radius:50%;
                                                                                color: {{ Cache::has('user_is_online_' . $user->id) ? '#28a745' : '#6c757d' }};
                                                                            "></i>
                                                    </div>
                                                </div>
                                            @endif
                                            @php
                                                $averageRating = round(optional($user->freelancer_ratings)->avg('rating'), 1);
                                            @endphp

                                            @if ($averageRating >= 4.5)
                                                <div
                                                    style="position: absolute; bottom: 0px; right: 0px; width: 50px; height: 50px; left: 43px; top: 30px;">
                                                    <img src="{{ asset('assets/uploads/profile/badge.png') }}"
                                                        alt="Rated Plus Badge" style="width: 100%; height: 100%;">
                                                    <span class="badge-title pb-3">+</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="jobFilter-proposal-author-contents">
                                            <h4 class="single-freelancer-author-name">
                                                <a href="{{ route('freelancer.profile.details', $user->username) }}">{{ $user->first_name }}
                                                    {{ $user->last_name }}@if(moduleExists('FreelancerLevel'))<small>{{ freelancer_level($user->id) }}</small>@endif
                                                </a>
                                                <!-- @if(Cache::has('user_is_online_' . $user->id))
                                                            <span class="single-freelancer-author-status"> {{ __('Online') }} </span>
                                                        @else
                                                            <span class="single-freelancer-author-status-ofline"> {{ __('Offline') }} </span>
                                                        @endif -->
                                            </h4>
                                            <p class="jobFilter-proposal-author-contents-subtitle mt-2">
                                                @if($user->user_introduction?->title)
                                                    {{ $user->user_introduction?->title }} ·
                                                @endif
                                                <span>
                                                    @if($user->user_state?->state)
                                                        {{ $user->user_state?->state }},
                                                    @endif
                                                    {{ $user->user_country?->country }}
                                                </span>
                                                @if($user->user_verified_status == 1) <i class="fas fa-circle-check"
                                                style="color:#1E90FF"></i>@endif
                                            </p>
                                            <div class="jobFilter-proposal-author-contents-review mt-2"
                                                style="margin-left: 10px;">
                                                {!! freelancer_rating($user->id) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if (Auth::guard('web')->check() && Auth::guard('web')->user()->id != $project->user_id)
                                    <div class="btn-wrapper">
                                        <form action="{{ route('client.message.send') }}" method="post"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="freelancer_id" id="freelancer_id"
                                                value="{{ $project->user_id }}">
                                            <input type="hidden" name="from_user" id="from_user"
                                                value="{{ Auth::guard('web')->user()->id }}">
                                            <input type="hidden" name="project_id" id="project_id" value="{{ $project->id }}">
                                            <button type="submit" class="btn-profile btn-bg-1">
                                                <i class="fa-regular fa-comments"></i> {{ __('Contact Me') }}</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if (!empty($project->standard_title) && !empty($project->premium_title))
                            <div class="project-preview" id="comparePackage">
                                <div class="project-preview-head profile-border-bottom">
                                    <h4 class="project-preview-head-title"> {{ __('Compare Packages') }} </h4>
                                </div>
                                <div class="pricing-wrapper d-flex flex-wrap">
                                    <!-- left wrapper -->
                                    <div class="pricing-wrapper-left">
                                        <div class="pricing-wrapper-card mb-30">
                                            <div class="pricing-wrapper-card-top">
                                            </div>
                                            <div class="pricing-wrapper-card-bottom">
                                                <div class="pricing-wrapper-card-bottom-list">
                                                    <ul class="list-style-none">
                                                        <li><span>{{ __('Revisions') }}</span></li>
                                                        <li><span>{{ __('Delivery time') }}</span></li>
                                                        <li><span>{{ __('Package Details') }}</span></li>
                                                        @foreach ($project->project_attributes as $attr)
                                                            <li><span>{{ $attr->check_numeric_title }}</span></li>
                                                        @endforeach

                                                        <li><span>{{ __('Charges') }}</span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pricing-wrapper-right d-flex flex-wrap">
                                        @if ($project->basic_title)
                                            <div class="pricing-wrapper-card text-center">
                                                <div class="pricing-wrapper-card-top">
                                                    <h2 class="pricing-wrapper-card-top-prices">
                                                        {{ $project->basic_title }}
                                                    </h2>
                                                </div>
                                                <div class="pricing-wrapper-card-bottom">
                                                    <div class="pricing-wrapper-card-bottom-list">
                                                        <ul class="list-style-none">
                                                            <li><span class="close-icon">{{ $project->basic_revision }}</span></li>
                                                            <li><span class="close-icon">{{ __($project->basic_delivery) }}</span>
                                                            </li>
                                                            <li>
                                                                <span class="close-icon" style="max-height: 3.4em; /* Approx. 1.5 lines */
                                                                                        overflow-y: auto;">
                                                                    {{ $project->basic_details }}
                                                                </span>
                                                            </li>
                                                            @foreach ($project->project_attributes as $attr)
                                                                @if ($attr->standard_check_numeric == 'on')
                                                                    <li><span class="check-icon"> <i class="fas fa-check"></i>
                                                                        </span></li>
                                                                @elseif($attr->standard_check_numeric == 'off')
                                                                    <li><span class="close-icon"> <i class="fas fa-times"></i>
                                                                        </span></li>
                                                                @else
                                                                    <li>
                                                                        <span class="close-icon">
                                                                            {{ $attr->standard_check_numeric }}
                                                                        </span>
                                                                    </li>
                                                                @endif
                                                            @endforeach

                                                            <li>
                                                                <div class="price">
                                                                    @if ($project->basic_discount_charge != null && $project->basic_discount_charge > 0)
                                                                        <h6 class="price-main">
                                                                            {{ amount_with_currency_symbol($project->basic_discount_charge) }}
                                                                        </h6>
                                                                        <s
                                                                            class="price-old">{{ amount_with_currency_symbol($project->basic_regular_charge) }}</s>
                                                                    @else
                                                                        <h6 class="price-main">
                                                                            {{ amount_with_currency_symbol($project->basic_regular_charge) }}
                                                                        </h6>
                                                                    @endif
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                            </div>
                                        @endif
                                        <div class="pricing-wrapper-card text-center">
                                            <div class="pricing-wrapper-card-top">
                                                <h2 class="pricing-wrapper-card-top-prices">
                                                    {{ $project->standard_title }}
                                                </h2>
                                            </div>

                                            <div class="pricing-wrapper-card-bottom">
                                                <div class="pricing-wrapper-card-bottom-list">
                                                    <ul class="list-style-none">
                                                        <li><span class="close-icon">
                                                                {{ $project->standard_revision }}</span>
                                                        </li>
                                                        <li><span class="close-icon">{{ __($project->standard_delivery) }}
                                                            </span>
                                                        </li>
                                                        <li>
                                                            <span class="close-icon" style="max-height: 3.4em; /* Approx. 1.5 lines */
                                                                                overflow-y: auto;">
                                                                {{ $project->standard_details }}
                                                            </span>
                                                        </li>
                                                        @foreach ($project->project_attributes as $attr)
                                                            @if ($attr->standard_check_numeric == 'on')
                                                                <li><span class="check-icon"> <i class="fas fa-check"></i>
                                                                    </span></li>
                                                            @elseif($attr->standard_check_numeric == 'off')
                                                                <li><span class="close-icon"> <i class="fas fa-times"></i>
                                                                    </span></li>
                                                            @else
                                                                <li>
                                                                    <span class="close-icon">
                                                                        {{ $attr->standard_check_numeric }}
                                                                    </span>
                                                                </li>
                                                            @endif
                                                        @endforeach

                                                        <li>
                                                            <div class="price">
                                                                @if ($project->standard_discount_charge != null && $project->standard_discount_charge > 0)
                                                                    <h6 class="price-main">
                                                                        {{ amount_with_currency_symbol($project->standard_discount_charge) }}
                                                                    </h6>
                                                                    <s class="price-old">
                                                                        {{ amount_with_currency_symbol($project->standard_regular_charge ?? '') }}</s>
                                                                @else
                                                                    <h6 class="price-main">
                                                                        {{ amount_with_currency_symbol($project->standard_regular_charge ?? '') }}
                                                                    </h6>
                                                                @endif
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-wrapper-card text-center">
                                            <div class="pricing-wrapper-card-top">
                                                <h2 class="pricing-wrapper-card-top-prices">{{ $project->premium_title }}
                                                </h2>
                                            </div>
                                            <div class="pricing-wrapper-card-bottom">
                                                <div class="pricing-wrapper-card-bottom-list">
                                                    <ul class="list-style-none">
                                                        <li><span class="close-icon"> {{ $project->premium_revision }}
                                                            </span>
                                                        </li>
                                                        <li><span class="close-icon">{{ __($project->premium_delivery) }}</span>
                                                        </li>
                                                        <li>
                                                            <span class="close-icon" style="max-height: 3.4em; /* Approx. 1.5 lines */
                                                                                overflow-y: auto;">
                                                                {{ $project->premium_details }}
                                                            </span>
                                                        </li>
                                                        @foreach ($project->project_attributes as $attr)
                                                            @if ($attr->premium_check_numeric == 'on')
                                                                <li><span class="check-icon"> <i class="fas fa-check"></i>
                                                                    </span></li>
                                                            @elseif($attr->premium_check_numeric == 'off')
                                                                <li><span class="close-icon"> <i class="fas fa-times"></i>
                                                                    </span></li>
                                                            @else
                                                                <li><span class="close-icon">
                                                                        {{ $attr->premium_check_numeric }} </span></li>
                                                            @endif
                                                        @endforeach
                                                        <li>
                                                            <div class="price">
                                                                @if ($project->premium_discount_charge != null && $project->premium_discount_charge > 0)
                                                                    <h6 class="price-main">
                                                                        {{ amount_with_currency_symbol($project->premium_discount_charge) }}
                                                                    </h6>
                                                                    <s class="price-old">
                                                                        {{ amount_with_currency_symbol($project->premium_regular_charge) }}
                                                                    </s>
                                                                @else
                                                                    <h6 class="price-main">
                                                                        {{ amount_with_currency_symbol($project->premium_regular_charge) }}
                                                                    </h6>
                                                                @endif
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <?php
    $pagination_limit = 10;
    $project_id = $project->id;
    $countProjectCompleteOrder = \App\Models\Order::select('id')
        ->whereHas('rating')
        ->where('identity', $project->id)
        ->where('is_project_job', 'project')
        ->where('status', 3)
        ->count();
                                ?>

                        @if ($countProjectCompleteOrder >= 1)
                            <div class="project-preview">
                                <div class="project-preview-head profile-border-bottom">
                                    <h4 class="project-preview-head-title">{{ __('Feedback & Reviews') }}</h4>
                                </div>
                                <div class="project-reviews">
                                    @include('frontend.pages.project-details.reviews')
                                </div>
                                @if($countProjectCompleteOrder > $pagination_limit)
                                    <a href="javascript:void(0)" data-project-id="{{ $project_id }}"
                                        class="btn-profile btn-bg-1 text-center load_more_data"
                                        data-review-count="{{ $countProjectCompleteOrder }}" data-page-id="1"
                                        data-pagination-limit="{{ $pagination_limit }}">{{ __('Load More') }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="col-xl-5 col-lg-5">
                        <x-validation.error />
                        <div class="sticky-sidebar">
                            <div class="project-preview">
                                <div class="project-preview-tab">
                                    <ul class="tabs">
                                        <li data-tab="basic" class="active">{{ $project->basic_title }}</li>
                                        <li data-tab="standard" class="@if(empty($project->standard_title)) pe-none @endif">
                                            {{ $project->standard_title }}
                                        </li>
                                        <li data-tab="premium" class="@if(empty($project->premium_title)) pe-none @endif">
                                            {{ $project->premium_title }}
                                        </li>
                                    </ul>
                                    <div class="project-preview-tab-contents mt-4">

                                        <div class="tab-content-item active" id="basic">
                                            <div class="project-preview-tab-header">
                                                <div class="project-preview-tab-header-item">
                                                    <span class="left"><i class="fa-solid fa-repeat"></i>
                                                        {{ __('Revisions') }}</span>
                                                    <strong class="right">{{ $project->basic_revision }}</strong>
                                                </div>
                                                <div class="project-preview-tab-header-item">
                                                    <span class="left"><i class="fa-regular fa-clock"></i>
                                                        {{ __('Delivery time') }}</span>
                                                    <strong class="right">{{ __($project->basic_delivery) }}</strong>
                                                </div>
                                            </div>
                                            @if (!empty($project->basic_details))
                                                <div class="project-preview-tab-header-item mt-3">
                                                    <div class="project-description-text mt-2">
                                                        {!! nl2br(e($project->basic_details)) !!}
                                                    </div>
                                                </div>
                                            @endif
                                            <hr>
                                            <div class="project-preview-tab-inner mt-4">
                                                @foreach ($project->project_attributes as $attr)
                                                    <div class="project-preview-tab-inner-item">
                                                        <span class="left">{{ $attr->check_numeric_title }}</span>
                                                        @if ($attr->standard_check_numeric == 'on')
                                                            <span class="check-icon"> <i class="fas fa-check"></i> </span>
                                                        @elseif($attr->standard_check_numeric == 'off')
                                                            <span class="close-close"> <i class="fas fa-times"></i>
                                                            </span>
                                                        @else
                                                            <span class="right"> {{ $attr->standard_check_numeric }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach

                                                <div class="project-preview-tab-inner-item">
                                                    @if ($project->basic_discount_charge != null && $project->basic_discount_charge > 0)
                                                        <span class="left price-title">{{ __('Price') }}</span>
                                                        <span class="right price">
                                                            <s>{{ amount_with_currency_symbol($project->basic_regular_charge ?? '') }}</s><span>{{ amount_with_currency_symbol($project->basic_discount_charge) }}</span></span>
                                                    @else
                                                        <span class="left price-title">{{ __('Price') }}</span>
                                                        <span
                                                            class="right price"><span>{{ amount_with_currency_symbol($project->basic_regular_charge ?? '') }}</span></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content-item" id="standard">
                                            <div class="project-preview-tab-header">
                                                <div class="project-preview-tab-header-item">
                                                    <span class="left"><i class="fa-solid fa-repeat"></i>
                                                        {{ __('Revisions') }}</span>
                                                    <strong class="right">{{ $project->standard_revision }}</strong>
                                                </div>
                                                <div class="project-preview-tab-header-item">
                                                    <span class="left"><i class="fa-regular fa-clock"></i>
                                                        {{ __('Delivery time') }}</span>
                                                    <strong class="right">{{ __($project->standard_delivery) }}</strong>
                                                </div>
                                            </div>
                                            @if (!empty($project->standard_details))
                                                <div class="project-preview-tab-header-item mt-3">
                                                    <div class="project-description-text mt-2">
                                                        {!! nl2br(e($project->standard_details)) !!}
                                                    </div>
                                                </div>
                                            @endif
                                            <hr>
                                            <div class="project-preview-tab-inner mt-4">
                                                @foreach ($project->project_attributes as $attr)
                                                    <div class="project-preview-tab-inner-item">
                                                        <span class="left">{{ $attr->check_numeric_title }}</span>
                                                        @if ($attr->standard_check_numeric == 'on')
                                                            <span class="check-icon"> <i class="fas fa-check"></i> </span>
                                                        @elseif($attr->standard_check_numeric == 'off')
                                                            <span class="close-close"> <i class="fas fa-times"></i>
                                                            </span>
                                                        @else
                                                            <span class="right"> {{ $attr->standard_check_numeric }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach

                                                <div class="project-preview-tab-inner-item">
                                                    @if ($project->standard_discount_charge != null && $project->standard_discount_charge > 0)
                                                        <span class="left price-title">{{ __('Price') }}</span>
                                                        <span class="right price">
                                                            <s>{{ amount_with_currency_symbol($project->standard_regular_charge ?? '') }}</s><span>{{ amount_with_currency_symbol($project->standard_discount_charge) }}</span></span>
                                                    @else
                                                        <span class="left price-title">{{ __('Price') }}</span>
                                                        <span
                                                            class="right price"><span>{{ amount_with_currency_symbol($project->standard_regular_charge ?? '') }}</span></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content-item" id="premium">
                                            <div class="project-preview-tab-header">
                                                <div class="project-preview-tab-header-item">
                                                    <span class="left"><i class="fa-solid fa-repeat"></i>
                                                        {{ __('Revisions') }}</span>
                                                    <strong class="right">{{ $project->premium_revision }}</strong>
                                                </div>
                                                <div class="project-preview-tab-header-item">
                                                    <span class="left"><i class="fa-regular fa-clock"></i>
                                                        {{ __('Delivery time') }}</span>
                                                    <strong class="right">{{ __($project->premium_delivery) }}</strong>
                                                </div>
                                            </div>
                                            @if (!empty($project->premium_details))
                                                <div class="project-preview-tab-header-item mt-3">
                                                    <div class="project-description-text mt-2">
                                                        {!! nl2br(e($project->premium_details)) !!}
                                                    </div>
                                                </div>
                                            @endif
                                            <hr>
                                            <div class="project-preview-tab-inner mt-4">
                                                @foreach ($project->project_attributes as $attr)
                                                    <div class="project-preview-tab-inner-item">
                                                        <span class="left">{{ $attr->check_numeric_title }}</span>
                                                        @if ($attr->premium_check_numeric == 'on')
                                                            <span class="check-icon"> <i class="fas fa-check"></i> </span>
                                                        @elseif($attr->premium_check_numeric == 'off')
                                                            <span class="close-icon"> <i class="fas fa-times"></i> </span>
                                                        @else
                                                            <span class="right"> {{ $attr->premium_check_numeric }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach


                                                <div class="project-preview-tab-inner-item">
                                                    @if ($project->premium_discount_charge != null && $project->premium_discount_charge > 0)
                                                        <span class="left price-title">{{ __('Price') }}</span>
                                                        <span class="right price">
                                                            <s>{{ amount_with_currency_symbol($project->premium_regular_charge ?? '') }}</s><span>{{ amount_with_currency_symbol($project->premium_discount_charge) }}</span></span>
                                                    @else
                                                        <span class="left price-title">{{ __('Price') }}</span>
                                                        <span
                                                            class="right price"><span>{{ amount_with_currency_symbol($project->premium_regular_charge ?? '') }}</span></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="btn-wrapper flex-btn justify-content-between mt-4">
                                            @if (Auth::guard('web')->check())
                                                @if (Auth::guard('web')->user()->id != $project->user_id)
                                                    <form action="{{ route('client.message.send') }}" method="post"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="freelancer_id" id="freelancer_id"
                                                            value="{{ $project->user_id }}">
                                                        <input type="hidden" name="from_user" id="from_user"
                                                            value="{{ Auth::guard('web')->user()->id }}">
                                                        <input type="hidden" name="project_id" id="project_id"
                                                            value="{{ $project->id }}">
                                                        <button type="submit" class="btn-profile btn-outline-gray"><i
                                                                class="fa-regular fa-comments"></i>
                                                            {{ __('Contact Me') }}</button>
                                                    </form>
                                                    @if(moduleExists('SecurityManage'))
                                                        @if(Auth::guard('web')->user()->freeze_order_create == 'freeze')
                                                            <a href="#/"
                                                                class="btn-profile btn-bg-1 @if(Auth::guard('web')->user()->freeze_order_create == 'freeze') disabled-link @endif">
                                                                {{ __('Continue to Order') }}
                                                            </a>
                                                        @else
                                                            <a href="#/" class="btn-profile btn-bg-1 basic_standard_premium"
                                                                data-project_id="{{ $project->id }}" data-bs-toggle="modal"
                                                                data-bs-target="#paymentGatewayModal">{{ __('Continue to Order') }}
                                                            </a>
                                                        @endif
                                                    @else
                                                        <a href="#/" class="btn-profile btn-bg-1 basic_standard_premium"
                                                            data-project_id="{{ $project->id }}" data-bs-toggle="modal"
                                                            data-bs-target="#paymentGatewayModal">{{ __('Continue to Order') }}
                                                        </a>
                                                    @endif
                                                @endif
                                            @else
                                                <a class="btn-profile btn-outline-gray contact_warning_chat_message">
                                                    <i class="fa-regular fa-comments"></i>{{ __('Contact Me') }}
                                                </a>
                                                <a href="#/" class="btn-profile btn-bg-1" data-bs-toggle="modal"
                                                    data-bs-target="#loginModal">{{ __('Login to Order') }}
                                                </a>
                                            @endif
                                        </div>
                                        @if (!empty($project->standard_title) && !empty($project->premium_title))
                                            <div class="btn-wrapper text-left mt-4">
                                                <a href="#comparePackage" class="compareBtn">
                                                    {{ __('Compare Package') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Project preview area end -->
    </main>

    @include('frontend.pages.order.login-markup')
    @include('frontend.pages.order.gateway-markup')

    <!-- Lightbox Modal -->
    <div class="lightbox" id="lightbox">
        <div class="lightbox-content">
            <button class="lightbox-close" onclick="closeLightbox()">
                <i class="fas fa-times"></i>
            </button>
            <div id="lightboxContent"></div>
            <div class="lightbox-nav">
                <button class="prev" onclick="changeLightboxSlide(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="next" onclick="changeLightboxSlide(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="lightbox-counter">
                <span id="lightboxCounter">1</span> / <span id="lightboxTotal">{{ count($files ?? []) }}</span>
            </div>
        </div>
    </div>

    {{-- Share Project Modal --}}
    <div class="modal fade" id="shareProjectModal" tabindex="-1" aria-labelledby="shareProjectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content radius-10">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareProjectModalLabel">{{ __('Share Project') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body text-center">
                    <!-- Project Files Preview -->
                    @php
                        $shareFiles = json_decode($project->image, true);
                        $shareItems = [];
                        $dummyIcons = [
                            'pdf' => 'https://ssl.gstatic.com/docs/doclist/images/mediatype/icon_1_pdf_x16.png',
                            'doc' => 'https://ssl.gstatic.com/docs/doclist/images/mediatype/icon_1_word_x16.png',
                            'docx' => 'https://ssl.gstatic.com/docs/doclist/images/mediatype/icon_1_word_x16.png',
                            'xls' => 'https://ssl.gstatic.com/docs/doclist/images/mediatype/icon_1_xls_x16.png',
                            'xlsx' => 'https://ssl.gstatic.com/docs/doclist/images/mediatype/icon_1_xls_x16.png',
                            'default' => 'https://ssl.gstatic.com/docs/doclist/images/mediatype/icon_1_document_x16.png',
                        ];
                        if (!empty($shareFiles) && is_array($shareFiles)) {
                            foreach ($shareFiles as $file) {
                                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                $fp = (cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                                    ? render_frontend_cloud_image_if_module_exists('project/' . $file, load_from: $project->load_from)
                                    : asset('assets/uploads/project/' . $file);
                                $item = [
                                    'type' => 'other',
                                    'src' => $fp,
                                    'ext' => $ext,
                                    'name' => basename($file),
                                    'icon' => $dummyIcons['default']
                                ];
                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'bmp', 'tiff', 'svg', 'webp'])) {
                                    $item['type'] = 'image';
                                } elseif (in_array($ext, ['mp4', 'mov', 'avi', 'wmv', 'mkv'])) {
                                    $item['type'] = 'video';
                                } elseif ($ext === 'pdf') {
                                    $item['type'] = 'pdf';
                                    $item['icon'] = $dummyIcons['pdf'];
                                } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                    $item['type'] = 'excel';
                                    $item['icon'] = $dummyIcons['xls'];
                                } elseif (in_array($ext, ['doc', 'docx'])) {
                                    $item['type'] = 'word';
                                    $item['icon'] = $dummyIcons['doc'];
                                }
                                $shareItems[] = $item;
                            }
                        }
                    @endphp
                    @if(count($shareItems))
                        <div class="mb-3" id="shareProjectMainPreview">
                            @php $main = $shareItems[0]; @endphp
                            @if($main['type'] === 'image')
                                <img src="{{ $main['src'] }}" alt="{{ __('Project Image') }}" class="rounded mb-2" width="150px">
                            @elseif($main['type'] === 'video')
                                <video controls class="rounded mb-2" style="max-width:180px; max-height:100px;">
                                    <source src="{{ $main['src'] }}" type="video/{{ $main['ext'] }}">
                                </video>
                            @elseif($main['type'] === 'pdf')
                                <embed src="{{ $main['src'] }}" type="application/pdf" width="100" height="100"
                                    class="rounded mb-2">
                            @elseif($main['type'] === 'excel' || $main['type'] === 'word')
                                <a href="{{ $main['src'] }}" target="_blank">
                                    <img src="{{ $main['icon'] }}" class="mb-2" style="width:40px;">
                                    <div class="small text-muted">{{ $main['name'] }}</div>
                                </a>
                            @else
                                <a href="{{ $main['src'] }}" target="_blank">
                                    <img src="{{ $main['icon'] }}" class="mb-2" style="width:40px;">
                                    <div class="small text-muted">{{ $main['name'] }}</div>
                                </a>
                            @endif
                        </div>
                        @if(count($shareItems) > 1)
                            <div class="d-flex justify-content-center gap-2 mb-3">
                                @foreach($shareItems as $idx => $item)
                                    @if($item['type'] === 'image')
                                        <img src="{{ $item['src'] }}"
                                            class="rounded share-project-thumb @if($idx === 0) border border-success @endif"
                                            data-type="image" data-src="{{ $item['src'] }}"
                                            style="width:40px; height:40px; object-fit:cover; cursor:pointer;">
                                    @elseif($item['type'] === 'video')
                                        <video muted class="rounded share-project-thumb @if($idx === 0) border border-success @endif"
                                            data-type="video" data-src="{{ $item['src'] }}"
                                            style="width:40px; height:40px; object-fit:cover; cursor:pointer;">
                                            <source src="{{ $item['src'] }}" type="video/{{ $item['ext'] }}">
                                        </video>
                                    @elseif($item['type'] === 'pdf')
                                        <img src="{{ $item['icon'] }}"
                                            class="rounded share-project-thumb @if($idx === 0) border border-success @endif" data-type="pdf"
                                            data-src="{{ $item['src'] }}"
                                            style="width:40px; height:40px; object-fit:contain; background:#fff; cursor:pointer;">
                                    @elseif($item['type'] === 'excel' || $item['type'] === 'word')
                                        <img src="{{ $item['icon'] }}"
                                            class="rounded share-project-thumb @if($idx === 0) border border-success @endif"
                                            data-type="{{ $item['type'] }}" data-src="{{ $item['src'] }}" data-name="{{ $item['name'] }}"
                                            style="width:40px; height:40px; object-fit:contain; background:#fff; cursor:pointer;">
                                    @else
                                        <img src="{{ $item['icon'] }}"
                                            class="rounded share-project-thumb @if($idx === 0) border border-success @endif"
                                            data-type="other" data-src="{{ $item['src'] }}" data-name="{{ $item['name'] }}"
                                            style="width:40px; height:40px; object-fit:contain; background:#fff; cursor:pointer;">
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endif
                    <h5>{{ $project->title }}</h5>
                    <div class="text-muted"
                        style="max-height:180px; overflow:auto; text-align:left; margin-bottom:10px; text-align: center;">
                        {!! $project->description !!}
                    </div>
                    @php
                        $projectUrl = url()->current();
                    @endphp
                    <div class="d-flex justify-content-center gap-3">
                        <!-- WhatsApp -->
                        <a href="https://wa.me/?text={{ urlencode('Check out this project: ' . $projectUrl) }}"
                            target="_blank" class="btn btn-success btn-icon" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <!-- Twitter -->
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($projectUrl) }}" target="_blank"
                            class="btn btn-primary btn-icon" title="Twitter"
                            style="background-color: #1DA1F2; border-color: #1DA1F2;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <!-- LinkedIn -->
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode($projectUrl) }}"
                            target="_blank" class="btn btn-primary btn-icon" title="LinkedIn"
                            style="background-color: #0077B5; border-color: #0077B5;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($projectUrl) }}" target="_blank"
                            class="btn btn-primary btn-icon" title="Facebook"
                            style="background-color: #1877F2; border-color: #1877F2;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <!-- Copy Link -->
                        <a href="javascript:void(0);" class="btn btn-dark btn-icon copy-project-link" title="Copy Link"
                            data-link="{{ $projectUrl }}">
                            <i class="fas fa-link"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <x-frontend.payment-gateway.gateway-select-js />
    @include('frontend.pages.project-details.load-more-js')
    @include('frontend.pages.order.order-js')
    <script>
        // Gallery data
        let galleryFiles = @json($files ?? []);
        let currentIndex = 0;
        let lightboxIndex = 0;

        document.addEventListener('DOMContentLoaded', function () {
            // Professional Gallery Functions
            window.changeMainContent = function (index) {
                if (index < 0 || index >= galleryFiles.length) return;

                currentIndex = index;
                const file = galleryFiles[index];
                const ext = file.split('.').pop().toLowerCase();
                const cloud = {{ $cloud ? 'true' : 'false' }};
                const loadFrom = '{{ $project->load_from ?? "" }}';

                let filePath;
                if (cloud) {
                    filePath = '{{ url("/") }}/render-frontend-cloud-image-if-module-exists/project/' + file + '?load_from=' + loadFrom;
                } else {
                    filePath = '{{ asset("assets/uploads/project/") }}/' + file;
                }

                const mainContainer = document.getElementById('galleryMain');
                let html = '';

                if (['jpg', 'jpeg', 'png', 'bmp', 'tiff', 'svg', 'webp'].includes(ext)) {
                    html = `<img src="${filePath}" alt="{{ $project->title }}" id="mainImage">`;
                } else if (['mp4', 'mov', 'avi', 'wmv', 'mkv'].includes(ext)) {
                    html = `<video controls id="mainVideo"><source src="${filePath}" type="video/${ext}"></video>`;
                } else if (ext === 'pdf') {
                    html = `<embed src="${filePath}" type="application/pdf" id="mainPdf">`;
                } else {
                    const dummyIcon = 'https://ssl.gstatic.com/docs/doclist/images/mediatype/icon_1_document_x16.png';
                    html = `<div class="file-preview"><img src="${dummyIcon}" alt="File"><p>${file}</p></div>`;
                }

                mainContainer.innerHTML = html + `
                        <div class="gallery-counter">
                            <span id="currentIndex">${index + 1}</span> / <span id="totalCount">${galleryFiles.length}</span>
                        </div>
                        <button class="gallery-fullscreen" onclick="openLightbox(${index})">
                            <i class="fas fa-expand"></i>
                        </button>
                    `;

                // Update thumbnails
                document.querySelectorAll('.gallery-thumb').forEach((thumb, i) => {
                    thumb.classList.toggle('active', i === index);
                });
            };

            window.changeSlide = function (direction) {
                const newIndex = currentIndex + direction;
                if (newIndex >= 0 && newIndex < galleryFiles.length) {
                    changeMainContent(newIndex);
                }
            };

            window.openLightbox = function (index) {
                lightboxIndex = index;
                const file = galleryFiles[index];
                const ext = file.split('.').pop().toLowerCase();
                const cloud = {{ $cloud ? 'true' : 'false' }};
                const loadFrom = '{{ $project->load_from ?? "" }}';

                let filePath;
                if (cloud) {
                    filePath = '{{ url("/") }}/render-frontend-cloud-image-if-module-exists/project/' + file + '?load_from=' + loadFrom;
                } else {
                    filePath = '{{ asset("assets/uploads/project/") }}/' + file;
                }

                const lightboxContent = document.getElementById('lightboxContent');
                let html = '';

                if (['jpg', 'jpeg', 'png', 'bmp', 'tiff', 'svg', 'webp'].includes(ext)) {
                    html = `<img src="${filePath}" alt="{{ $project->title }}">`;
                } else if (['mp4', 'mov', 'avi', 'wmv', 'mkv'].includes(ext)) {
                    html = `<video controls><source src="${filePath}" type="video/${ext}"></video>`;
                } else if (ext === 'pdf') {
                    html = `<embed src="${filePath}" type="application/pdf">`;
                } else {
                    const dummyIcon = 'https://ssl.gstatic.com/docs/doclist/images/mediatype/icon_1_document_x16.png';
                    html = `<div style="text-align:center; color:white;"><img src="${dummyIcon}" style="width:80px; margin-bottom:1rem;"><p>${file}</p></div>`;
                }

                lightboxContent.innerHTML = html;
                document.getElementById('lightboxCounter').textContent = index + 1;
                document.getElementById('lightbox').classList.add('active');
                document.body.style.overflow = 'hidden';
            };

            window.closeLightbox = function () {
                document.getElementById('lightbox').classList.remove('active');
                document.body.style.overflow = '';
            };

            window.changeLightboxSlide = function (direction) {
                const newIndex = lightboxIndex + direction;
                if (newIndex >= 0 && newIndex < galleryFiles.length) {
                    openLightbox(newIndex);
                }
            };

            // Keyboard navigation
            document.addEventListener('keydown', function (e) {
                if (document.getElementById('lightbox').classList.contains('active')) {
                    if (e.key === 'Escape') {
                        closeLightbox();
                    } else if (e.key === 'ArrowLeft') {
                        changeLightboxSlide(-1);
                    } else if (e.key === 'ArrowRight') {
                        changeLightboxSlide(1);
                    }
                }
            });

            // Close lightbox when clicking outside content
            document.getElementById('lightbox').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeLightbox();
                }
            });

            // Copy project link logic
            document.querySelectorAll('.copy-project-link').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const projectLink = this.getAttribute('data-link');
                    navigator.clipboard.writeText(projectLink).then(() => {
                        if (window.toastr) toastr.success("Project link copied to clipboard!");
                    }).catch(() => {
                        if (window.toastr) toastr.error("Failed to copy project link.");
                    });
                });
            });

            // Share modal file preview logic
            document.querySelectorAll('.share-project-thumb').forEach(function (thumb) {
                thumb.addEventListener('click', function () {
                    document.querySelectorAll('.share-project-thumb').forEach(t => t.classList.remove('border', 'border-success'));
                    this.classList.add('border', 'border-success');
                    var type = this.getAttribute('data-type');
                    var src = this.getAttribute('data-src');
                    var name = this.getAttribute('data-name') || '';
                    var mainPreview = document.getElementById('shareProjectMainPreview');
                    if (mainPreview) {
                        let html = '';
                        if (type === 'image') {
                            html = `<img src="${src}" alt="Project Image" class="rounded mb-2" width="150px">`;
                        } else if (type === 'video') {
                            html = `<video controls class="rounded mb-2" style="max-width:180px; max-height:100px;"><source src="${src}"></video>`;
                        } else if (type === 'pdf') {
                            html = `<embed src="${src}" type="application/pdf" width="100" height="100" class="rounded mb-2">`;
                        } else if (type === 'excel' || type === 'word') {
                            html = `<a href="${src}" target="_blank"><img src="${this.src}" class="mb-2" style="width:40px;"><div class="small text-muted">${name}</div></a>`;
                        } else {
                            html = `<a href="${src}" target="_blank"><img src="${this.src}" class="mb-2" style="width:40px;"><div class="small text-muted">${name}</div></a>`;
                        }
                        mainPreview.innerHTML = html;
                    }
                });
            });
        });
    </script>
@endsection