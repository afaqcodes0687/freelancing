<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            $('.country_select2').select2();
            $('.state_select2').select2();
            $('.city_select2').select2();

            // Image cropping logic
            let cropper;
            let currentInput;
            let currentPreview;
            let currentImage;
            let currentContainerSpan;
            let currentContainerP;

            function openCropper(input, previewClass, imageClass, containerSpanSelector, containerPSelector) {
                if (input.files && input.files[0]) {
                    currentInput = input;
                    currentPreview = previewClass;
                    currentImage = imageClass;
                    currentContainerSpan = containerSpanSelector;
                    currentContainerP = containerPSelector;

                    let reader = new FileReader();
                    reader.onload = function (e) {
                        $('#cropperImage').attr('src', e.target.result);
                        $('#cropperModal').modal('show');
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('#cropperModal').on('shown.bs.modal', function () {
                cropper = new Cropper(document.getElementById('cropperImage'), {
                    aspectRatio: 5 / 3,
                    viewMode: 1,
                });
            }).on('hidden.bs.modal', function () {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            });

            $('#cropButton').on('click', function () {
                let canvas = cropper.getCroppedCanvas({
                    width: 500,
                    height: 300,
                });

                canvas.toBlob(function (blob) {
                    let url = URL.createObjectURL(blob);
                    $(currentPreview).attr('src', url).show();
                    $(currentImage).hide();

                    // Hide upload icons/text
                    $(currentContainerSpan).hide();
                    $(currentContainerP).text("{{__('Click to change photo')}}");

                    // Create a new File object from the blob to replace the input value
                    let file = new File([blob], currentInput.files[0].name, { type: 'image/jpeg', lastModified: Date.now() });
                    let container = new DataTransfer();
                    container.items.add(file);
                    currentInput.files = container.files;

                    $('#cropperModal').modal('hide');
                }, 'image/jpeg');
            });

            // change country and get state
            $('.identity-verifying-list').on('click', function () {
                $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
            });
            $('.verify-radio').on('change', function () {
                let selectedValue = $(this).closest('.identity-verifying-list').find('.identity-verifying-list-contents-details-title').text().trim();
                $('#verify_by').val(selectedValue);

                let inputTitle = '';
                let inputPlaceholder = '';
                switch (selectedValue) {
                    case 'National ID Card':
                        inputTitle = '{{ __("National ID number") }}';
                        inputPlaceholder = '{{ __("Enter id number") }}';
                        @if(isset($user_identity) && $user_identity->verify_by == 'National ID Card')
                            $('#national_id_number').val("{{ $user_identity->national_id_number ?? '' }}");
                            $('.front_image').attr('src', "{{ asset('assets/uploads/verification/' . $user_identity->front_image) }}");
                            $('.back_image').attr('src', "{{ asset('assets/uploads/verification/' . $user_identity->back_image) }}");
                        @else
                            $('#national_id_number').val('');
                            $('.front_image').attr('src', '');
                            $('.back_image').attr('src', '');
                        @endif
                        break;
                    case 'Driving License':
                        inputTitle = '{{ __("Driving License number") }}';
                        inputPlaceholder = '{{ __("Enter driving license number") }}';
                        @if(isset($user_identity) && $user_identity->verify_by == 'Driving License')
                            $('#national_id_number').val("{{ $user_identity->national_id_number ?? '' }}");
                            $('.front_image').attr('src', "{{ asset('assets/uploads/verification/' . $user_identity->front_image) }}");
                            $('.back_image').attr('src', "{{ asset('assets/uploads/verification/' . $user_identity->back_image) }}");
                        @else
                            $('#national_id_number').val('');
                            $('.front_image').attr('src', '');
                            $('.back_image').attr('src', '');
                        @endif
                        break;
                    case 'Passport':
                        inputTitle = '{{ __("Passport number") }}';
                        inputPlaceholder = '{{ __("Enter passport number") }}';
                        @if(isset($user_identity) && $user_identity->verify_by == 'Passport')
                            $('#national_id_number').val("{{ $user_identity->national_id_number ?? '' }}");
                            $('.front_image').attr('src', "{{ asset('assets/uploads/verification/' . $user_identity->front_image) }}");
                            $('.back_image').attr('src', "{{ asset('assets/uploads/verification/' . $user_identity->back_image) }}");
                        @else
                            $('#national_id_number').val('');
                            $('.front_image').attr('src', '');
                            $('.back_image').attr('src', '');
                        @endif
                        break;
                }

                $('label[for="national_id_number"]').text(inputTitle);
                $('#national_id_number').attr('placeholder', inputPlaceholder);
            });


            let selectedValue = $('.verify-radio:checked').closest('.identity-verifying-list').find('.identity-verifying-list-contents-details-title').text();
            let inputTitle = '';
            let inputPlaceholder = '';
            switch (selectedValue) {
                case 'National ID Card':
                    inputTitle = '{{ __("National ID number") }}';
                    inputPlaceholder = '{{ __("Enter id number") }}';
                    break;
                case 'Driving License':
                    inputTitle = '{{ __("Driving License number") }}';
                    inputPlaceholder = '{{ __("Enter driving license number") }}';
                    break;
                case 'Passport':
                    inputTitle = '{{ __("Passport number") }}';
                    inputPlaceholder = '{{ __("Enter passport number") }}';
                    break;
            }
            $('label[for="national_id_number"]').text(inputTitle);
            $('#national_id_number').attr('placeholder', inputPlaceholder);


            // Country to State
            $('#country').on('change', function () {
                let country = $(this).val();

                $("#state").html('<option value="">{{ __("Select State") }}</option>');
                $("#city").html('<option value="">{{ __("Select City") }}</option>');

                if (!country) return;

                $.ajax({
                    method: 'post',
                    url: "{{ route('au.state.all') }}",
                    data: { country: country },
                    success: function (res) {
                        if (res.status === 'success') {
                            let options = "<option value=''>{{ __('Select State') }}</option>";
                            $.each(res.states, function (index, value) {
                                options += `<option value="${value.id}">${value.state}</option>`;
                            });

                            $("#state").html(options).trigger('change.select2');
                        }
                    }
                });
            });

            // Load cities when state is selected
            $('#state').on('change', function () {
                let state = $(this).val();

                $("#city").html('<option value="">{{ __("Select City") }}</option>');

                if (!state) return;

                $.ajax({
                    method: 'post',
                    url: "{{ route('au.city.all') }}",
                    data: { state: state },
                    success: function (res) {
                        let options = "<option value=''>{{ __('Select City') }}</option>";
                        if (res.status === 'success') {
                            res.cities.forEach(city => {
                                options += `<option value="${city.id}">${city.city}</option>`;
                            });
                        }

                        $("#city").html(options).trigger('change.select2');
                    }
                });
            });

            // Load initial state and city based on stored values
            let storedCountry = "{{ $user_identity->country_id ?? $user->country_id ?? '' }}";
            let storedState = "{{ $user_identity->state_id ?? $user->state_id ?? '' }}";
            let storedCity = "{{ $user_identity->city_id ?? $user->city_id ?? '' }}";

            function loadInitialStateAndCity(country, state, city) {
                if (!country) return;

                // Set the country value first
                $('#country').val(country).trigger('change.select2');

                $.ajax({
                    method: 'post',
                    url: "{{ route('au.state.all') }}",
                    data: { country },
                    success: function (res) {
                        if (res.status === 'success') {
                            let stateOptions = "<option value=''>{{ __('Select State') }}</option>";
                            $.each(res.states, function (index, value) {
                                stateOptions += `<option value="${value.id}" ${value.id == state ? 'selected' : ''}>${value.state}</option>`;
                            });

                            $("#state").html(stateOptions).trigger('change.select2');

                            if (state) {
                                $.ajax({
                                    method: 'post',
                                    url: "{{ route('au.city.all') }}",
                                    data: { state },
                                    success: function (res) {
                                        let cityOptions = "<option value=''>{{ __('Select City') }}</option>";
                                        if (res.status === 'success') {
                                            res.cities.forEach(cityItem => {
                                                cityOptions += `<option value="${cityItem.id}" ${cityItem.id == city ? 'selected' : ''}>${cityItem.city}</option>`;
                                            });
                                        }

                                        $("#city").html(cityOptions).trigger('change.select2');
                                    }
                                });
                            }
                        }
                    }
                });
            }

            // Load initial state and city on page load
            if (storedCountry) {
                loadInitialStateAndCity(storedCountry, storedState, storedCity);
            } else {
                // If no stored country, set default values for other fields
                $('#country').val('').trigger('change.select2');
                $('#state').val('').trigger('change.select2');
                $('#city').val('').trigger('change.select2');
            }

            // Initialize form values on page load
            function initializeFormValues() {
                let selectedValue = $('.verify-radio:checked').closest('.identity-verifying-list').find('.identity-verifying-list-contents-details-title').text().trim();
                if (selectedValue) {
                    $('#verify_by').val(selectedValue);

                    let inputTitle = '';
                    let inputPlaceholder = '';
                    switch (selectedValue) {
                        case 'National ID Card':
                            inputTitle = '{{ __("National ID number") }}';
                            inputPlaceholder = '{{ __("Enter id number") }}';
                            break;
                        case 'Driving License':
                            inputTitle = '{{ __("Driving License number") }}';
                            inputPlaceholder = '{{ __("Enter driving license number") }}';
                            break;
                        case 'Passport':
                            inputTitle = '{{ __("Passport number") }}';
                            inputPlaceholder = '{{ __("Enter passport number") }}';
                            break;
                    }

                    $('label[for="national_id_number"]').text(inputTitle);
                    $('#national_id_number').attr('placeholder', inputPlaceholder);
                }
            }

            // Call initialization function
            initializeFormValues();

            //front image preview
            $('.front_image_preview').hide();
            document.querySelector('#front_image').addEventListener('change', function () {
                openCropper(this, '.front_image_preview', '.front_image', ".identity_verify_front span:first", ".identity_verify_front p:first");
            });

            //back image preview
            $('.back_image_preview').hide();
            document.querySelector('#back_image').addEventListener('change', function () {
                openCropper(this, '.back_image_preview', '.back_image', ".identity_verify_back span:last", ".identity_verify_back p:last");
            });

            //identity verification request
            $(document).on('submit', '#submit_client_verify_info', function (e) {
                e.preventDefault();
                let country = $('#country').val();
                let state = $('#state').val();
                let city = $('#city').val();
                let address = $('#address').val();
                let zipcode = $('#zipcode').val();
                let national_id_number = $('#national_id_number').val();
                let front_image = $('#front_image').val();
                let back_image = $('#back_image').val();
                if (country == '' || address == '' || national_id_number == '' || front_image == '' || back_image == '') {
                    toastr_warning_js("{{ __('Except city all fields required !') }}");
                    return false;
                } else {
                    $('.verification_load_spinner').html('<i class="fas fa-spinner fa-pulse"></i>')
                    $.ajax({
                        url: "{{ route('client.identity.verification') }}",
                        method: 'post',
                        data: new FormData(this),
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (res) {
                            $('.error_msg_container').html('');
                            if (res.status == 'success') {
                                $('#display_client_identity_verification').load(location.href + " #display_client_identity_verification");
                                $('.front_image_preview').html('');
                                $('.front_image').hide();
                                $('.back_image_preview').hide();
                                $(".identity-verifying-upload").find('span').first().hide()
                                $(".identity-verifying-upload").find('p').first().text("{{__('Click to change photo')}}")
                                toastr_success_js("{{ __('Documents successfully submitted') }}");
                            }
                        },
                        error: function (err) {
                            let error = err.responseJSON;
                            $('.error_msg_container').html('');
                            $.each(error.errors, function (index, value) {
                                $('.error_msg_container').append('<p class="text-danger">' + value + '<p>');
                            });
                        }
                    })
                }
            });

        });
    }(jQuery));

    //toastr warning
    function toastr_warning_js(msg) {
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
    function toastr_success_js(msg) {
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
</script>