<div class="single-profile-settings mt-4" id="display_client_profile_info">
    <div class="single-profile-settings-header">
        <x-validation.error />
        <div class="single-profile-settings-header-flex">
            <x-form.form-title :title="__('Personal Information')" :class="'single-profile-settings-header-title'" />
            <div class="btn-wrapper">
                <a href="javascript:void(0)" class="btn-profile btn-outline-gray profile-click"><i
                        class="fa-regular fa-edit"></i>{{ __('Edit Info') }}</a>
            </div>
        </div>
    </div>
    <div class="single-profile-settings-inner profile-border-top">
        <form id="inline_client_profile_form" method="post">
            @csrf
            <div class="single-profile-settings-form custom-form">
                <div class="single-flex-input">
                    <div class="single-input">
                        <label for="inline_first_name" class="label-title">{{ __('First Name') }}</label>
                        <input type="text" name="first_name" id="inline_first_name" value="{{ Auth::guard('web')->user()->first_name ?? '' }}" class="form-control" placeholder="{{ __('Type First Name') }}">
                    </div>
                    <div class="single-input">
                        <label for="inline_last_name" class="label-title">{{ __('Last Name') }}</label>
                        <input type="text" name="last_name" id="inline_last_name" value="{{ Auth::guard('web')->user()->last_name ?? '' }}" class="form-control" placeholder="{{ __('Type Last Name') }}">
                    </div>
                </div>
                 <div class="single-input">
                    <label for="inline_username" class="label-title">{{ __('Your Username') }}</label>
                    <input type="text" name="username" id="inline_username" value="{{ Auth::guard('web')->user()->username ?? '' }}" class="form-control" placeholder="{{ __('Type Username') }}">
                </div>
                <div class="single-input">
                    <label for="inline_email" class="label-title">{{ __('Your Email') }}</label>
                    <input type="email" name="email" id="inline_email" value="{{ Auth::guard('web')->user()->email ?? '' }}" class="form-control" placeholder="{{ __('Type Email') }}">
                </div>
                <div class="single-input">
                    <label for="inline_country_id" class="label-title">{{ __('Your Country') }}</label>
                    <select name="country" id="inline_country_id" class="form--control country_select2">
                        <option value="">{{ __('Select Country') }}</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ (Auth::guard('web')->user()->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                {{ $country->country }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="single-input">
                    <label for="inline_state_id" class="label-title">{{ __('Your State') }}</label>
                     <select name="state" id="inline_state_id" class="form--control state_select2 get_country_state_inline">
                        <option value="">{{ __('Select State') }}</option>
                    </select>
                </div>

                <div class="single-input">
                    <label for="inline_city_id" class="label-title">{{ __('Your City') }}</label>
                     <select name="city" id="inline_city_id" class="form--control city_select2 get_state_city_inline">
                        <option value="">{{ __('Select City') }}</option>
                    </select>
                </div>
                
                <div class="btn-wrapper mt-4">
                    <button type="submit" class="btn-profile btn-bg-1">{{ __('Submit') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
