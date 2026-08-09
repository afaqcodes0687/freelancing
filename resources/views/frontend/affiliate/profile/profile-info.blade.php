<div class="single-profile-settings mt-4" id="display_affiliate_profile_info">
    <div class="single-profile-settings-header">
        <div class="single-profile-settings-header-flex">
            <x-form.form-title :title="__('Personal Information')" :class="'single-profile-settings-header-title'" />
            <div class="btn-wrapper">
                <a href="javascript:void(0)" class="btn-profile btn-outline-gray profile-click">
                    <i class="fa-regular fa-edit"></i>{{ __('Update Info') }}
                </a>
            </div>
        </div>
    </div>

    <div class="single-profile-settings-inner profile-border-top">
        <div class="single-profile-settings-form custom-form">
            <div class="single-flex-input">
                <div class="single-input">
                    <label for="first_name" class="label-title">{{ __('First Name') }}</label>
                    <input value="{{ $affiliate->first_name ?? '' }}" class="form-control" readonly disabled>
                </div>
                <div class="single-input">
                    <label for="last_name" class="label-title">{{ __('Last Name') }}</label>
                    <input value="{{ $affiliate->last_name ?? '' }}" class="form-control" readonly disabled>
                </div>
            </div>

            <div class="single-input">
                <label for="username" class="label-title">{{ __('Your Username') }}</label>
                <input value="{{ $affiliate->username ?? '' }}" class="form-control" readonly disabled>
            </div>

            <div class="single-input">
                <label for="email" class="label-title">{{ __('Your Email') }}</label>
                <input value="{{ $affiliate->email ?? '' }}" class="form-control" readonly disabled>
            </div>

            <div class="single-input">
                <label for="country" class="label-title">{{ __('Your Country') }}</label>
                <input value="{{ optional($affiliate->country)->country ?? '' }}" class="form-control" readonly disabled>
            </div>

            <div class="single-input">
                <label for="state" class="label-title">{{ __('Your State') }}</label>
                <input value="{{ optional($affiliate->state)->state ?? '' }}" class="form-control" readonly disabled>
            </div>

            <div class="single-input">
                <label for="city" class="label-title">{{ __('Your City') }}</label>
                <input value="{{ optional($affiliate->city)->city ?? '' }}" class="form-control" readonly disabled>
            </div>

            <div class="single-input">
                <label for="company_website" class="label-title">{{ __('Company Website') }}</label>
                <input value="{{ $affiliate->company_website ?? '' }}" class="form-control" readonly disabled>
            </div>
        </div>
    </div>
</div>
