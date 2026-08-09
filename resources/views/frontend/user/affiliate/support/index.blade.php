@extends('frontend.layout.master')
@section('site_title', __('Support'))

@section('style')
    <style>
        .profile-settings-area {
            background: #f9fafb;
        }
    </style>
@endsection

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('Support')" :innerTitle="__('Support')" />

        <div class="responsive-overlay"></div>
        <div class="profile-settings-area pat-100 pab-100 section-bg-2">
            <div class="container">
                <div class="row g-4">

                    <!-- Sidebar -->
                    @include('frontend.user.layout.partials.sidebar')

                    <!-- Main Content -->
                    <div class="col-xl-9 col-lg-8">
                        <div class="profile-settings-wrapper">
                            <div class="single-profile-settings">
                                <div class="single-profile-settings-header">
                                    <div class="single-profile-settings-header-flex">
                                        <x-form.form-title :title="__('Contact Support')"
                                            :class="'single-profile-settings-header-title'" />
                                    </div>
                                </div>

                                <div class="single-profile-settings-inner profile-border-top">
                                    
                                    <form id="support_form" method="POST" action="{{ route('affiliate.support.send') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Subject') }}</label>
                                            <input name="subject" id="subject" class="form-control"
                                                placeholder="Enter subject">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Message') }}</label>
                                            <textarea name="message" id="message" class="form-control" rows="6"
                                                placeholder="Enter your message"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
@endsection


@section('script')
    <script>
        (function ($) {
            "use strict";

            $(document).ready(function () {

                $(document).on('submit', '#support_form', function (e) {
                    e.preventDefault();

                    let subject = $('#subject').val().trim();
                    let message = $('#message').val().trim();

                    if (subject === '') {
                        toastr_warning_js("{{ __('Subject is required.') }}");
                        return;
                    }
                    if (message === '') {
                        toastr_warning_js("{{ __('Message is required.') }}");
                        return;
                    }

                    let $btn = $(this).find('button[type=submit]');
                    $btn.prop('disabled', true).text('{{ __("Sending...") }}');

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function (res) {
                            if (res.status === 'success') {
                                toastr_success_js("{{ __('Message has been sent successfully!') }}");
                                $('#support_form')[0].reset();
                            } else {
                                toastr_warning_js("{{ __('Something went wrong. Please try again.') }}");
                            }
                            $btn.prop('disabled', false).text('{{ __("Send") }}');
                        },
                        error: function (err) {
                            toastr_warning_js("{{ __('Failed to send message. Please check all fields and try again.') }}");
                            $btn.prop('disabled', false).text('{{ __("Send") }}');
                        }
                    });
                });

            });

        }(jQuery));

        function toastr_warning_js(msg) {
            Command: toastr["warning"](msg, "Warning !");
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "4000",
            }
        }

        function toastr_success_js(msg) {
            Command: toastr["success"](msg, "Success !");
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "4000",
            }
        }

    </script>
@endsection