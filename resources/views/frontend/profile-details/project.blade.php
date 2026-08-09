<style>
    .file-name-box {
        background-color: #f9f9f9;
        padding: 10px 15px;
        border-radius: 6px;
        font-weight: 500;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .file-name-box i {
        font-size: 20px;
        color: #2b7a78;
    }
    .file-name-text {
        font-size: 14px;
        word-break: break-word;
        margin-top: 6px;
    }
    .project-catalogue-thumb {
        position: relative;
        background-color: #f0f0f0;
        border-radius: 6px;
        overflow: hidden;
    }
    .slider-container {
        height: 200px;
        position: relative;
    }
    .slider-item {
        display: none;
        height: 100%;
        width: 100%;
    }
    .slider-item.active {
        display: block;
    }
    .slider-item a {
        display: block;
        height: 100%;
        width: 100%;
    }
    .slider-item img,
    .slider-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .slider-nav-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 0;
        gap: 15px;
    }
    .slider-nav {
        display: flex;
        align-items: center;
        display: flex;
        align-items: center;
        gap: 18px;
        background-color: #309400;
        padding: 8px;
        border-radius: 30px;
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
    }
    .slider-nav button:hover {
        background: rgba(0, 0, 0, 0.6);
    }
    .slider-text {
        font-size: 14px;
        color: white;
    }
</style>


