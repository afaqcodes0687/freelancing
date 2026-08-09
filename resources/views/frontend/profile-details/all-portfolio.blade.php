<div class="profile-details-widget-single radius-10">
    <div class="profile-wrapper-item-flex flex-between align-items-center profile-border-bottom">
        <h4 class="profile-wrapper-item-title"> {{ __('Portfolio') }} </h4>
        @if (
                Auth::guard('web')->check() &&
                Auth::guard('web')->user()->user_type == 2 &&
                Auth::guard('web')->user()->username == $username
            )
            <div class="profile-wrapper-item-plus add-portfolio-click add_portfolio_show_hide">
                <i class="fas fa-plus"></i>
            </div>
        @endif
    </div>
    <div class="profile-details-widget-portfolio-row portfolio_details_display">
        @forelse ($portfolios as $portfolio)
            <div class="profile-details-widget-portfolio-col click-portfolio view_portfolio_details"
                data-id="{{ $portfolio->id }}">
                <div class="profile-details-portfolio ">
                    <div class="profile-details-portfolio-thumb">
                        <a href="#/">
                            @php
                                $image = $portfolio->image ?? null;
                                $cloud = cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']);
                                $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                                $file_url = $cloud
                                    ? render_frontend_cloud_image_if_module_exists('portfolio/' . $image, load_from: $portfolio->load_from)
                                    : asset('assets/uploads/portfolio/' . $image);
                            @endphp

                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                                <img src="{{ $file_url }}" alt="{{ __('portfolio') }}">
                            @elseif(in_array($ext, ['mp4', 'mov', 'avi', 'webm']))
                                <video src="{{ $file_url }}" style="max-width:100%; border-radius:10px;" controls muted></video>
                            @elseif(in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx']))
                                <div style="text-align:center;">
                                    @php
                                        $icon = match ($ext) {
                                            'pdf' => 'fa-file-pdf',
                                            'doc', 'docx' => 'fa-file-word',
                                            'xls', 'xlsx' => 'fa-file-excel',
                                            default => 'fa-file-alt',
                                        };
                                    @endphp
                                    <i class="fas {{ $icon }}" style="font-size:40px; color:#2b7a78;"></i><br>
                                    <small>{{ $image }}</small>
                                </div>
                            @else
                                <img src="{{ asset('assets/uploads/portfolio/logo172632858617267305591731011557.png') }}"
                                    alt="default-avatar">
                            @endif
                        </a>
                    </div>

                    <div class="profile-details-portfolio-content mt-3">
                        <h5 class="profile-details-portfolio-content-title d-flex justify-content-between">
                            <a href="#/">{{ $portfolio->title }}</a>
                        </h5>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <p class="profile-details-portfolio-content-para">
                                {{ $portfolio->created_at->toFormattedDateString() ?? '' }}
                            </p>
                            <a href="#/" class="btn-profile btn-outline-1 btn-small">{{ __('Details') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            @if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2 && Auth::guard('web')->user()->username == $username)
                <div class="no-portfolio-found-wrapper text-center w-100">
                    <div class="empty-state-icon mb-3">
                        <i class="fas fa-briefcase" style="font-size: 48px; color: #ddd;"></i>
                    </div>
                    <h4 class="mb-3">{{ __('Build trust with a portfolio!') }}</h4>
                    <p class="text-muted mb-4 px-3">
                        {{ __('Adding portfolios helps clients see your expertise and trust your skills. Boost your chances of getting hired!') }}
                    </p>
                    <div class="add-portfolio-click add_portfolio_show_hide btn-profile btn-bg-1 d-inline-block cursor-pointer">
                        {{ __('Add Your First Portfolio') }}
                    </div>
                </div>
            @else
                <div class="col-12 py-4 text-center">
                    <p class="text-muted">{{ __('No portfolios found.') }}</p>
                </div>
            @endif
        @endforelse
    </div>
</div>