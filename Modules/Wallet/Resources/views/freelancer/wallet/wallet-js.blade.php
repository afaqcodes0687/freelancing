<script>
    (function($){
        "use strict";
        $(document).ready(function(){
            //update profile
            $(document).on('click','.deposit_amount_to_wallet',function(e){
                let amount  = parseInt($('#amount').val());
                let max_amount = parseInt("{{ get_static_option('deposit_amount_limitation_for_user') ?? '3000' }}");
                if(amount == '' || isNaN(amount) || amount <= 0){
                    toastr_warning_js("{{ __('Please enter your deposit amount.') }}");
                    return false;
                }
                if(amount  > max_amount){
                    toastr_warning_js("{{ __('Deposit amount must not greater than the max limit.') }}");
                    return false;
                }
            })

            // auto-select PayPro gateway on wallet deposit modal open (freelancer)
            $('#paymentGatewayModal').on('shown.bs.modal', function () {
                const $modal = $(this);
                const $payproLi = $modal.find('.payment_getway_image ul li[data-gateway="paypro"]');
                if ($payproLi.length) {
                    $payproLi.trigger('click');
                }
            });

            // pagination
            $(document).on('click', '.pagination a', function(e){
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                histories(page);
            });
            function histories(page){
                $.ajax({
                    url:"{{ route('freelancer.wallet.paginate.data').'?page='}}" + page,
                    success:function(res){
                        $('.search_result').html(res);
                    }
                });
            }

            // search history
            $(document).on('keyup','#string_search',function(){
                let string_search = $(this).val();
                $.ajax({
                    url:"{{ route('freelancer.wallet.search') }}",
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

            // get fields
            $(document).on("change", ".gateway-name", function() {
                let gatewayInformation = "";
                $(".gateway-information-wrapper").fadeOut(150);

                // Clear previous validation
                $('#withdraw_request_amount').removeClass('is-invalid');
                $('.gateway-field-input').removeClass('is-invalid');

                JSON.parse($(this).find(":selected").attr("data-fields")).forEach(function(value, index) {
                    let gateway_name = value.toLowerCase().replaceAll(" ", "_").replaceAll("-", "_");

                    gatewayInformation += `
                        <div class="single-input">
                            ${ value }
                            <input type="text" name="gateway_field[${ gateway_name }]" class="form-control gateway-field-input" placeholder="Write ${ value.toLowerCase() }" />
                        </div>
                    `;
                })

                $(".gateway-information-wrapper").html(gatewayInformation);
                $(".gateway-information-wrapper").fadeIn(250);

                // Run validation on gateway fields after they are created
                validateGatewayFields();
            })

            //fee and amount container
            $(document).on('keyup','#withdraw_request_amount',function(){
                let site_default_currency_symbol = '{{ site_currency_symbol() }}';
                let available_balance = parseFloat("{{ $total_wallet_balance ?? 0 }}");
                $('.fee_and_receive_amount_container').removeClass('d-none');

                let withdraw_fee = 0;
                let withdraw_fee_amount = 0;
                let receiveable_amount = 0;

                let amount = parseFloat($(this).val())
                let withdraw_fee_type = "{{ get_static_option('withdraw_fee_type') }}"
                withdraw_fee = "{{ round(get_static_option('withdraw_fee'),2) }}"

                // Validation: Check if amount exceeds available balance
                if(amount > available_balance) {
                    console.log('Amount exceeds balance:', amount, 'Available:', available_balance);
                    $('#withdraw_request_amount').addClass('is-invalid');
                    $('#withdraw_amount_error').text("{{ __('Withdrawal amount cannot exceed your available balance of') }} " + site_default_currency_symbol + available_balance.toFixed(2)).show();
                    $('.fee_and_receive_amount_container').addClass('d-none');
                    $('.withdraw_fee_amount_for_each_transaction').text('');
                    $('.receiveable_amount').text('');
                    return false;
                }

                // Validation: Check if amount is less than minimum amount
                let min_amount = parseFloat("{{ get_static_option('minimum_withdraw_amount') ?? 0 }}");
                if(amount < min_amount) {
                    console.log('Amount less than minimum:', amount, 'Minimum:', min_amount);
                    $('#withdraw_request_amount').addClass('is-invalid');
                    $('#withdraw_amount_error').text("{{ __('Minimum withdrawal amount is') }} " + site_default_currency_symbol + min_amount.toFixed(2)).show();
                    $('.fee_and_receive_amount_container').addClass('d-none');
                    $('.withdraw_fee_amount_for_each_transaction').text('');
                    $('.receiveable_amount').text('');
                    return false;
                }

                // Validation: Check if amount is zero or negative
                if(amount <= 0 || isNaN(amount)) {
                    console.log('Amount invalid:', amount);
                    $('#withdraw_request_amount').addClass('is-invalid');
                    $('#withdraw_amount_error').text("{{ __('Please enter a valid amount greater than 0') }}").show();
                    $('.fee_and_receive_amount_container').addClass('d-none');
                    $('.withdraw_fee_amount_for_each_transaction').text('');
                    $('.receiveable_amount').text('');
                    return false;
                }

                // Clear amount field validation if valid
                $('#withdraw_request_amount').removeClass('is-invalid');

                console.log(amount,withdraw_fee_type,withdraw_fee)

                withdraw_fee_amount = withdraw_fee_type == 'percentage' ? (amount*withdraw_fee/100).toFixed(2) : withdraw_fee;
                receiveable_amount = parseFloat(amount - withdraw_fee_amount);

                $('.withdraw_fee_amount_for_each_transaction').text(site_default_currency_symbol + withdraw_fee_amount)
                $('.receiveable_amount').text(site_default_currency_symbol + receiveable_amount.toFixed(2))
            })

            // Withdrawal form submission validation
            $(document).on('click', '.withdraw_amount_from_wallet', function(e) {
                let amount = parseFloat($('#withdraw_request_amount').val());
                let available_balance = parseFloat("{{ $total_wallet_balance ?? 0 }}");
                let min_amount = parseFloat("{{ get_static_option('minimum_withdraw_amount') ?? 0 }}");
                let max_amount = parseFloat("{{ get_static_option('maximum_withdraw_amount') ?? 999999 }}");

                // Check if amount is empty or invalid
                if(isNaN(amount) || amount <= 0) {
                    $('#withdraw_request_amount').addClass('is-invalid');
                    toastr_warning_js("{{ __('Please enter a valid withdrawal amount.') }}");
                    e.preventDefault();
                    return false;
                }

                // Check if amount exceeds available balance
                if(amount > available_balance) {
                    $('#withdraw_request_amount').addClass('is-invalid');
                    toastr_warning_js("{{ __('Withdrawal amount cannot exceed your available balance of') }} {{ site_currency_symbol() }}" + available_balance.toFixed(2));
                    e.preventDefault();
                    return false;
                }

                // Check minimum amount
                if(amount < min_amount) {
                    $('#withdraw_request_amount').addClass('is-invalid');
                    toastr_warning_js("{{ __('Minimum withdrawal amount is') }} {{ site_currency_symbol() }}" + min_amount.toFixed(2));
                    e.preventDefault();
                    return false;
                }

                // Check maximum amount
                if(amount > max_amount) {
                    $('#withdraw_request_amount').addClass('is-invalid');
                    toastr_warning_js("{{ __('Maximum withdrawal amount is') }} {{ site_currency_symbol() }}" + max_amount.toFixed(2));
                    e.preventDefault();
                    return false;
                }

                // Check if gateway is selected
                if($('.gateway-name').val() === '') {
                    toastr_warning_js("{{ __('Please select a withdrawal method.') }}");
                    e.preventDefault();
                    return false;
                }

                // Check if all gateway fields are filled
                if(!validateGatewayFields()) {
                    toastr_warning_js("{{ __('Please fill in all required gateway fields.') }}");
                    e.preventDefault();
                    return false;
                }
            });

            // Validate gateway fields
            function validateGatewayFields() {
                let isValid = true;
                $('.gateway-field-input').each(function() {
                    if ($(this).val() === '') {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                return isValid;
            }
        });
    }(jQuery));
</script>