<div class="profile-wrapper-item add-project-parent radius-10 project_wrapper_area">
    <div class="profile-wrapper-item-flex flex-between align-items-center profile-border-bottom">
        <h4 class="profile-wrapper-item-title"> {{ __('Project Catalogues') }} </h4>
        @if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2  && Auth::guard('web')->user()->username==$username)
            <div class="profile-wrapper-item-plus create_project_show_hide">
               <a href="{{route('freelancer.project.create')}}"><i class="fas fa-plus"></i></a>
            </div>
        @endif
    </div>
    @forelse($projects as $project)
        @php
            $projectFiles = !empty($project->image) ? json_decode($project->image, true) : [];
            if (!is_array($projectFiles)) { $projectFiles = []; }
            $defaultImage = asset('assets/uploads/project/project-default-logo.jpeg');
            $isCloud = cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']);
            $imageExtensions = ['jpg', 'jpeg', 'png', 'svg', 'bmp', 'tiff', 'webp'];
            $videoExtensions = ['mp4', 'mov', 'avi', 'mkv'];
            $docExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];
        @endphp
        @if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2 && Auth::guard('web')->user()->username==$username)
            <div class="single-project project-catalogue">
                <div class="project-catalogue-flex">
                    <div class="project-catalogue-thumb">
                        <div class="slider-container" id="slider-{{ $project->id }}">
                            @if(!empty($projectFiles))
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
                                                <img src="{{ $filePath }}" alt="{{ $project->title }}">
                                            @elseif(in_array($fileExtension, $videoExtensions))
                                                <video muted loop autoplay>
                                                    <source src="{{ $filePath }}" type="video/{{ $fileExtension }}">
                                                    {{ __('Your browser does not support the video tag.') }}
                                                </video>
                                            @elseif(in_array($fileExtension, $docExtensions))
                                                <div class="file-name-box" style="height:100%;">
                                                    <i class="fa fa-file-alt me-2" style="font-size: 48px; color: #2b7a78;"></i><br>
                                                    <span class="file-name-text">{{ basename($projectImage) }}</span>
                                                </div>
                                            @else
                                                <img src="{{ $defaultImage }}" alt="default-avatar">
                                            @endif
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <div class="slider-item active">
                                    <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                        <img src="{{ $defaultImage }}" alt="default-avatar">
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
                    </div>
                
                    <div class="single-project-content project-catalogue-contents mt-0">
                        <div class="single-project-content-top align-items-center flex-between">
                            {!! project_rating($project->id) !!}
                        </div>
                        <h4 class="single-project-content-title">
                            <a href="{{ route('project.details',['username'=>$project->project_creator?->username,'slug'=>$project->slug]) }}"> {{$project->title}} </a>
                        </h4>

                        <div class="project-catalogue-bottom flex-between mt-3">
                            @if($project->basic_discount_charge != null && $project->basic_discount_charge > 0)
                                <span class="single-project-content-price"> {{ amount_with_currency_symbol($project->basic_discount_charge) ?? '' }} <s>{{ amount_with_currency_symbol($project->basic_regular_charge) ?? '' }}</s> </span>
                            @else
                                <span class="single-project-content-price"> {{ amount_with_currency_symbol($project->basic_regular_charge) ?? '' }}</span>
                            @endif
                            <div class="single-project-delivery">
                            <span class="single-project-delivery-icon">
                                <i class="fa-regular fa-clock"></i> {{ __('Delivery') }}
                            </span>
                                <span class="single-project-delivery-days"> {{ __($project->basic_delivery) ?? 0 }} </span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="profile-wrapper-item-bottom profile-border-top">
                    <div class="profile-wrapper-item-bottom-flex flex-between align-items-center">
                        @if($project->status === 1)
                            <div class="profile-wrapper-right-flex flex-btn order_availability_show_hide">
                                <span class="profile-wrapper-switch-title"> {{ __('Available for order') }} </span>
                                <div class="profile-wrapper-switch-custom display_availability_for_order_or_not_{{$project->id}}">
                                    <label class="custom_switch">
                                        <input type="checkbox" id="available_for_order_or_not" data-id="{{ $project->id }}" data-project_on_off="{{ $project->project_on_off }}" @if($project->project_on_off == 1)checked @endif>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        @else
                            <div class="flex-btn">
                                <x-status.table.active-inactive :status="$project->status"/>
                                @if($project->project_approve_request == 2)
                                    <span class="btn-profile btn-outline-1 mb-3 view_project_reject_reason_details"
                                          data-bs-target="#rejectProjectReason"
                                          data-bs-toggle="modal"
                                          data-project-reject-description="{{ $project?->project_history?->reject_reason ?? __('No Description')}}"
                                    >
                                          {{ __('View Reject Reason') }}
                                    </span>
                                @endif
                            </div>
                        @endif
                        <div class="profile-wrapper-item-btn flex-btn">
                            @if($project?->orders_count == 0)
                            <a href="javascript:void(0)" class="btn-profile btn-outline-cancel delete_project edit_info_show_hide" data-project-id="{{ $project->id }}"> {{__('Delete')}} </a>
                            @endif
                            @if(moduleExists('SecurityManage'))
                                @if(Auth::guard('web')->user()->freeze_project == 'freeze')
                                    <a href="#" class="btn-profile btn-bg-1 @if(Auth::guard('web')->user()->freeze_project == 'freeze') disabled-link @endif"> {{ __('Edit Project') }} </a>
                                @else
                                    <a href="{{ route('freelancer.project.edit',$project->id) }}" class="btn-profile btn-bg-1 edit_info_show_hide"> {{ __('Edit Project') }} </a>
                                @endif
                            @else
                               <a href="{{ route('freelancer.project.edit',$project->id) }}" class="btn-profile btn-bg-1 edit_info_show_hide"> {{ __('Edit Project') }} </a>
                            @endif

                            @if(moduleExists('PromoteFreelancer'))
                                @php
                                       $current_date = \Carbon\Carbon::now()->toDateTimeString();
                                       $is_promoted = \Modules\PromoteFreelancer\Entities\PromotionProjectList::where('identity',$project->id)->where('type','project')->where('expire_date','>',$current_date)->where('payment_status','complete')->first();
                                @endphp

                                @if(!empty($is_promoted))
                                    <button type="button" class="btn btn-outline-primary" disabled>{{ __('Promoted') }}</button>
                                @else
                                    <a href="javascript:void(0)"
                                       class="btn-profile btn-bg-1 open_project_promote_modal"
                                       data-bs-target="#openProjectPromoteModal"
                                       data-bs-toggle="modal"
                                       data-project-id="{{ $project->id }}">
                                        {{ __('Promote Project') }}
                                    </a>
                                @endif
                           @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            @if($project->project_on_off == 1 && $project->status == 1 && $project->project_approve_request == 1)
                <div class="single-project project-catalogue">
                <div class="project-catalogue-flex">
                    <div class="project-catalogue-thumb">
                        <div class="slider-container" id="slider-public-{{ $project->id }}">
                            @if(!empty($projectFiles))
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
                                                <img src="{{ $filePath }}" alt="{{ $project->title }}">
                                            @elseif(in_array($fileExtension, $videoExtensions))
                                                <video muted loop autoplay>
                                                    <source src="{{ $filePath }}" type="video/{{ $fileExtension }}">
                                                    {{ __('Your browser does not support the video tag.') }}
                                                </video>
                                            @elseif(in_array($fileExtension, $docExtensions))
                                                <div class="file-name-box" style="height:100%;">
                                                    <i class="fa fa-file-alt me-2" style="font-size: 48px; color: #2b7a78;"></i><br>
                                                    <span class="file-name-text">{{ basename($projectImage) }}</span>
                                                </div>
                                            @else
                                                <img src="{{ $defaultImage }}" alt="default-avatar">
                                            @endif
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <div class="slider-item active">
                                    <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                        <img src="{{ $defaultImage }}" alt="default-avatar">
                                    </a>
                                </div>
                            @endif
                        </div>
                        @if(count($projectFiles) > 1)
                            <div class="slider-nav-wrapper">
                                <div class="slider-nav">
                                    <button class="slider-prev" onclick="navigateSlide('slider-public-{{ $project->id }}', -1)">&#10094;</button>
                                    <span class="slider-text swipe-btn">{{ __('Swipe') }}</span>
                                    <button class="slider-next" onclick="navigateSlide('slider-public-{{ $project->id }}', 1)">&#10095;</button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="single-project-content project-catalogue-contents mt-0">
                        <h4 class="single-project-content-title">
                            <a href="{{ route('project.details',['username'=>$project->project_creator?->username,'slug'=>$project->slug]) }}"> {{$project->title}} </a>
                        </h4>

                        <div class="project-catalogue-bottom flex-between mt-3">
                            @if($project->basic_discount_charge != null && $project->basic_discount_charge > 0)
                                <span class="single-project-content-price"> {{ amount_with_currency_symbol($project->basic_discount_charge) ?? '' }} <s>{{ amount_with_currency_symbol($project->basic_regular_charge) ?? '' }}</s> </span>
                            @else
                                <span class="single-project-content-price"> {{ amount_with_currency_symbol($project->basic_regular_charge) ?? '' }}</span>
                            @endif
                            <div class="single-project-delivery">
                            <span class="single-project-delivery-icon">
                                <i class="fa-regular fa-clock"></i> {{ __('Delivery') }}
                            </span>
                                <span class="single-project-delivery-days"> {{ __($project->basic_delivery) ?? 0 }}</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @endif
        @endif
    @empty
        @if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2 && Auth::guard('web')->user()->username == $username)
            <div class="no-project-found-wrapper text-center">
                <div class="empty-state-icon mb-3">
                    <i class="fas fa-layer-group" style="font-size: 48px; color: #ddd;"></i>
                </div>
                <h4 class="mb-3">{{ __('Your profile holds the key to your success!') }}</h4>
                <p class="text-muted mb-4 px-5">{{ __('Adding projects significantly boosts your visibility and trust with clients. Don\'t miss out—showcase your best work today!') }}</p>
                <a href="{{ route('freelancer.project.create') }}" class="btn-profile btn-bg-1">{{ __('Add Your First Project') }}</a>
            </div>
        @else
            <div class="no-project-found-wrapper text-center py-4">
                <p class="text-muted">{{ __('No projects found.') }}</p>
            </div>
        @endif
    @endforelse
    @include('frontend.profile-details.project-reject-reason')
    @if(moduleExists('PromoteFreelancer'))
    @include('frontend.profile-details.promotion.project-promote-modal')
    @endif
