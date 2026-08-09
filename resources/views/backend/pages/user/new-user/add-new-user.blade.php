@extends('backend.layout.master')
@section('title', __('Add New User'))
@section('style')
    <x-media.css />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <style>
        .iti {
            width: 100%;
        }
    #admin_password_validation ul { padding-left: 16px; margin: 4px 0 0; }
            #admin_password_validation li { font-size: 12px; }
        </style>
@endsection
@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="customMarkup__single__item__flex">
                            <h4 class="customMarkup__single__title">{{ __('Add New User') }}</h4>
                        </div>
                        <div class="customMarkup__single__inner mt-4">
                            <x-validation.error />
                            <form action="{{ route('admin.user.add') }}" method="POST" enctype="multipart/form-data"
                                id="add_user_form">
                                @csrf
                                <div class="row">
                                    {{-- First Name --}}
                                    <div class="col-lg-6">
                                        <div class="single-input mb-3">
                                            <label class="label-title">{{ __('First Name') }}</label>
                                            <input type="text" name="first_name" id="admin_first_name"
                                                class="form-control" value="{{ old('first_name', '') }}"
                                                placeholder="{{ __('Enter first name') }}">
                                            <span class="admin_first_name_error"></span>
                                        </div>
                                    </div>
                                    {{-- Last Name --}}
                                    <div class="col-lg-6">
                                        <div class="single-input mb-3">
                                            <label class="label-title">{{ __('Last Name') }}</label>
                                            <input type="text" name="last_name" id="admin_last_name"
                                                class="form-control" value="{{ old('last_name', '') }}"
                                                placeholder="{{ __('Enter last name') }}">
                                            <span class="admin_last_name_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <x-form.text :title="__('Username')" :type="__('text')" :name="'username'"
                                            :value="old('username', '')" :placeholder="__('Enter username')" />
                                    </div>
                                    <div class="col-lg-6">
                                        <x-form.text :title="__('Email Address')" :type="__('email')" :name="'email'"
                                            :value="old('email', '')" :placeholder="__('Enter email')" />
                                    </div>
                                    {{-- Phone Number --}}
                                    <div class="col-lg-6">
                                        <div class="single-input mb-3">
                                            <label class="label-title">{{ __('Phone Number') }}</label>
                                            <input type="tel" id="admin_phone_input" class="form-control"
                                                placeholder="{{ __('Enter phone') }}">
                                            <input type="hidden" name="phone" id="admin_phone_hidden"
                                                value="{{ old('phone', '') }}">
                                        </div>
                                    </div>
                                    {{-- User Type --}}
                                    <div class="col-lg-6">
                                        <div class="single-input mb-3">
                                            <label class="label-title">{{ __('User Type') }}</label>
                                            <select name="user_type" class="form-control">
                                                <option value="">{{ __('Select Type') }}</option>
                                                <option value="1">{{ __('Client') }}</option>
                                                <option value="2">{{ __('Freelancer') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    {{-- Password --}}
                                    <div class="col-lg-6">
                                        <div class="single-input mb-3">
                                            <label class="label-title">{{ __('Password') }}</label>
                                            <input type="text" name="password" id="admin_password"
                                                class="form-control" value="{{ old('password', '') }}"
                                                placeholder="{{ __('Enter password') }}">
                                            <span id="admin_password_validation"></span>
                                        </div>
                                    </div>
                                    {{-- Confirm Password --}}
                                    <div class="col-lg-6">
                                        <div class="single-input mb-3">
                                            <label class="label-title">{{ __('Confirm Password') }}</label>
                                            <input type="text" name="password_confirmation"
                                                id="admin_confirm_password" class="form-control"
                                                value="{{ old('password_confirmation', '') }}"
                                                placeholder="{{ __('Confirm password') }}">
                                            <span id="admin_password_match_validation"></span>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <x-btn.submit :title="__('Save')"
                                    :class="'btn-profile btn-bg-1 mt-4 pr-4 pl-4 validate_subscription_type'" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-media.markup />
@endsection

@section('script')
    <x-media.js />
    @include('subscription::backend.subscription.subscription-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {

                // ─── Phone Input ──────────────────────────────────────────────
                const phoneInputEl = document.querySelector("#admin_phone_input");
                const iti = window.intlTelInput(phoneInputEl, {
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    separateDialCode: true,
                    allowDropdown: true,
                    initialCountry: "pk",
                    preferredCountries: ["pk", "sa", "us", "gb"],
                });

                function updatePlaceholder() {
                    if (typeof intlTelInputUtils !== 'undefined') {
                        var countryCode = iti.getSelectedCountryData().iso2;
                        var example = intlTelInputUtils.getExampleNumber(countryCode, false,
                            intlTelInputUtils.numberType.MOBILE);
                        phoneInputEl.placeholder = example || '';
                    }
                }
                phoneInputEl.addEventListener('countrychange', updatePlaceholder);
                setTimeout(updatePlaceholder, 800);

                // Push full E.164 number into hidden input before submit
                $('#add_user_form').on('submit', function() {
                    if (iti.isValidNumber()) {
                        $('#admin_phone_hidden').val(iti.getNumber());
                    } else {
                        $('#admin_phone_hidden').val(phoneInputEl.value);
                    }
                });

                // ─── First Name & Last Name Validation ────────────────────────
                $(document).on('keyup', '#admin_first_name, #admin_last_name', function() {
                    let firstName = $('#admin_first_name').val().trim();
                    let lastName  = $('#admin_last_name').val().trim();
                    let nameRegex = /^[a-zA-Z\s]+$/;

                    $('.admin_first_name_error, .admin_last_name_error').html('');

                    if (firstName.length > 0 && !nameRegex.test(firstName)) {
                        $('.admin_first_name_error').html(
                            "<span style='color:red;'>Sorry! No special characters or numbers</span>");
                        return;
                    }

                    if (lastName.length > 0 && !nameRegex.test(lastName)) {
                        $('.admin_last_name_error').html(
                            "<span style='color:red;'>Sorry! No special characters or numbers</span>");
                        return;
                    }

                    if (firstName !== '' && lastName !== '' &&
                        firstName.toLowerCase() === lastName.toLowerCase()) {
                        $('.admin_last_name_error').html(
                            "<span style='color:red;'>First name and Last name cannot be the same</span>"
                        );
                    }
                });

                // ─── Password Strength Validation ─────────────────────────────
                $(document).on('keyup', '#admin_password', function() {
                    let password = $(this).val();
                    let validations = [
                        { regex: /.{6,}/,       message: 'Password must be at least 6 characters', valid: false },
                        { regex: /[a-z]/,        message: 'Password must include lowercase letters', valid: false },
                        { regex: /[A-Z]/,        message: 'Password must include uppercase letters', valid: false },
                        { regex: /\d/,           message: 'Password must include numbers',           valid: false },
                        { regex: /[@$!%*#?&]/,   message: 'Password must include special characters', valid: false },
                    ];

                    validations.forEach(function(v) {
                        if (v.regex.test(password)) v.valid = true;
                    });

                    let html = '<ul>';
                    validations.forEach(function(v) {
                        html += '<li style="color:' + (v.valid ? 'green' : 'red') + ';">' + v.message + '</li>';
                    });
                    html += '</ul>';
                    $('#admin_password_validation').html(html);

                    // also re-check match
                    checkMatch();
                });

                // ─── Confirm Password Match ───────────────────────────────────
                function checkMatch() {
                    let password        = $('#admin_password').val();
                    let confirmPassword = $('#admin_confirm_password').val();
                    if (confirmPassword === '') {
                        $('#admin_password_match_validation').html('');
                        return;
                    }
                    if (password === confirmPassword) {
                        $('#admin_password_match_validation').html(
                            "<span style='color:green;'>Passwords match</span>");
                    } else {
                        $('#admin_password_match_validation').html(
                            "<span style='color:red;'>Passwords do not match</span>");
                    }
                }
                $(document).on('keyup', '#admin_password, #admin_confirm_password', checkMatch);

            });
        }(jQuery));
    </script>
@endsection