<script>
    (function($){
        "use strict";
        $(document).ready(function(){
            //user identity details
            $(document).on('click','.user_identity_details',function(e){
                e.preventDefault();
                let user_id = $(this).data('user_id');
                $.ajax({
                    url:"{{ route('admin.user.identity.details') }}",
                    method:'post',
                    data:{user_id:user_id},
                    success:function(res){
                        $('#user_identity_details').html(res);
                    }
                });
            })

            //user identity verify status
            $(document).on('click','.user_verify_status',function(e){
                e.preventDefault();
                let user_id = $('.compare-profile-and-identity #user_id_for_verified_status').val();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "To change user verified status",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url:"{{ route('admin.user.identity.verify.status') }}",
                            method:'post',
                            data:{user_id:user_id},
                            success:function(res){
                                toastr_success_js("{{ __('Status successfully updated') }}")
                                $('.table_activation').load(location.href + ' .table_activation');
                            }
                        });
                        Swal.fire(
                            'Updated!',
                            'Status successfully updated.',
                            'success'
                        )
                    }
                    $('#userIdentityModal').modal('hide');
                })
            })

            //user identity decline
            $(document).on('click','.user_identity_decline',function(e){
                e.preventDefault();
                let user_id = $('.compare-profile-and-identity #user_id_for_verified_status').val();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "To decline the user identity verify request",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, decline it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url:"{{ route('admin.user.identity.verify.decline') }}",
                            method:'post',
                            data:{user_id:user_id},
                            success:function(res){
                                toastr_warning_js("{{ __('Identity verify request successfully decline') }}")
                                $('.table_activation').load(location.href + ' .table_activation');
                            }
                        });
                        Swal.fire(
                            'Updated!',
                            'Request successfully decline.',
                            'success'
                        )
                    }
                    $('#userIdentityModal').modal('hide');
                })
            })

            // send missing details notification
            $(document).on('click', '.send_missing_details_notification', function (e) {
                e.preventDefault();
                let user_id = $('.compare-profile-and-identity #user_id_for_verified_status').val();
                let additional_message = $('#additional_message').val();

                if (additional_message.trim() === '') {
                    toastr_warning_js("{{ __('Please enter a professional message for the user.') }}");
                    return false;
                }

                // Create FormData to handle file upload
                let formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('user_id', user_id);
                formData.append('additional_message', additional_message);
                
                // Add screenshot file if selected
                let screenshotFile = $('#verification_screenshot')[0].files[0];
                if (screenshotFile) {
                    formData.append('verification_screenshot', screenshotFile);
                }

                $(this).attr('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Thinking...');

                $.ajax({
                    url: "{{ route('admin.user.identity.notify.missing') }}",
                    method: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (res) => {
                        $(this).attr('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i> {{ __("Send Professional Notification") }}');
                        if (res.status === 'success') {
                            toastr_success_js(res.msg);
                            $('#userIdentityModal').modal('hide');
                            // Reset form
                            $('#additional_message').val('');
                            $('#verification_screenshot').val('');
                            $('#screenshot_preview').hide();
                        }
                    },
                    error: (err) => {
                        $(this).attr('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i> {{ __("Send Professional Notification") }}');
                        let response = err.responseJSON;
                        toastr_error_js(response.msg || "{{ __('Something went wrong') }}");
                    }
                });
            });

            // pagination
            $(document).on('click', '.pagination a', function(e){
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                countries(page);
            });
            function countries(page){
                $.ajax({
                    url:"{{ route('admin.user.identity.request.paginate.data').'?page='}}" + page,
                    success:function(res){
                        $('.search_result').html(res);
                    }
                });
            }

            // search state
            $(document).on('keyup','#string_search',function(){
                let string_search = $(this).val();
                $.ajax({
                    url:"{{ route('admin.user.identity.request.search') }}",
                    method:'GET',
                    data:{string_search:string_search},
                    success:function(res){
                        if(res.status=='nothing'){
                            $('.search_result').html('<h3 class="text-center text-danger">'+"{{ __('Nothing Found') }}"+'</h3>');
                        }else{
                            $('.search_result').html(res);
                        }
                    }
                });
            })

        });

        // Screenshot preview functionality
        $(document).on('change', '#verification_screenshot', function(e) {
            let file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    toastr_warning_js("{{ __('Please select a valid image file.') }}");
                    $(this).val('');
                    $('#screenshot_preview').hide();
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    toastr_warning_js("{{ __('Image size should not exceed 5MB.') }}");
                    $(this).val('');
                    $('#screenshot_preview').hide();
                    return;
                }
                
                // Show preview
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#screenshot_preview img').attr('src', e.target.result);
                    $('#screenshot_preview').show();
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove screenshot functionality
        $(document).on('click', '#remove_screenshot', function(e) {
            e.preventDefault();
            $('#verification_screenshot').val('');
            $('#screenshot_preview').hide();
            $('#screenshot_preview img').attr('src', '');
        });

        // Clear screenshot preview when modal is hidden
        $('#userIdentityModal').on('hidden.bs.modal', function () {
            $('#verification_screenshot').val('');
            $('#screenshot_preview').hide();
            $('#screenshot_preview img').attr('src', '');
        });

    }(jQuery));

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

    //toastr warning
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

    //toastr error
    function toastr_error_js(msg){
        Command: toastr["error"](msg, "Error !")
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
