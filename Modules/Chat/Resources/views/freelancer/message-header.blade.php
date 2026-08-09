@php
    $isFavorited = \App\Models\ChatFavorite::isFavorited(auth('web')->id(), $data->client->id, 'freelancer');
@endphp
<div class="chat-wrapper-details-header profile-border-bottom flex-between" id="livechat-message-header"
    data-client-id="{{ $data->client->id }}" data-live-chat-id="{{ $data->id }}">
    <div class="chat-wrapper-details-header-left d-flex gap-2 align-items-center">
        <div class="chat-header-profile-trigger position-relative">
            <button type="button" class="chat-header-profile-btn" id="chatProfileDropdownToggle"
                aria-expanded="false" aria-haspopup="true">
                <div class="chat-wrapper-details-header-left-author d-flex gap-2 align-items-center">
                    @if ($data->client?->image)
                        <div class="chat-wrapper-contact-list-thumb-main chat-wrapper-contact-list-thumb">
                            @if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                                <img src="{{ render_frontend_cloud_image_if_module_exists('profile/' . $data?->client?->image, load_from: $data?->client?->load_from ?? '') }}"
                                    alt="{{ $data->client?->fullname }}">
                            @else
                                <img src="{{ asset('assets/uploads/profile/' . $data->client?->image) }}"
                                    alt="{{ $data->client?->fullname }}">
                            @endif
                        </div>
                    @else
                        <div class="chat-wrapper-contact-list-thumb-main chat-wrapper-contact-list-thumb">
                            <img src="{{ asset('assets/static/img/author/author.jpg') }}" alt="{{ __('author') }}">
                        </div>
                    @endif
                    <div class="chat-wrapper-contact-list-thumb-contents text-start">
                        <h5 class="chat-wrapper-details-header-title mb-0">{{ $data->client?->fullname }}</h5>
                    </div>
                    <span class="chat-header-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                </div>
            </button>
            <div class="chat-header-dropdown-menu d-none" id="chatProfileDropdown" role="menu">
                <button type="button" class="chat-header-dropdown-item chat-favorite-toggle" role="menuitem"
                    data-client-id="{{ $data->client->id }}"
                    data-is-favorited="{{ $isFavorited ? '1' : '0' }}">
                    <i class="{{ $isFavorited ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                    <span class="chat-favorite-label">{{ $isFavorited ? __('Remove from Favorites') : __('Add to Favorites') }}</span>
                </button>
                <button type="button" class="chat-header-dropdown-item header-end-conversation" role="menuitem"
                    data-chat-id="{{ $data->id }}"
                    data-other-name="{{ $data->client?->fullname }}">
                    <i class="fa-regular fa-circle-xmark" style="color: #ef4444;"></i>
                    <span>{{ __('End Conversation') }}</span>
                </button>
            </div>
        </div>
    </div>
    <div class="chat-wrapper-details-header-right">
        <div class="flex-btn gap-2">
            <button class="btn-profile btn-outline-1 color-one get_client_id" data-client-id="{{ $data->client?->id }}"
                data-bs-toggle="modal" data-bs-target="#exampleModal">{{ __('Send Offer') }}
            </button>
        </div>
    </div>
</div>
