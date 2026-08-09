<li class="chat-wrapper-contact-list-item chat_item" data-freelancer-id="{{ $clientChat?->freelancer?->id }}"
    data-live-chat-id="{{ $clientChat->id }}">
    <div class="chat-wrapper-contact-list-flex">
        <div class="list-profile-trigger position-relative">
            <div class="chat-wrapper-contact-list-thumb">
                <a href="javascript:void(0)" class="list-profile-dropdown-toggle">
                    @if($clientChat?->freelancer?->image)
                        @if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                            <img src="{{ render_frontend_cloud_image_if_module_exists('profile/' . $clientChat?->freelancer?->image, load_from: $clientChat?->freelancer?->load_from ?? '') }}"
                                alt="{{ $clientChat?->freelancer?->fullname }}">
                        @else
                            <img src="{{ asset('assets/uploads/profile/' . $clientChat?->freelancer?->image) }}"
                                alt="{{ $clientChat?->freelancer?->fullname }}">
                        @endif
                    @else
                        <img src="{{ asset('assets/static/img/author/author.jpg') }}" alt="{{ __('author') }}">
                    @endif
                </a>
                <div
                    class="notification-dots {{ Cache::has('user_is_online_' . $clientChat->freelancer?->id) ? "active" : "" }}">
                </div>
            </div>
            
            <div class="chat-header-dropdown-menu d-none list-profile-dropdown" role="menu" style="top: 100%; left: 0;">
                <a href="{{ route('freelancer.profile.details', $clientChat?->freelancer?->username ?? '') }}" target="_blank"
                    class="chat-header-dropdown-item list-profile-dropdown-item" role="menuitem">
                    <i class="fa-regular fa-user"></i>
                    <span>{{ __('View Profile') }}</span>
                </a>
                @php
                    $isFavorited = \App\Models\ChatFavorite::isFavorited(auth('web')->id(), $clientChat?->freelancer?->id, 'client');
                @endphp
                <button type="button" class="chat-header-dropdown-item list-favorite-toggle" role="menuitem"
                    data-freelancer-id="{{ $clientChat?->freelancer?->id }}">
                    <i class="{{ $isFavorited ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                    <span class="chat-favorite-label">{{ $isFavorited ? __('Remove from Favorites') : __('Add to Favorites') }}</span>
                </button>
                <button type="button" class="chat-header-dropdown-item list-end-conversation" role="menuitem"
                    data-chat-id="{{ $clientChat->id }}"
                    data-other-name="{{ $clientChat?->freelancer?->fullname }}">
                    <i class="fa-regular fa-circle-xmark" style="color: #ef4444;"></i>
                    <span>{{ __('End Conversation') }}</span>
                </button>
                <button type="button" class="chat-header-dropdown-item list-archive-toggle" role="menuitem"
                    data-chat-id="{{ $clientChat->id }}">
                    <i class="{{ $clientChat->client_archived ? 'fa-solid fa-box-open' : 'fa-solid fa-box-archive' }}"></i>
                    <span class="chat-archive-label">{{ $clientChat->client_archived ? __('Unarchive Chat') : __('Archive Chat') }}</span>
                </button>
                <button type="button" class="chat-header-dropdown-item list-report-user" role="menuitem"
                    data-freelancer-id="{{ $clientChat?->freelancer?->id }}">
                    <i class="fa-solid fa-flag" style="color: #f59e0b;"></i>
                    <span>{{ __('Report') }}</span>
                </button>
            </div>
        </div>
        <div class="chat-wrapper-contact-list-contents">
            <div class="chat-wrapper-contact-list-contents-flex flex-between">
                <h4 class="chat-wrapper-contact-list-contents-title d-flex align-items-center gap-2">
                    <a href="javascript:void(0)">{{ $clientChat->freelancer?->fullname }}</a>
                    @if(\App\Models\ChatFavorite::isFavorited(auth('web')->id(), $clientChat?->freelancer?->id, 'client'))
                        <i class="fa-solid fa-star chat-list-favorite-star" title="{{ __('Favorite') }}"></i>
                    @endif
                </h4>
                @php
                    \Carbon\Carbon::setLocale('en');
                @endphp
                <span class="chat-wrapper-contact-list-time">
                    {{ $clientChat?->freelancer?->check_online_status?->diffForHumans() }}</span>
                @php
                    \Carbon\Carbon::setLocale(app()->getLocale());
                @endphp
            </div>
            <div>
                <p class="chat-wrapper-contact-list-contents-para">
                    {{ $clientChat?->freelancer?->user_introduction?->title }}</p>
                <div class="unseen_message_count_{{$clientChat?->freelancer?->id}}">
                    @if($clientChat->client_unseen_msg_count > 0)
                        <span class="badge bg-danger text-right">{{ $clientChat->client_unseen_msg_count }}</span>
                    @endif
                </div>

            </div>
        </div>
    </div>
</li>