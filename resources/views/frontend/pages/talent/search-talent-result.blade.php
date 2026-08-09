<style>
    .single-freelancer {
        font-family: 'Inter', 'Poppins', 'Roboto', sans-serif;
        box-shadow:
            0 2px 4px rgba(0, 0, 0, 0.04),
            0 8px 16px rgba(0, 0, 0, 0.08);
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }

    .single-freelancer:hover {
        transform: translateY(-4px);
        box-shadow:
            0 4px 6px rgba(0, 0, 0, 0.05),
            0 12px 24px rgba(0, 0, 0, 0.08);
    }

    .profile-status-icon {
        position: absolute;
        top: 6px;
        left: 6px;   /* ← LEFT side */
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 3px solid #fff;
    }

    .profile-status-icon.online {
        background-color: #28a745;
    }

    .profile-status-icon.offline {
        background-color: #6c757d;
    }

</style>
<div class="shop-contents-wrapper-right">
    <div class="row g-4">
            <div class="col-lg-12">
                <div class="categoryWrap-wrapper-item" style="height:106%">
                    <div class="row g-4">
                        @php $current_date = \Carbon\Carbon::now()->toDateTimeString() @endphp
                        @foreach ($talents as $talent)
                            <div class="col-xxl-4 col-md-6">
                                <div class="single-freelancer center-text radius-20">
                                    <div class="single-freelancer-author">
                                        @if(moduleExists('PromoteFreelancer'))
                                            @if($talent->pro_expire_date >= $current_date  && $talent->is_pro === 'yes')
                                                @if($is_pro == 1)
                                                   {{--set is_pro value in session and get from profile details controller for click count--}}
                                                    @php Session::put('is_pro',$is_pro) @endphp
                                                <div class="single-project-content-review pro-profile-badge">
                                                    <div class="pro-icon-background">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                    <small>{{ __('Pro') }}</small>
                                                </div>
                                                @endif
                                            @endif
                                        @endif
                                        <a href="{{ route('freelancer.profile.details', $talent->username) }}" class="position-relative d-inline-block mb-2" style="width: 100px; height: 100px;">
                                            {{-- Image block stays same --}}
                                            @if ($talent->image)
                                                @if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                                                    <img src="{{ render_frontend_cloud_image_if_module_exists('profile/' . $talent->image, load_from: $talent->load_from) }}"
                                                        alt="{{ $talent->first_name }}"
                                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"
                                                        onerror="this.onerror=null;this.src='{{ asset('assets/static/img/author/author.jpg') }}';">
                                                @else
                                                    <img src="{{ asset('assets/uploads/profile/' . $talent->image) }}"
                                                        alt="{{ $talent->first_name }}"
                                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"
                                                        onerror="this.onerror=null;this.src='{{ asset('assets/static/img/author/author.jpg') }}';">
                                                @endif
                                                
                                                @else
                                                    <img src="{{ asset('assets/static/img/author/author.jpg') }}"
                                                        alt="{{ __('AuthorImg') }}"
                                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                            @endif


                                            {{-- Online/offline status icon --}}
                                           <div class="position-absolute profile-status-icon
                                            {{ Cache::has('user_is_online_' . $talent->id) ? 'online' : 'offline' }}">
                                        </div>
                                        
                                            {{-- Rated Plus Badge --}}
                                            @php
                                                $averageRating = round(optional($talent->freelancer_ratings)->avg('rating'), 1);
                                            @endphp
                                            @if ($averageRating >= 4.5)
                                                <div style="position: absolute; bottom: 0; right: 0; width: 60px; height: 60px; left: 74px;">
                                                    <img src="{{ asset('assets/uploads/profile/badge.png') }}"
                                                        alt="Rated Plus Badge"
                                                        style="width: 100%; height: 100%;">
                                                    <span class="badge-title pb-3">+</span>
                                                </div>
                                            @endif
                                        </a>
                                        <br>
                                        
                                        <h4 class="single-freelancer-author-name mt-2">
                                            <a href="{{ route('freelancer.profile.details', $talent->username) }}">
                                                {{ $talent->full_name }}
                                                @if($talent->user_verified_status == 1) <i class="fas fa-circle-check" style="color:#309400"></i>@endif
                                            </a>
                                        </h4>
                                        <span class="single-freelancer-author-para mt-2">
                                            {{ $talent?->user_introduction?->title ?? '' }}
                                        </span>
                                        {!! freelancer_rating_for_profile_details_page($talent->id) !!}
                                    </div>
                                    <div class="single-freelancer-bottom">
                                        <div class="btn-wrapper">
                                            <a href="{{ route('freelancer.profile.details', $talent->username) }}" class="cmn-btn btn-bg-gray btn-small w-100 radius-5"> {{ __('View Profile') }} </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
    </div>
</div>

<x-pagination.laravel-paginate :allData="$talents" class="mt-4" />