</div>
@if(!isset($__slider_script_loaded))
@php $__slider_script_loaded = true; @endphp
<script>
    if (typeof window.initProjectSliders === 'undefined') {
        window.initProjectSliders = function() {
            document.querySelectorAll('.slider-container').forEach(slider => {
                const items = slider.querySelectorAll('.slider-item');
                if (items.length === 0) return;

                let currentIndex = -1;
                items.forEach((item, index) => {
                    if (item.classList.contains('active')) {
                        currentIndex = index;
                    }
                });
                
                if (currentIndex === -1) {
                    currentIndex = 0;
                    items[0].classList.add('active');
                }

                slider.dataset.currentIndex = currentIndex;
                
                const activeVideo = items[currentIndex].querySelector('video');
                if (activeVideo) {
                    activeVideo.play().catch(e => console.error("Autoplay was prevented."));
                }

                if (items.length > 1) {
                    let slideInterval;
                    const startAutoSlide = () => {
                        slideInterval = setInterval(() => {
                            window.navigateSlide(slider.id, 1);
                        }, 5000); 
                    };

                    const stopAutoSlide = () => {
                        if (slideInterval) {
                            clearInterval(slideInterval);
                            slideInterval = null;
                        }
                    };

                    startAutoSlide();

                    slider.addEventListener('mouseenter', stopAutoSlide);
                    slider.addEventListener('mouseleave', startAutoSlide);

                    slider.startAutoSlide = startAutoSlide;
                    slider.stopAutoSlide = stopAutoSlide;
                }
            });
        };

        window.navigateSlide = function(sliderId, direction) {
            const slider = document.getElementById(sliderId);
            if (!slider) return;
            const items = slider.querySelectorAll('.slider-item');
            if (items.length <= 1) return;

            if (slider.stopAutoSlide) {
                slider.stopAutoSlide();
            }

            let currentIndex = parseInt(slider.dataset.currentIndex || 0);
            const currentItem = items[currentIndex];

            currentItem.classList.remove('active');
            const currentVideo = currentItem.querySelector('video');
            if (currentVideo) {
                currentVideo.pause();
                currentVideo.currentTime = 0;
            }

            currentIndex = (currentIndex + direction + items.length) % items.length;
            
            const nextItem = items[currentIndex];
            nextItem.classList.add('active');
            const nextVideo = nextItem.querySelector('video');
            if (nextVideo) {
                nextVideo.play().catch(e => console.error("Autoplay was prevented."));
            }
            
            slider.dataset.currentIndex = currentIndex;

            if (slider.startAutoSlide) {
                slider.startAutoSlide();
            }
        };

        document.addEventListener('DOMContentLoaded', window.initProjectSliders);
    }
    
    window.initProjectSliders();
</script>
@endif
