@once
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <style>
        .project-catalogue-thumb .swiper {
            width: 100%;
            height: 100%;
        }
        .project-catalogue-thumb .swiper-slide {
            text-align: center;
            font-size: 18px;
            background: #fff;
            display: -webkit-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
        }
        .project-catalogue-thumb .swiper-slide img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .swiper-pagination-bullet-active {
            background-color: var(--main-color-one) !important;
        }
    </style>
@endonce

<style>
.file-name-box {
// ... existing code ...
    <div class="profile-wrapper-item-flex flex-between align-items-center profile-border-bottom">
        <h4 class="profile-wrapper-item-title"> {{ __('Project Catalogues') }} </h4>
        @if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2  && Auth::guard('web')->user()->username==$username)
// ... existing code ...
    @foreach($projects as $project)
        @if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2 && Auth::guard('web')->user()->username==$username)
            <div class="single-project project-catalogue">
                <div class="project-catalogue-flex">
                <div class="single-project-thumb project-catalogue-thumb">
                    @php
                        $projectFiles = json_decode($project->image, true);
                        if (!is_array($projectFiles)) { $projectFiles = $project->image ? [$project->image] : []; }
                        $defaultImage = asset('assets/uploads/project/project-default-logo.jpeg');
                        $isCloud = cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']);
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'svg', 'bmp', 'tiff', 'webp'];
                        $videoExtensions = ['mp4', 'mov', 'avi', 'mkv'];
                        $docExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];
                    @endphp

                    @if (!empty($projectFiles))
                        <div class="swiper project-swiper-{{ $project->id }}">
                            <div class="swiper-wrapper">
                                @foreach($projectFiles as $file)
                                    <div class="swiper-slide">
                                        <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                            @php
                                                $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                                $filePath = $isCloud
                                                    ? render_frontend_cloud_image_if_module_exists('project/' . $file, load_from: $project->load_from)
                                                    : asset('assets/uploads/project/' . $file);
                                            @endphp

                                            @if(in_array(strtolower($fileExtension), $imageExtensions))
                                                <img src="{{ $filePath }}" alt="project-image">
                                            @elseif(in_array(strtolower($fileExtension), $videoExtensions))
                                                <video controls width="100%" style="max-height:200px; border-radius: 6px;">
                                                    <source src="{{ $filePath }}" type="video/{{ strtolower($fileExtension) }}">
                                                    {{ __('Your browser does not support the video tag.') }}
                                                </video>
                                            @elseif(in_array(strtolower($fileExtension), $docExtensions))
                                                <div class="file-name-box mt-2" style="padding: 15px; text-align: center;">
                                                    <i class="fa fa-file-alt me-2" style="font-size: 28px; color: #2b7a78;"></i><br>
                                                    <span class="file-name-text" style="font-size: 14px;">{{ basename($file) }}</span>
                                                </div>
                                            @else
                                                <img src="{{ $defaultImage }}" alt="default-avatar">
                                            @endif
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    @else
                        <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                            <img src="{{ $defaultImage }}" alt="default-avatar">
                        </a>
                    @endif
                </div>
                
                    <div class="single-project-content project-catalogue-contents mt-0">
                        <div class="single-project-content-top align-items-center flex-between">
// ... existing code ...
            @if($project->project_on_off == 1 && $project->status == 1 && $project->project_approve_request == 1)
                <div class="single-project project-catalogue">
                <div class="project-catalogue-flex">
                    <div class="single-project-thumb project-catalogue-thumb">
                        @php
                            $projectFiles = json_decode($project->image, true);
                            if (!is_array($projectFiles)) { $projectFiles = $project->image ? [$project->image] : []; }
                            $defaultImage = asset('assets/uploads/project/project-default-logo.jpeg');
                            $isCloud = cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']);
                            $imageExtensions = ['jpg', 'jpeg', 'png', 'svg', 'bmp', 'tiff', 'webp'];
                            $videoExtensions = ['mp4', 'mov', 'avi', 'mkv'];
                            $docExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];
                        @endphp

                        @if (!empty($projectFiles))
                            <div class="swiper project-swiper-{{ $project->id }}">
                                <div class="swiper-wrapper">
                                    @foreach($projectFiles as $file)
                                        <div class="swiper-slide">
                                            <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                                @php
                                                    $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                                                    $filePath = $isCloud
                                                        ? render_frontend_cloud_image_if_module_exists('project/' . $file, load_from: $project->load_from)
                                                        : asset('assets/uploads/project/' . $file);
                                                @endphp

                                                @if(in_array(strtolower($fileExtension), $imageExtensions))
                                                    <img src="{{ $filePath }}" alt="project-image">
                                                @elseif(in_array(strtolower($fileExtension), $videoExtensions))
                                                    <video controls width="100%" style="max-height:200px; border-radius: 6px;">
                                                        <source src="{{ $filePath }}" type="video/{{ strtolower($fileExtension) }}">
                                                        {{ __('Your browser does not support the video tag.') }}
                                                    </video>
                                                @elseif(in_array(strtolower($fileExtension), $docExtensions))
                                                    <div class="file-name-box mt-2" style="padding: 15px; text-align: center;">
                                                        <i class="fa fa-file-alt me-2" style="font-size: 28px; color: #2b7a78;"></i><br>
                                                        <span class="file-name-text" style="font-size: 14px;">{{ basename($file) }}</span>
                                                    </div>
                                                @else
                                                    <img src="{{ $defaultImage }}" alt="default-avatar">
                                                @endif
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        @else
                            <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                <img src="{{ $defaultImage }}" alt="default-avatar">
                            </a>
                        @endif
                    </div>
                    <div class="single-project-content project-catalogue-contents mt-0">
                        <h4 class="single-project-content-title">
                            <a href="{{ route('project.details',['username'=>$project->project_creator?->username,'slug'=>$project->slug]) }}"> {{$project->title}} </a>
// ... existing code ...
    @include('frontend.profile-details.project-reject-reason')
    @if(moduleExists('PromoteFreelancer'))
    @include('frontend.profile-details.promotion.project-promote-modal')
    @endif
</div>

@once
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[class*="project-swiper-"]').forEach(function(element) {
                new Swiper(element, {
                    loop: true,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                });
            });
        });
    </script>
@endonce 