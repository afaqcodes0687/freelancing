@php
    $isFavorited = \App\Models\ChatFavorite::isFavorited(auth('web')->id(), $data->freelancer->id, 'client');
@endphp
<div class="chat-wrapper-details-header profile-border-bottom flex-between" id="livechat-message-header"
    data-freelancer-id="{{ $data->freelancer->id }}" data-live-chat-id="{{ $data->id }}">
    <div class="chat-wrapper-details-header-left d-flex gap-2 align-items-center">
        <div class="chat-header-profile-trigger position-relative">
            <button type="button" class="chat-header-profile-btn" id="chatProfileDropdownToggle"
                aria-expanded="false" aria-haspopup="true">
                <div class="chat-wrapper-details-header-left-author d-flex gap-2 align-items-center">
                    @if ($data->freelancer?->image)
                        <div class="chat-wrapper-contact-list-thumb-main chat-wrapper-contact-list-thumb">
                            @if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                                <img src="{{ render_frontend_cloud_image_if_module_exists('profile/' . $data?->freelancer?->image, load_from: $data?->freelancer?->load_from ?? '') }}"
                                    alt="{{ $data->freelancer?->fullname }}">
                            @else
                                <img src="{{ asset('assets/uploads/profile/' . $data->freelancer?->image) }}"
                                    alt="{{ $data->freelancer?->fullname }}">
                            @endif
                        </div>
                    @else
                        <div class="chat-wrapper-contact-list-thumb-main chat-wrapper-contact-list-thumb">
                            <img src="{{ asset('assets/static/img/author/author.jpg') }}" alt="{{ __('author') }}">
                        </div>
                    @endif
                    <div class="chat-wrapper-contact-list-thumb-contents text-start">
                        <h5 class="chat-wrapper-details-header-title mb-0">{{ $data->freelancer?->fullname }}</h5>
                        <p class="chat-wrapper-details-header-para mb-0">{{ $data->freelancer?->user_introduction?->title }}</p>
                    </div>
                    <span class="chat-header-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                </div>
            </button>
            <div class="chat-header-dropdown-menu d-none" id="chatProfileDropdown" role="menu">
                <a href="{{ route('freelancer.profile.details', $data?->freelancer?->username) }}" target="_blank"
                    class="chat-header-dropdown-item" role="menuitem">
                    <i class="fa-regular fa-user"></i>
                    <span>{{ __('View Profile') }}</span>
                </a>
                <button type="button" class="chat-header-dropdown-item chat-favorite-toggle" role="menuitem"
                    data-freelancer-id="{{ $data->freelancer->id }}"
                    data-is-favorited="{{ $isFavorited ? '1' : '0' }}">
                    <i class="{{ $isFavorited ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                    <span class="chat-favorite-label">{{ $isFavorited ? __('Remove from Favorites') : __('Add to Favorites') }}</span>
                </button>
                <button type="button" class="chat-header-dropdown-item header-end-conversation" role="menuitem"
                    data-chat-id="{{ $data->id }}"
                    data-other-name="{{ $data->freelancer?->fullname }}">
                    <i class="fa-regular fa-circle-xmark" style="color: #ef4444;"></i>
                    <span>{{ __('End Conversation') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
