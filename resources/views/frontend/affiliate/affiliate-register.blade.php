@extends('frontend.layout.master')
@section('site_title', __('User Register'))
@section('style')
    <style>
        .login-box {
            background-color: white;
            border-radius: 12px;
            padding: 40px;
        }

        .choose-account-single {
            cursor: pointer;
        }

        .iti {
            width: 100%;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
@endsection
@section('content')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <!-- login Area Starts -->
    <section class="login-area pat-100 pab-100 user_info_area section-bg-1">
        <div class="container">
            <div class="row gy-5 align-items-center justify-content-center">
                <div class="col-lg-6">
                    <div class="login-wrapper login-box">
                        <div class="login-wrapper-contents">
                            <h3 class="login-wrapper-contents-title">{{ __('Affiliate Registration') }}</h3>

                            <form class="login-wrapper-form custom-form" method="POST"
                                action="{{ route('affiliate.register') }}">
                                @csrf

                                {{-- Success/Error Message Container --}}
                                <div class="alert alert-success d-none" id="register_success_message"></div>
                                <div class="alert alert-danger d-none" id="register_error_message"></div>

                                <div class="input-flex-item">
                                    <div class="single-input mt-4">
                                        <label class="label-title mb-2">{{ __('First Name') }}</label>
                                        <input class="form--control" type="text" name="first_name" id="first_name" required
                                            placeholder="{{ __('Type First Name') }}">
                                    </div>
                                    <div class="single-input mt-4">
                                        <label class="label-title mb-2">{{ __('Last Name') }}</label>
                                        <input class="form--control" type="text" name="last_name" id="last_name" required
                                            placeholder="{{ __('Type Last Name') }}">
                                    </div>
                                </div>

                                <div class="single-input mt-4">
                                    <label class="label-title mb-2">{{ __('Username') }}</label>
                                    <input class="form--control" type="text" id="username" name="username" required
                                        placeholder="{{ __('Choose Username') }}">
                                    <div id="user_name_availability"></div>
                                </div>

                                <div class="single-input mt-4">
                                    <label class="label-title mb-2">{{ __('Email Address') }}</label>
                                    <input class="form--control" type="email" id="email" name="email" required
                                        placeholder="{{ __('Type Email') }}">
                                    <div id="email_availability"></div>
                                </div>

                                <div class="single-input mt-4">
                                    <label class="label-title mb-2">{{ __('Phone Number') }}</label>
                                    <input class="form--control" type="tel" id="phones" name="phone"
                                        placeholder="{{ __('Phone Number') }}">
                                    <div id="phone_availability"></div>
                                </div>

                                <div class="input-flex-item">
                                    <div class="single-input mt-0">
                                        <label class="label-title mb-2"> {{ __('Create Password') }} </label>
                                        <div class="single-input-inner">
                                            <input class="form--control" type="password" name="password" id="password"
                                                placeholder="{{ __('Type Password') }}">
                                            <div class="icon toggle-password">
                                                <div class="show-icon"> <i class="fas fa-eye-slash"></i> </div>
                                                <span class="hide-icon"> <i class="fas fa-eye"></i> </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="single-input mt-4">
                                        <label class="label-title mb-2"> {{ __('Confirm Password') }} </label>
                                        <div class="single-input-inner">
                                            <input class="form--control" type="password" name="confirm_password"
                                                id="confirm_password" placeholder="{{ __('Confirm Password') }}">
                                            <div class="icon toggle-password">
                                                <div class="show-icon"> <i class="fas fa-eye-slash"></i> </div>
                                                <span class="hide-icon"> <i class="fas fa-eye"></i> </span>
                                            </div>
                                            <span id="password_match_validation"></span>
                                        </div>
                                    </div>
                                </div>
                                <span id="password_validation"></span>

                                <!-- <div class="single-input mt-4">
                                                <label class="label-title mb-3">{{ __('Register As*') }}</label>
                                                <select name="user_type" class="form--control">
                                                    <option value="client">{{ __('Client') }}</option>
                                                    <option value="freelancer">{{ __('Freelancer') }}</option>
                                                </select>
                                            </div> -->

                                <div class="single-input mt-4">
                                    <label class="label-title mb-2">{{ __('Account Display Name') }}</label>
                                    <input class="form--control" type="text" name="account_display_name"
                                        id="account_display_name" required
                                        placeholder="{{ __('Your Brand or Store Name') }}">
                                </div>

                                <div class="single-input mt-4">
                                    <label class="label-title mb-2">{{ __('Company Website') }}</label>
                                    <input class="form--control" type="url" name="company_website" id="company_website"
                                        placeholder="https://example.com">
                                </div>



                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="terms_condition"
                                        name="terms_condition" required>
                                    <label class="form-check-label" for="terms_condition">
                                        {{ __('I agree to the') }}
                                        <a href="{{ url(get_static_option('toc_page_link') ?? '#') }}" target="_blank"
                                            class="fw-bold">{{ __('Terms & Conditions') }}</a>
                                        {{ __('and') }}
                                        <a href="{{ url(get_static_option('privacy_policy_link') ?? '#') }}" target="_blank"
                                            class="fw-bold">{{ __('Privacy Policy') }}</a>
                                    </label>
                                </div>

                                @if(get_static_option('site_google_captcha_enable'))
                                    <div class="my-3">
                                        <div class="g-recaptcha" data-sitekey="{{ get_static_option('recaptcha_site_key') }}">
                                        </div>
                                        @error('g-recaptcha-response')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                                <button class="submit-btn w-100 mt-4 sign_up_now_button"
                                    type="submit">{{ __('Sign Up Now') }} <span
                                        id="user_register_load_spinner"></span></button>

                                <span class="account color-light mt-3">
                                    {{ __('Already have an account?') }}
                                    <a class="color-one" href="{{ route('affiliate.login') }}">{{ __('Login') }}</a>
                                </span>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- login Area end -->
@endsection


{{-- todo register script --}}
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                // todo continue

                $(document).on('keyup', '#username', function () {
                    let username = $(this).val();
                    let usernameRegex = /^[a-zA-Z0-9]+$/;
                    if (usernameRegex.test(username) && username != '') {
                        $.ajax({
                            url: "{{ route('affiliate.user.name.availability') }}",
                            type: 'post',
                            data: {
                                username: username
                            },
                            success: function (res) {
                                if (res.status == 'available') {
                                    $("#user_name_availability").html(
                                        "<span style='color: green;'>" + res.msg +
                                        "</span>");
                                } else {
                                    $("#user_name_availability").html(
                                        "<span style='color: red;'>" + res.msg +
                                        "</span>");
                                }
                            }
                        });
                    } else {
                        $("#user_name_availability").html(
                            "<span style='color: red;'>{{ __('Enter valid username') }}</span>");
                    }
                });

                $(document).on('keyup', '#email', function () {
                    let email = $(this).val();
                    let emailRegex = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
                    if (emailRegex.test(email) && email != '') {
                        $.ajax({
                            url: "{{ route('affiliate.user.email.availability') }}",
                            type: 'post',
                            data: {
                                email: email
                            },
                            success: function (res) {
                                if (res.status == 'available') {
                                    $("#email_availability").html(
                                        "<span style='color: green;'>" + res.msg +
                                        "</span>");
                                } else {
                                    $("#email_availability").html(
                                        "<span style='color: red;'>" + res.msg +
                                        "</span>");
                                }
                            }
                        });
                    } else {
                        $("#email_availability").html(
                            "<span style='color: red;'>{{ __('Enter valid email') }}</span>");
                    }
                });

                // Initialize intl-tel-input
                const phoneInput = document.querySelector("#phones");
                const iti = window.intlTelInput(phoneInput, {
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    separateDialCode: true,
                    allowDropdown: true,
                    initialCountry: "pk",
                    preferredCountries: ["pk", "sa", "us", "gb"],
                });

                $(document).on('keyup', '#phones', function () {
                    const errorMsg = $("#phone_availability");
                    errorMsg.html("");

                    if (phoneInput.value.trim()) {
                        if (iti.isValidNumber()) {
                            errorMsg.html("<span style='color: green;'>{{ __('Valid phone number') }}</span>");
                        } else {
                            errorMsg.html("<span style='color: red;'>{{ __('Invalid phone number') }}</span>");
                        }
                    } else {
                        errorMsg.html("<span style='color: red;'>{{ __('Phone number is required') }}</span>");
                    }
                });

                $(document).on('blur', '#phones', function () {
                    if (iti.isValidNumber()) {
                        let phone = iti.getNumber();
                        $.ajax({
                            url: "{{ route('affiliate.user.phone.number.availability') }}",
                            type: 'post',
                            data: {
                                phone: phone
                            },
                            success: function (res) {
                                if (res.status == 'available') {
                                    $("#phone_availability").html(
                                        "<span style='color: green;'>" + res.msg +
                                        "</span>");
                                } else {
                                    $("#phone_availability").html(
                                        "<span style='color: red;'>" + res.msg +
                                        "</span>");
                                }
                            }
                        });
                    }
                });

                $(document).on('keyup', '#password, #confirm_password', function () {
                    let password = $("#password").val();
                    let confirmPassword = $("#confirm_password").val();
                    $.ajax({
                        url: "{{ route('user.password.match.validation') }}",
                        type: 'post',
                        data: {
                            password: password,
                            confirm_password: confirmPassword
                        },
                        success: function (res) {
                            if (res.status == 'match') {
                                $("#password_match_validation").html("<span style='color: green;'>" + res.msg + "</span>");
                            } else {
                                $("#password_match_validation").html("<span style='color: red;'>" + res.msg + "</span>");
                            }
                        }
                    });
                });

                $(document).on('keyup', '#password', function () {
                    let password = $(this).val();
                    let validations = [
                        { regex: /.{6,}/, message: 'Password must be at least 6 characters', valid: false },
                        { regex: /[a-z]/, message: 'Password must include lowercase letters', valid: false },
                        { regex: /[A-Z]/, message: 'Password must include uppercase letters', valid: false },
                        { regex: /\d/, message: 'Password must include numbers', valid: false },
                        { regex: /[@$!%*#?&]/, message: 'Password must include special characters', valid: false },
                    ];

                    validations.forEach(function (validation) {
                        if (validation.regex.test(password)) {
                            validation.valid = true;
                        }
                    });

                    let errorHtml = '<ul>';
                    validations.forEach(function (validation) {
                        if (validation.valid) {
                            errorHtml += '<li style="color: green;">' + validation.message + '</li>';
                        } else {
                            errorHtml += '<li style="color: red;">' + validation.message + '</li>';
                        }
                    });
                    errorHtml += '</ul>';

                    $("#password_validation").html(errorHtml);
                });

                //confirm signup
                $(document).on('click', '.sign_up_now_button', function (e) {
                    e.preventDefault()
                    $('#user_register_load_spinner').html('<i class="fas fa-spinner fa-pulse"></i>')

                    let first_name = $('#first_name').val();
                    let last_name = $('#last_name').val();
                    let username = $('#username').val();
                    let email = $('#email').val();
                    let phone = iti.getNumber();
                    let password = $('#password').val();
                    let confirm_password = $('#confirm_password').val();
                    let terms_condition = $('#terms_condition:checked').val();
                    let account_display_name = $('#account_display_name').val();
                    let company_website = $('#company_website').val();

                    let recaptchaResponse;
                    if ($('.g-recaptcha').length > 0) {
                        recaptchaResponse = grecaptcha.getResponse();
                    }

                    let erContainer = $("#register_error_message");
                    erContainer.html('').addClass('d-none');

                    // Frontend validation
                    let errors = [];
                    let nameRegex = /^[a-zA-Z\s'-]+$/;
                    let urlRegex = /^(https?:\/\/)?([\w\d-]+\.)+[\w\d]{2,}(\/.*)?$/i;

                    if (!first_name || !nameRegex.test(first_name)) {
                        errors.push('Please enter a valid first name.');
                    }
                    if (!last_name || !nameRegex.test(last_name)) {
                        errors.push('Please enter a valid last name.');
                    }
                    if (!username) {
                        errors.push('Please enter a username.');
                    }
                    if (!email) {
                        errors.push('Please enter an email address.');
                    }
                    if (!phone) {
                        errors.push('Please enter a phone number.');
                    }
                    if (!password) {
                        errors.push('Please enter a password.');
                    }
                    if (!confirm_password) {
                        errors.push('Please confirm your password.');
                    }
                    if (!account_display_name) {
                        errors.push('Please enter an account display name.');
                    }
                    if (!company_website || !urlRegex.test(company_website)) {
                        errors.push('Please enter a valid company website URL.');
                    }

                    if (!terms_condition) {
                        errors.push('You must accept the Terms and Conditions and Privacy Policy.');
                    }
                    if (document.getElementById('recaptcha_element_register') && !recaptchaResponse) {
                        errors.push('Please complete the reCAPTCHA.');
                    }

                    if (errors.length > 0) {
                        let errorHtml = '';
                        errors.forEach(function (error) {
                            errorHtml += '<p>' + error + '</p>';
                        });
                        erContainer.html(errorHtml).removeClass('d-none');
                        $('#user_register_load_spinner').html('');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('affiliate.register') }}",
                        type: 'post',
                        data: {
                            first_name: first_name,
                            last_name: last_name,
                            username: username,
                            email: email,
                            phone: phone,
                            password: password,
                            confirm_password: confirm_password,
                            terms_condition: terms_condition,
                            'g-recaptcha-response': recaptchaResponse,
                            account_display_name: account_display_name,
                            company_website: company_website
                        },
                        error: function (res) {
                            let errors = res.responseJSON;
                            let errorMsg = '';
                            if (errors && errors.errors) {
                                $.each(errors.errors, function (index, value) {
                                    errorMsg += '<p>' + value + '</p>';
                                });
                            } else if (errors && errors.message) {
                                errorMsg = '<p>' + errors.message + '</p>';
                            } else {
                                errorMsg = '<p>Registration failed. Please try again.</p>';
                            }
                            $('#register_error_message').removeClass('d-none').html(errorMsg);
                            $('#register_success_message').addClass('d-none').html('');
                            $('#user_register_load_spinner').html('');
                        },
                        success: function (res) {
                            if (res.status === 'success') {
                                $('#register_success_message').removeClass('d-none').html(res.msg);
                                $('#register_error_message').addClass('d-none').html('');
                                setTimeout(function () {
                                    window.location.href = res.redirect_url;
                                }, 1500);
                            } else if (res.status === 'error' && res.msg) {
                                $('#register_error_message').removeClass('d-none').html(res.msg);
                                $('#register_success_message').addClass('d-none').html('');
                                $('#user_register_load_spinner').html('');
                            }
                        }
                    });
                })

            });
        }(jQuery));
    </script>



@endsection