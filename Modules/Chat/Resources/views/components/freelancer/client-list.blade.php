<li class="chat-wrapper-contact-list-item chat_item" data-client-id="{{ $freelancerChat?->client?->id }}"
    data-live-chat-id="{{ $freelancerChat->id }}">
    <div class="chat-wrapper-contact-list-flex">
        <div class="list-profile-trigger position-relative">
            <div class="chat-wrapper-contact-list-thumb">
                <a href="javascript:void(0)" class="list-profile-dropdown-toggle">
                    @if($freelancerChat?->client?->image)
                        @if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                            <img src="{{ render_frontend_cloud_image_if_module_exists('profile/' . $freelancerChat?->client?->image, load_from: $freelancerChat?->client?->load_from ?? '') }}"
                                alt="{{ $freelancerChat->client?->fullname }}">
                        @else
                            <img src="{{ asset('assets/uploads/profile/' . $freelancerChat?->client?->image) }}"
                                alt="{{ $freelancerChat->client?->fullname }}">
                        @endif
                    @else
                        <img src="{{ asset('assets/static/img/author/author.jpg') }}" alt="{{ __('author') }}">
                    @endif
                </a>
                <div
                    class="notification-dots {{ Cache::has('user_is_online_' . $freelancerChat?->client?->id) ? "active" : "" }}">
                </div>
            </div>
            <div class="chat-header-dropdown-menu d-none list-profile-dropdown" role="menu" style="top: 100%; left: 0;">
                @php
                    $isFavorited = \App\Models\ChatFavorite::isFavorited(auth('web')->id(), $freelancerChat?->client?->id, 'freelancer');
                @endphp
                <button type="button" class="chat-header-dropdown-item list-favorite-toggle" role="menuitem"
                    data-client-id="{{ $freelancerChat?->client?->id }}">
                    <i class="{{ $isFavorited ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                    <span class="chat-favorite-label">{{ $isFavorited ? __('Remove from Favorites') : __('Add to Favorites') }}</span>
                </button>
                <button type="button" class="chat-header-dropdown-item list-end-conversation" role="menuitem"
                    data-chat-id="{{ $freelancerChat->id }}"
                    data-other-name="{{ $freelancerChat?->client?->fullname }}">
                    <i class="fa-regular fa-circle-xmark" style="color: #ef4444;"></i>
                    <span>{{ __('End Conversation') }}</span>
                </button>
                <button type="button" class="chat-header-dropdown-item list-archive-toggle" role="menuitem"
                    data-chat-id="{{ $freelancerChat->id }}">
                    <i class="{{ $freelancerChat->freelancer_archived ? 'fa-solid fa-box-open' : 'fa-solid fa-box-archive' }}"></i>
                    <span class="chat-archive-label">{{ $freelancerChat->freelancer_archived ? __('Unarchive Chat') : __('Archive Chat') }}</span>
                </button>
                <button type="button" class="chat-header-dropdown-item list-report-user" role="menuitem"
                    data-client-id="{{ $freelancerChat?->client?->id }}">
                    <i class="fa-solid fa-flag" style="color: #f59e0b;"></i>
                    <span>{{ __('Report') }}</span>
                </button>
            </div>
        </div>
        <div class="chat-wrapper-contact-list-contents">
            <div class="chat-wrapper-contact-list-contents-flex flex-between">
                <h4 class="chat-wrapper-contact-list-contents-title d-flex align-items-center gap-2">
                    <a href="javascript:void(0)">{{ $freelancerChat?->client?->fullname }}</a>
                    @if(\App\Models\ChatFavorite::isFavorited(auth('web')->id(), $freelancerChat?->client?->id, 'freelancer'))
                        <i class="fa-solid fa-star chat-list-favorite-star" title="{{ __('Favorite') }}"></i>
                    @endif
                </h4>
                @php
                    \Carbon\Carbon::setLocale('en');
                @endphp
                <span
                    class="chat-wrapper-contact-list-time">{{ $freelancerChat->client?->check_online_status?->diffForHumans() }}</span>
                @php
                    \Carbon\Carbon::setLocale(app()->getLocale());
                @endphp

            </div>
            <div>
                <p class="chat-wrapper-contact-list-contents-para">
                    {{ $freelancerChat?->client?->user_introduction?->title ?? '' }}</p>
                <div class="unseen_message_count_{{$freelancerChat?->client->id}}">
                    @if($freelancerChat->freelancer_unseen_msg_count > 0)
                        <span class="badge bg-danger text-right">{{ $freelancerChat->freelancer_unseen_msg_count }}</span>
                    @endif
                </div>

            </div>
        </div>
    </div>
</li>