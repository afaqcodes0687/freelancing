<style>
    .image-popup-overlay {
        display:none;
        position:fixed;
        z-index:9999;
        background:rgba(0,0,0,0.85);
        top:0; left:0; right:0; bottom:0;
        justify-content:center;
        align-items:center;
    }
    .image-popup-overlay img {
        max-width:90%;
        max-height:90%;
        border:4px solid #fff;
        border-radius:8px;
        width: 62%;
    }
    .popup-img-item {
        cursor:pointer;
        border-radius:6px;
        transition:0.2s;
    }
    .popup-img-item:hover {
        opacity:0.8;
    }
</style>

<div class="compare-profile-and-identity">
    <div class="row g-4 gy-5">

        <!-- USER PROFILE INFO -->
        <div class="col-lg-6">
            <div class="user-profile userProfileDetails">
                <div class="userProfileDetails__header">
                    <h5 class="userProfileDetails__title">{{__('User Profile Info')}}</h5>
                    <input type="hidden" id="user_id_for_verified_status" value="{{ $user_details->id }}">
                </div>

                <div class="userDetails__wrapper userProfile__details mt-3">
                    <div class="userProfile__details__thumb mb-3">
                        @if(!empty($user_details->image))
                            <img onclick="openImage(this.src)" class="popup-img-item"
                                 src="{{ asset('assets/uploads/profile/'.$user_details->image) }}" alt="profile-img">
                        @else
                            <img src="{{ asset('assets/static/img/author/author.jpg') }}" alt="freelancer-image">
                        @endif
                    </div>

                    <p class="userDetails__wrapper__item">
                        <strong>{{ __('User Type:') }}</strong>
                        @if($user_details->user_type==1)
                            {{ __('Client') }}
                        @else
                            {{ __('Freelancer') }}
                        @endif
                    </p>

                    <p class="userDetails__wrapper__item"><strong>{{ __('Hourly Rate:') }}</strong> {{ $user_details->hourly_rate ?? '' }}</p>
                    <p class="userDetails__wrapper__item"><strong>{{ __('Full Name:') }}</strong>{{ $user_details->first_name.' '.$user_details->last_name }}</p>
                    <p class="userDetails__wrapper__item"><strong>{{ __('Username:') }}</strong>{{ $user_details->username ?? '' }}</p>
                    <p class="userDetails__wrapper__item"><strong>{{ __('Email:') }}</strong>{{ $user_details->email ?? '' }}</p>
                    <p class="userDetails__wrapper__item"><strong>{{ __('Phone:') }}</strong>{{ $user_details->phone ?? '' }}</p>
                    <p class="userDetails__wrapper__item"><strong>{{ __('Country:') }}</strong>{{ optional($user_details->user_country)->country ?? '' }}</p>

                    @if(!moduleExists('CoinPaymentGateway'))
                        <p class="userDetails__wrapper__item"><strong>{{ __('State:') }}</strong>{{ optional($user_details->user_state)->state ?? '' }}</p>
                        <p class="userDetails__wrapper__item"><strong>{{ __('City:') }}</strong>{{ optional($user_details->user_city)->city ?? '' }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- USER IDENTITY INFO -->
        <div class="col-lg-6">
            <div class="user-identity userProfileDetails">
                <div class="userProfileDetails__header">
                    <h5 class="userProfileDetails__title">{{__('User Identity Info')}}</h5>
                </div>

                <div class="userDetails__wrapper userProfile__details mt-3">

                    @if(!empty($user_identity_details))

                        <div class="userProfile__details__thumb mb-3">
                            <img onclick="openImage(this.src)" class="popup-img-item" style="width:80%"
                                 src="{{ asset('assets/uploads/verification/'.$user_identity_details->front_image) }}" alt="front-img">

                            <img onclick="openImage(this.src)" class="popup-img-item mt-3" style="width:80%"
                                 src="{{ asset('assets/uploads/verification/'.$user_identity_details->back_image) }}" alt="back-img">
                        </div>

                        <p class="userDetails__wrapper__item"><strong>{{ __('National ID:') }}</strong>{{ $user_identity_details->national_id_number ?? '' }}</p>
                        <p class="userDetails__wrapper__item"><strong>{{ __('Documents Type:') }}</strong>{{ $user_identity_details->verify_by ?? '' }}</p>
                        <p class="userDetails__wrapper__item"><strong>{{ __('Country:') }}</strong>{{ optional($user_identity_details->user_country)->country ?? '' }}</p>

                        @if(!moduleExists('CoinPaymentGateway'))
                            <p class="userDetails__wrapper__item"><strong>{{ __('State:') }}</strong>{{ optional($user_identity_details->user_state)->state ?? '' }}</p>
                            <p class="userDetails__wrapper__item"><strong>{{ __('City:') }}</strong>{{ optional($user_identity_details->user_city)->city ?? '' }}</p>
                            <p class="userDetails__wrapper__item"><strong>{{ __('Zipcode:') }}</strong>{{ $user_identity_details->zipcode ?? '' }}</p>
                        @endif

                        <p class="userDetails__wrapper__item"><strong>{{ __('Address:') }}</strong>{{ $user_identity_details->address ?? '' }}</p>

                    @else
                        <div class="userProfileDetails__noInfo">
                            <h3 class="userProfileDetails__noInfo__title">{{ __('No Information') }}</h3>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<!-- IMAGE POPUP -->
<div class="image-popup-overlay" id="img_popup">
    <img id="popup_img" src="" alt="">
</div>

<script>
    function openImage(src) {
        document.getElementById('popup_img').src = src;
        document.getElementById('img_popup').style.display = 'flex';
    }
    document.getElementById('img_popup').addEventListener('click', function () {
        this.style.display = 'none';
    });
</script>
