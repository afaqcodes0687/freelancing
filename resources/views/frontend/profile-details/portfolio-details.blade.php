<span class="popup-contents-close popup-close"> <i class="fas fa-times"></i> </span>
    <div class="profile-details-portfolio">
        <div class="popup-contents-portfolio-thumb text-center">
            @php
                $file = $portfolioDetails->image ?? null;
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $cloud = cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']);
                $fileUrl = $cloud
                    ? render_frontend_cloud_image_if_module_exists('portfolio/' . $file, load_from: $portfolioDetails->load_from)
                    : asset('assets/uploads/portfolio/' . $file);

                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm']);
                $isDoc = in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx']);
            @endphp

            @if($file)
                @if($isImage)
                    <img src="{{ $fileUrl }}" alt="portfolio" style="max-width: 100%; border-radius: 10px;">
                @elseif($isVideo)
                    <video src="{{ $fileUrl }}" controls muted style="max-width: 100%; border-radius: 10px;"></video>
                @elseif($isDoc)
                    @php
                        $icon = match($ext) {
                            'pdf' => 'fa-file-pdf',
                            'doc', 'docx' => 'fa-file-word',
                            'xls', 'xlsx' => 'fa-file-excel',
                            default => 'fa-file-alt'
                        };
                    @endphp
                    <div style="margin-top: 10px;">
                        <i class="fas {{ $icon }}" style="font-size: 60px; color: #2b7a78;"></i>
                        <p class="mt-2">{{ $file }}</p>
                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                            {{ __('Download / View') }}
                        </a>
                    </div>
                @endif
            @else
                <img src="{{ asset('assets/uploads/portfolio/logo172632858617267305591731011557.png') }}" alt="default-avatar" style="max-width: 100%; border-radius: 10px;">
            @endif
        </div>
    <div class="profile-details-portfolio-content mt-3">
        <h5 class="profile-details-portfolio-content-title">
            <a href="javascript:void(0)">{{ $portfolioDetails->title ?? '' }}</a>
        </h5>
        <p class="profile-details-portfolio-content-para">{{ $portfolioDetails->created_at->toFormattedDateString() ?? '' }}</p>
        <p class="profile-details-portfolio-content-para">{{ $portfolioDetails->description ?? '' }} </p>
    </div>
</div>
@if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2 && Auth::guard('web')->user()->username==$username)
    <div class="popup-contents-btn flex-btn justify-content-end profile-border-top">
        <a href="javascript:void(0)" class="btn-profile btn-outline-gray btn-hover-danger delete_portfolio" data-id="{{ $portfolioDetails->id }}">
            <i class="fa-solid fa-trash-can"></i> {{ __('Delete') }}
        </a>
        <a href="javascript:void(0)"
           class="btn-profile btn-bg-1 edit_portfolio_details"
           data-id="{{ $portfolioDetails->id }}"
           data-title="{{ $portfolioDetails->title }}"
           data-description="{{ $portfolioDetails->description }}"
           data-image="{{ $portfolioDetails->image }}"
        > {{ __('Edit This') }} </a>
    </div>
@endif

