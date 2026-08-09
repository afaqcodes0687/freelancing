<script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            let site_default_currency_symbol = '{{ site_currency_symbol() }}';

            // pagination
            $(document).on('click', '.pagination a', function(e){
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                jobs(page);
            });
            function jobs(page){
                $.ajax({
                    url:"{{ route('subscriptions.pagination').'?page='}}" + page,
                    method:'GET',
                    success:function(res){
                        if(res.status=='nothing'){
                            $('.search_subscription_result').html('<h3 class="text-center text-danger">'+"{{ __('Nothing Found') }}"+'</h3>');
                        }else{
                            $('.search_subscription_result').html(res);
                        }
                    }

                });
            }

            //filter subscription
            $(document).on('click', '.get_subscription_type_id', function(e){
                e.preventDefault();
                let type_id = $(this).data('type_id');
                $.ajax({
                    url:"{{ route('subscriptions.filter')}}",
                    data:{type_id:type_id},
                    method:'GET',
                    success:function(res){
                        if(res.status=='nothing'){
                            $('.search_subscription_result').html('<h3 class="text-center text-danger">'+"{{ __('Nothing Found') }}"+'</h3>');
                        }else{
                            $('.search_subscription_result').html(res);
                        }
                    }

                });
            });


            //choose plan
            @php
                $user_type = '';
                if(Auth::check()){
                    $user_type = Auth::user()->user_type == 1 ? 'client' : 'freelancer';
                    $user_type = route($user_type .'.'. 'wallet.history');
                }
            @endphp

            $(document).on('click', '.choose_plan', function(e){
                let subscription_id = $(this).data('id');
                let subscription_price = $(this).data('price');
                let current_plan_id = {{ $current_plan_id ?? 'null' }}; 
                let current_plan_price = {{ $current_plan_price ?? 'null' }};  

                $('#paymentGatewayModal').modal('show');

                if(subscription_price == 0){
                    $('#modal-title').text('{{ __('Free Plan') }}');
                    $('#free-plan-message').show();
                    $('#payment-section').hide();
                    $('.buy_subscription').hide();
                    $('#subscription_id').val(subscription_id);  
                } 
                else if (current_plan_id && current_plan_price > subscription_price) {
                    $('#modal-title').text('{{ __('Downgrade Plan') }}');
                    $('#free-plan-message').show();
                    $('#payment-section').hide();
                    $('.buy_subscription').hide();
                    $('#subscription_id').val(subscription_id);
                }
                else {
                    $('#modal-title').text('{{ __('Buy Package') }}');
                    $('#free-plan-message').hide();
                    $('#payment-section').show();
                    $('.buy_subscription').show();

                    let balance = {{ Auth::check() && Auth::user()->user_wallet ? Auth::user()->user_wallet->balance : 0 }};
                    let signup_bonus = {{ Auth::check() && Auth::user()->user_wallet ? Auth::user()->user_wallet->signup_bonus : 0 }};
                    let tax_rate = {{ (float) (get_static_option('subscription_tax_rate') ?? env('SUBSCRIPTION_TAX_RATE', 0)) }};
                    
                    // For wallet purchases, no tax applies - only base price
                    let total_amount = parseFloat(subscription_price);
                    let tax_amount = 0;
                    
                    $('#subscription_id').val(subscription_id);
                    $('#subscription_price').val(subscription_price);

                    // Restriction: Signup bonus only usable for $10 package
                    let usable_balance = balance;
                    if (subscription_price != 10) {
                        usable_balance -= signup_bonus;
                    }

                    if(total_amount > usable_balance){
                        let shortage = (total_amount - usable_balance).toFixed(2);
                        let msg = '<span class="text-danger">{{__('Wallet Balance Shortage:')}}'+ site_default_currency_symbol + shortage + '</span>';
                        if (signup_bonus > 0 && subscription_price != 10) {
                            msg += '<br><small class="text-muted">{{__('Note: Signup bonus is only usable for the Weekly Starter package.')}}</small>';
                        }
                        $('.display_balance').html(msg);
                        $('.deposit_link').html('<a href="{{ $user_type }}" target="_blank">{{ __('Deposit')}}</a>');
                    }
                }
            });

            $(document).on('click', '#free_plan_confirm_button', function(){
                let subscription_id = $('#subscription_id').val();

                $.ajax({
                    url: "{{ route('subscriptions.downgrade') }}",
                    method: 'POST',
                    data: {
                        subscription_id: subscription_id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response){
                        if(response.status == 'success'){
                            toastr.success("{{ __('Next plan selected successfully') }}");
                            $('#paymentGatewayModal').modal('hide');
                            location.reload();  
                        }
                    },
                    error: function(){
                        toastr.error("{{ __('Something went wrong. Please try again.') }}");
                    }
                });
            });



            // login
            $(document).on('click', '.login_to_buy_a_subscription', function(e){
                e.preventDefault();
                let username = $('#username').val();
                let password = $('#password').val();
                let subscription_price = $('#subscription_price').val();
                let erContainer = $(".error-message");
                erContainer.html('');
                $.ajax({
                    url:"{{ route('subscriptions.user.login')}}",
                    data:{username:username,password:password},
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
                            location.reload();
                            let balance = res.balance;
                            $('#loginModal').modal('hide');
                            
                            // For wallet purchases, no tax applies - only base price
                            let total_amount = parseFloat(subscription_price);
                            let tax_amount = 0;
                            
                            if(total_amount > balance){
                                $('.load_after_login').load(location.href + ' .load_after_login', function (){
                                    $('.display_balance').html('<span class="text-danger">{{__('Wallet Balance Shortage:')}}'+ site_default_currency_symbol + (total_amount-balance).toFixed(2) +'</span>');
                                    $('.deposit_link').html('<a href="{{ $user_type }}" target="_blank">{{ __('Deposit')}}</a>');
                                });
                            }
                        }
                        if(res.status == 'failed'){
                            erContainer.html('<div class="alert alert-danger">'+res.msg+'</div>');
                        }
                    }

                });
            });

            //buy subscription-load spinner
            $(document).on('click','#confirm_buy_subscription_load_spinner',function(){
                //Image validation
                let manual_payment = $('#order_from_user_wallet').val();
                if(manual_payment == 'manual_payment') {
                    let manual_payment_image = $('input[name="manual_payment_image"]').val();
                    let manual_payment_method = $('select[name="manual_payment_method"]').val();
                    let manual_account_number = $('input[name="manual_account_number"]').val();
                    let manual_transaction_id = $('input[name="manual_transaction_id"]').val();
                    if(manual_payment_image == '') {
                        toastr_warning_js("{{__('Image field is required')}}")
                        return false
                    }
                    if(!manual_payment_method) {
                        toastr_warning_js("{{__('Payment method is required')}}")
                        return false
                    }
                    if(!manual_account_number) {
                        toastr_warning_js("{{__('Account or phone number is required')}}")
                        return false
                    }
                    if(!manual_transaction_id) {
                        toastr_warning_js("{{__('Transaction/Reference ID is required')}}")
                        return false
                    }
                }

                $('#buy_subscription_load_spinner').html('<i class="fas fa-spinner fa-pulse"></i>')
                setTimeout(function () {
                    $('#buy_subscription_load_spinner').html('');
                }, 10000);
            });

        });
    }(jQuery));
</script>
