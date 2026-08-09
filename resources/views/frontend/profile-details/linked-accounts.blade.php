<div class="profile-wrapper-item radius-10 mt-4 display_linked_accounts_info">
    @php
        $intro = $user->user_introduction;
        $has_links = optional($intro)->github_link || optional($intro)->stackoverflow_link;
    @endphp
    <div class="profile-wrapper-item-flex flex-between align-items-center profile-border-bottom">
        <h4 class="profile-wrapper-item-title"> {{ __('Linked Accounts') }} </h4>
        @if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2 && Auth::guard('web')->user()->username == $username)
            <div class="profile-wrapper-item-plus" style="cursor: pointer;" data-bs-toggle="modal"
                data-bs-target="#linkedAccountsModal">
                @if($has_links)
                    <i class="fa-regular fa-pen-to-square"></i>
                @else
                    <i class="fa-solid fa-plus"></i>
                @endif
            </div>
        @endif
    </div>
    <div class="profile-wrapper-details-social mt-3">
        @php
            $github_meta = optional($intro)->github_meta;
        @endphp

        @if(optional($intro)->github_link)
            <div class="linked-account-card p-3 mb-3 radius-10"
                style="background: #f9f9f9; border: 1px solid #eee; position: relative;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="account-info">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="m-0" style="font-size: 16px; font-weight: 700;">GitHub</h5>
                            @if(isset($github_meta['created_at']))
                                <span class="text-muted" style="font-size: 12px;">Since
                                    {{ date('Y', strtotime($github_meta['created_at'])) }}</span>
                            @endif
                        </div>
                        <p class="m-0 text-dark" style="font-size: 14px; font-weight: 500;">
                            {{ $github_meta['name'] ?? ltrim(parse_url($intro->github_link, PHP_URL_PATH), '/') }}
                        </p>

                        <div class="mt-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-solid fa-link" style="font-size: 14px; color: #309400;"></i>
                                <a href="{{ $intro->github_link }}" target="_blank"
                                    style="font-size: 14px; color: #309400; font-weight: 500;">View profile</a>
                            </div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-regular fa-user" style="font-size: 14px; color: #666;"></i>
                                <span style="font-size: 14px; color: #666;">{{ $github_meta['followers'] ?? 0 }}
                                    followers</span>
                            </div>
                        </div>

                        @if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2 && Auth::guard('web')->user()->username == $username)
                            <a href="javascript:void(0)" class="unlink_account mt-2 d-inline-block" data-type="github"
                                style="font-size: 13px; color: #309400; text-decoration: underline; font-weight: 500;">Unlink</a>
                        @endif
                    </div>
                    @if(isset($github_meta['avatar_url']))
                        <div class="account-avatar">
                            <img src="{{ $github_meta['avatar_url'] }}" alt="Github Avatar"
                                style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        </div>
                    @else
                        <div class="social-icon-box"
                            style="width: 40px; height: 40px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                            <i class="fab fa-github" style="font-size: 22px; color: #333;"></i>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if(optional($intro)->stackoverflow_link)
            <div class="linked-account-card p-3 mb-3 radius-10" style="background: #f9f9f9; border: 1px solid #eee;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="account-info">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="m-0" style="font-size: 16px; font-weight: 700;">Stack Overflow</h5>
                        </div>
                        <p class="m-0 text-dark" style="font-size: 14px; font-weight: 500;">Connected</p>

                        <div class="mt-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-solid fa-link" style="font-size: 14px; color: #309400;"></i>
                                <a href="{{ $intro->stackoverflow_link }}" target="_blank"
                                    style="font-size: 14px; color: #309400; font-weight: 500;">View profile</a>
                            </div>
                        </div>

                        @if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2 && Auth::guard('web')->user()->username == $username)
                            <a href="javascript:void(0)" class="unlink_account mt-2 d-inline-block" data-type="stackoverflow"
                                style="font-size: 13px; color: #309400; text-decoration: underline; font-weight: 500;">Unlink</a>
                        @endif
                    </div>
                    <div class="social-icon-box"
                        style="width: 40px; height: 40px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <i class="fab fa-stack-overflow" style="font-size: 22px; color: #f48024;"></i>
                    </div>
                </div>
            </div>
        @endif

        @if(!optional($intro)->github_link && !optional($intro)->stackoverflow_link)
            <div class="text-center p-4" style="background: #fdfdfd; border: 1px dashed #ddd; border-radius: 10px;">
                <p class="text-muted m-0" style="font-size: 14px;">{{ __('No accounts linked yet.') }}</p>
                @if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2 && Auth::guard('web')->user()->username == $username)
                    <button class="btn-profile btn-outline-1 btn-small mt-3" data-bs-toggle="modal"
                        data-bs-target="#linkedAccountsModal">{{ __('Link Accounts') }}</button>
                @endif
            </div>
        @endif
    </div>
</div>

@if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2 && Auth::guard('web')->user()->username == $username)
    <!-- Linked Accounts Modal -->
    <div class="modal fade" id="linkedAccountsModal" tabindex="-1" aria-labelledby="linkedAccountsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="linkedAccountsModalLabel">{{ __('Edit Linked Accounts') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="single-profile-settings-form custom-form">
                        <div class="error_msg_container"></div>
                        <x-form.text :type="'text'" :title="__('GitHub Profile Link')" :id="'github_link_direct'"
                            :class="'form-control'" value="{{ optional($user->user_introduction)->github_link }}" />
                        <x-form.text :type="'text'" :title="__('Stack Overflow Profile Link')"
                            :id="'stackoverflow_link_direct'" :class="'form-control'"
                            value="{{ optional($user->user_introduction)->stackoverflow_link }}" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary update_linked_accounts">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>
@endif