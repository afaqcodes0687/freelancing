<script>
    (function($){
        "use strict";
        $(document).ready(function(){
            $('.country_select2').select2({
                dropdownParent: $('.popup-fixed')
            });
            $('.state_select2').select2({
                dropdownParent: $('.popup-fixed')
            });
            $('.city_select2').select2({
                dropdownParent: $('.popup-fixed')
            });


            // profile photo change
            // document.querySelector('#profile_photo').addEventListener('change', function() {
            //     $("#profilePhotoModal").modal('show');
            //     if (this.files && this.files[0]) {
            //         let img = document.querySelector('.profile_photo_preview');
            //         img.onload = () => {
            //             URL.revokeObjectURL(img.src);  // no longer needed, free memory
            //         }
            //
            //         img.src = URL.createObjectURL(this.files[0]); // set src to blob url
            //         document.querySelector(".profile_photo_upload").files = this.files;
            //         document.querySelector(".profile_photo_upload").value = this.value;
            //     }
            // });

            //change profile photo
            $(document).on('submit','#profile_photo_change',function(e){
                e.preventDefault();
                $.ajax({
                    url:"{{ route('freelancer.profile.photo.edit') }}",
                    method:'post',
                    data: new FormData(this),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success:function(){
                        $('#profilePhotoModal').modal('hide');
                        toastr_success_js("{{ __('Profile Photo Successfully Changed') }}");
                        // Refresh the page after successful upload
                        window.location.reload();
                    },
                    error: function (err) {
                        let error = err.responseJSON;
                        $('.error_msg_container').html('');
                        $.each(error.errors, function (index, value) {
                            $('.error_msg_container').append('<p class="text-danger">'+value+'<p>');
                        });
                    }
                })
            });
            // Inline Select2 initialization (without dropdownParent limitation)
            $('#inline_country_id').select2();
            $('#inline_state_id').select2();
            $('#inline_city_id').select2();

            // change country and get state (MODAL)
            $(document).on('change', '#country_id', function() {
                let country = $(this).val();
                let $state = $(".get_country_state");
                let $city = $(".get_state_city");
                $state.prop('disabled', true).html("<option>Loading...</option>").trigger('change');
                $city.prop('disabled', true).html("<option value=''>{{__('Select City')}}</option>").trigger('change');
                $.ajax({
                    method: 'post',
                    url: "{{ route('au.state.all') }}",
                    data: { country: country },
                    success: function(res) {
                        if (res.status == 'success') {
                            let all_options = "<option value=''>{{__('Select State')}}</option>";
                            $.each(res.states, function(index, value) {
                                all_options += "<option value='" + value.id + "'>" + value.state + "</option>";
                            });
                            $state.html(all_options).prop('disabled', false).trigger('change');
                        }
                    },
                    error: function(){
                        $state.html("<option value=''>{{__('Select State')}}</option>").prop('disabled', false).trigger('change');
                    }
                })
            })

            // change state and get city (MODAL)
            $(document).on('change', '.get_country_state', function() {
                let state = $(this).val();
                let $city = $(".get_state_city");
                $city.prop('disabled', true).html("<option>Loading...</option>").trigger('change');
                $.ajax({
                    method: 'post',
                    url: "{{ route('au.city.all') }}",
                    data: { state: state },
                    success: function(res) {
                        if (res.status == 'success') {
                            let all_options = "<option value=''>{{__('Select City')}}</option>";
                            $.each(res.cities, function(index, value) {
                                all_options += "<option value='" + value.id + "'>" + value.city + "</option>";
                            });
                            $city.html(all_options).prop('disabled', false).trigger('change');
                        }
                    },
                    error: function(){
                        $city.html("<option value=''>{{__('Select City')}}</option>").prop('disabled', false).trigger('change');
                    }
                })
            })

            // ------------------------------------------------------------------------------------------------
            // NEW: INLINE FORM LOGIC (Dynamic Dropdowns) - FREELANCER
            // ------------------------------------------------------------------------------------------------

            // change country and get state (INLINE)
            $(document).on('change', '#inline_country_id', function() {
                let country = $(this).val();
                let $state = $(".get_country_state_inline");
                let $city = $(".get_state_city_inline");
                $state.prop('disabled', true).html("<option>Loading...</option>").trigger('change');
                $city.prop('disabled', true).html("<option value=''>{{__('Select City')}}</option>").trigger('change');
                $.ajax({
                    method: 'post',
                    url: "{{ route('au.state.all') }}",
                    data: { country: country },
                    success: function(res) {
                        if (res.status == 'success') {
                            let all_options = "<option value=''>{{__('Select State')}}</option>";
                            $.each(res.states, function(index, value) {
                                all_options += "<option value='" + value.id + "'>" + value.state + "</option>";
                            });
                            $state.html(all_options).prop('disabled', false).trigger('change');
                        }
                    },
                    error: function(){
                        $state.html("<option value=''>{{__('Select State')}}</option>").prop('disabled', false).trigger('change');
                    }
                })
            })

            // change state and get city (INLINE)
            $(document).on('change', '.get_country_state_inline', function() {
                let state = $(this).val();
                let $city = $(".get_state_city_inline");
                $city.prop('disabled', true).html("<option>Loading...</option>").trigger('change');
                $.ajax({
                    method: 'post',
                    url: "{{ route('au.city.all') }}",
                    data: { state: state },
                    success: function(res) {
                        if (res.status == 'success') {
                            let all_options = "<option value=''>{{__('Select City')}}</option>";
                            $.each(res.cities, function(index, value) {
                                all_options += "<option value='" + value.id + "'>" + value.city + "</option>";
                            });
                            $city.html(all_options).prop('disabled', false).trigger('change');
                        }
                    },
                    error: function(){
                        $city.html("<option value=''>{{__('Select City')}}</option>").prop('disabled', false).trigger('change');
                    }
                })
            })

            // Pre-populate state and city dropdowns
            $(document).ready(function() {
                let storedCountry = "{{ $user->country_id ?? '' }}";
                let storedState = "{{ $user->state_id ?? '' }}";
                let storedCity = "{{ $user->city_id ?? '' }}";

                // Pre-populate Inline Dropdowns
                function prePopulateInline(country, state, city) {
                    if (country) {
                        $('#inline_country_id').val(country).trigger('change');

                        let $state = $(".get_country_state_inline");
                        let $city = $(".get_state_city_inline");

                        // LOAD STATES
                        $.ajax({
                            method: 'post',
                            url: "{{ route('au.state.all') }}",
                            data: { country: country },
                            success: function(res) {
                                if (res.status == 'success') {
                                    let all_options = "<option value=''>{{__('Select State')}}</option>";
                                    $.each(res.states, function(index, value) {
                                        all_options += "<option value='" + value.id + "' " + (value.id == state ? 'selected' : '') + ">" + value.state + "</option>";
                                    });
                                    $state.html(all_options).prop('disabled', false).trigger('change');

                                    // IF STATE EXISTS, LOAD CITIES
                                    if(state){
                                        $.ajax({
                                            method: 'post',
                                            url: "{{ route('au.city.all') }}",
                                            data: { state: state },
                                            success: function(res) {
                                                if (res.status == 'success') {
                                                    let all_options = "<option value=''>{{__('Select City')}}</option>";
                                                    $.each(res.cities, function(index, value) {
                                                        all_options += "<option value='" + value.id + "' " + (value.id == city ? 'selected' : '') + ">" + value.city + "</option>";
                                                    });
                                                    $city.html(all_options).prop('disabled', false).trigger('change');
                                                }
                                            }
                                        });
                                    }
                                }
                            }
                        });
                    }
                }
                prePopulateInline(storedCountry, storedState, storedCity);

                // Set the selected country, state, and city (MODAL)
                $('#country_id').val(storedCountry).trigger('change');
                function setStateAndCity(country, state, city) {
                    if (country) {
                        let $state = $(".get_country_state");
                        let $city = $(".get_state_city");
                        $state.prop('disabled', true).html("<option>Loading...</option>").trigger('change');
                        $city.prop('disabled', true).html("<option value=''>{{__('Select City')}}</option>").trigger('change');
                        $.ajax({
                            method: 'post',
                            url: "{{ route('au.state.all') }}",
                            data: { country: country },
                            success: function(res) {
                                if (res.status == 'success') {
                                    let all_options = "<option value=''>{{__('Select State')}}</option>";
                                    $.each(res.states, function(index, value) {
                                        all_options += "<option value='" + value.id + "' " + (value.id == state ? 'selected' : '') + ">" + value.state + "</option>";
                                    });
                                    $state.html(all_options).prop('disabled', false).trigger('change');

                                    // Pre-populate city dropdown
                                    $city.prop('disabled', true).html("<option>Loading...</option>").trigger('change');
                                    $.ajax({
                                        method: 'post',
                                        url: "{{ route('au.city.all') }}",
                                        data: { state: state },
                                        success: function(res) {
                                            if (res.status == 'success') {
                                                let all_options = "<option value=''>{{__('Select City')}}</option>";
                                                $.each(res.cities, function(index, value) {
                                                    all_options += "<option value='" + value.id + "' " + (value.id == city ? 'selected' : '') + ">" + value.city + "</option>";
                                                });
                                                $city.html(all_options).prop('disabled', false).trigger('change');
                                            }
                                        },
                                        error: function(){
                                            $city.html("<option value=''>{{__('Select City')}}</option>").prop('disabled', false).trigger('change');
                                        }
                                    });
                                }
                            },
                            error: function(){
                                $state.html("<option value=''>{{__('Select State')}}</option>").prop('disabled', false).trigger('change');
                            }
                        });
                    }
                }
                setStateAndCity(storedCountry, storedState, storedCity);
            })

            // update profile (MODAL SUBMIT)
            $(document).on('submit', '#edit_profile_form', function (e) {
                submitProfileForm(e, $(this), '#first_name', '#last_name', '#username', '#email', '#country_id', '#state_id', '#city_id', '#level');
            });
            
            // update profile (INLINE SUBMIT)
            $(document).on('submit', '#inline_profile_form', function(e){
                submitProfileForm(e, $(this), '#inline_first_name', '#inline_last_name', '#inline_username', '#inline_email', '#inline_country_id', '#inline_state_id', '#inline_city_id', '#inline_level');
            });

            function submitProfileForm(e, $form, first_name_sel, last_name_sel, username_sel, email_sel, country_sel, state_sel, city_sel, level_sel) {
                e.preventDefault();

                let first_name = $(first_name_sel).val()?.trim();
                let last_name = $(last_name_sel).val()?.trim();
                let username = $(username_sel).val()?.trim();
                let email = $(email_sel).val()?.trim();
                let country = $(country_sel).val();
                let state = $(state_sel).val();
                let city = $(city_sel).val();
                let level = $(level_sel).val();
                let hasProfilePhoto = "{{ !empty(Auth::guard('web')->user()->image) }}";

                // helper to show first error only
                function showError($field, message) {
                    toastr_warning_js(message);
                    $(first_name_sel).focus(); 
                }

                // 1️⃣ Profile Photo check
                if (!hasProfilePhoto || hasProfilePhoto === "false") {
                    toastr_warning_js("{{ __('Please upload a profile photo first before updating personal information') }}");
                    return false;
                }

                // 2️⃣ Validation
                if (!first_name) {
                    toastr_warning_js("{{ __('First name is required.') }}");
                    $(first_name_sel).focus(); return false;
                }
                if (!last_name) {
                    toastr_warning_js("{{ __('Last name is required.') }}");
                    $(last_name_sel).focus(); return false;
                }
                if (!username) {
                    toastr_warning_js("{{ __('Username is required.') }}");
                    $(username_sel).focus(); return false;
                }
                if (!email) {
                    toastr_warning_js("{{ __('Email is required.') }}");
                     $(email_sel).focus(); return false;
                }
                if (!country) {
                    toastr_warning_js("{{ __('Please select a country.') }}");
                    $(country_sel).focus(); return false;
                }
                if (!state) {
                    toastr_warning_js("{{ __('Please select a state.') }}");
                    $(state_sel).focus(); return false;
                }
                if (!city) {
                    toastr_warning_js("{{ __('Please select a city.') }}");
                    $(city_sel).focus(); return false;
                }
                if (!level) {
                   toastr_warning_js("{{ __('Please select your experience level.') }}");
                   $(level_sel).focus(); return false;
                }

                // ✅ All checks passed — submit via AJAX
                $.ajax({
                    url: "{{ route('freelancer.profile.edit') }}",
                    type: 'post',
                    data: {
                        first_name,
                        last_name,
                        username,
                        email,
                        country,
                        state,
                        city,
                        level,
                    },
                    success: function (res) {
                        if (res.status === 'ok') {
                            toastr_success_js("{{ __('Profile Info Successfully Updated') }}");
                            window.location.href = "{{ route('freelancer.account.setup') }}";
                        }
                    },
                    error: function (err) {
                        let error = err.responseJSON;
                        $('.error_msg_container').html('');
                        $.each(error.errors, function (index, value) {
                            $('.error_msg_container').append('<p class="text-danger">' + value + '</p>');
                        });
                    },
                });
            }
            
            //open feedback modal
            $(document).on('click','.open_freelancer_feedback_modal',function(){
                $('#reviewForm input[name="title"]').val($(this).data('feedback-title'));
                $('#reviewForm textarea[name="description"]').val($(this).data('feedback-description'));
                $('#reviewForm input[name="rating"]').val($(this).data('feedback-rating'));
            });

            //submit review
            $(document).on('click', '.submit_your_review', function(e){
                e.preventDefault();
                let title = $('#reviewForm input[name="title"]').val();
                let description = $('#reviewForm textarea[name="description"]').val();
                let rating = $('#reviewForm input[name="rating"]').val();
                let erContainer = $(".error-message");
                erContainer.html('');
                $.ajax({
                    url:"{{ route('freelancer.submit.feedback')}}",
                    data:{title:title,description:description,rating:rating},
                    method:'POST',
                    error:function(res){
                        let errors = res.responseJSON;
                        erContainer.html('<div class="alert alert-danger"></div>');
                        $.each(errors.errors, function(index,value){
                            erContainer.find('.alert.alert-danger').append('<p>'+value+'</p>');
                        });
                    },
                    success: function(res){
                        if(res.status=='success'){
                            toastr_success_js("{{ __('Thanks to Feedback Us.') }}")
                            $('#reviewForm')[0].reset();
                            $("#feedbackModal").modal('hide');
                            location.reload();
                        }
                        if(res.status == 'failed'){
                            erContainer.html('<div class="alert alert-danger">'+res.msg+'</div>');
                        }
                    }

                });
            });

        });
    }(jQuery));

    // todo toastr warning
    function toastr_warning_js(msg){
        Command: toastr["warning"](msg, "Warning !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }
    //toastr success
    function toastr_success_js(msg){
        Command: toastr["success"](msg, "Success !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }
    //toastr delete
    function toastr_delete_js(msg){
        Command: toastr["error"](msg, "Delete !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }

    $(document).ready(function(){
        let cropper;

        $('#profile_photo').on('change', function(e) {
            const files = e.target.files;
            const done = function(url) {
                $('.profile_photo_preview').attr('src', url);
                $('#profilePhotoModal').modal('show');
            };

            let reader;
            let file;
            let url;

            if (files && files.length > 0) {
                file = files[0];
                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function(e) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        $('#profilePhotoModal').on('shown.bs.modal', function() {
            const image = document.querySelector('#previewProfilePhoto');
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 3,
                cropBoxResizable: false,
                scalable: false,
                zoomable: false,
            });
        }).on('hidden.bs.modal', function() {
            if(cropper){
                cropper.destroy();
                cropper = null;
            }
        });

        $('.resize-done').on('click', function(e){
            e.preventDefault()
            const canvas = cropper.getCroppedCanvas({
                width: 500,
                height: 500,
            });


            $('.resize-done').attr('disabled', 'disabled')

            canvas.toBlob(function(blob) {
                const url = URL.createObjectURL(blob);
                const reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function() {
                    const base64data = reader.result;
                    // $('.cropped_image').val(base64data);
                    $('.profile_photo_preview').attr('src', base64data);

                    const file = new File([blob], "cropped_image.png", {type: blob.type});
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    $('.cropped_image').prop('files', dataTransfer.files);

                    // $('#profilePhotoModal').modal('hide');
                    $('#profile_photo_change').submit()
                    if(cropper){
                        cropper.destroy();
                        cropper = null;
                    }
                };
            });
        });
    });

</script>
