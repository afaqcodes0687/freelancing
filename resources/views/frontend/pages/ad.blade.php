@extends('frontend.layout.master')
@section('site_title',__('Advertisement'))
@section('meta_title'){{ __('Advertisement') }}@endsection
@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title=" __('Advertisement Create')" :innerTitle=" __('Advertisement Create') ?? '' "/>
        <div class="preview-area section-bg-2 pat-100 pab-100">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="categoryWrap-wrapper-item">
                                    <form id="adCreateForm" method="post" action="{{route('ad.store')}}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="selected_payment_gateway" id="selected_payment_gateway" value="{{ (Auth::check() && Auth::user()->user_wallet && Auth::user()->user_wallet->remaining_balance > 0) ? 'wallet' : 'paypro' }}">
                                        <input type="hidden" name="ppq" id="ppq_input" value="0.02">
                                        
                                        <!-- Error Display -->
                                        @if ($errors->any())
                                            <div class="alert alert-danger mt-3">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        
                                        @if(session('success'))
                                            <div class="alert alert-success mt-3">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        @if(session('error'))
                                            <div class="alert alert-danger mt-3">
                                                {{ session('error') }}
                                            </div>
                                        @endif
                                        
                                        <div class="form-group mb-3">
                                            <label for="companyName">Company Name</label>
                                            <input value="{{old('company') ? old('company') : ''}}" name="company" type="text" class="form-control" id="companyName" placeholder="Enter your email address">
                                            @error('company')
                                            <span class="form-text text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="title">Ad Title</label>
                                            <input value="{{old('title') ? old('title') : ''}}" name="title" type="text" class="form-control" id="title" placeholder="Enter your ad title">
                                            @error('title')
                                            <span class="form-text text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="adUrl">Ad Url</label>
                                            <input value="{{old('url') ? old('url') : ''}}" name="url" type="text" class="form-control" id="adUrl" placeholder="Enter ad url">
                                            @error('url')
                                            <span class="form-text text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="description">Ad Description</label>
                                            <input value="{{old('description') ? old('description') : ''}}" name="description" type="text" class="form-control" id="description" placeholder="Enter your ad description here">
                                            @error('description')
                                            <span class="form-text text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="optimizeFor">Optimize For</label>
                                            <select name="optimize_for" id="optimizeFor" class="form-control">
    <option value="click" data-price="0.02">Click</option>
    <option value="impression" data-price="0.02">Impression</option>
</select>

                                            <span class="form-text">
                                                <b id="previewPricePerQuantity">$0.05</b>
                                            </span>
                                            @error('optimize_for')
                                            <span class="form-text text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="quantity">Quantity</label>
                                           <select name="quantity" id="quantity" class="form-control">
    <option value="50">50</option>
    <option value="100">100</option>
    <option value="150">150</option>
    <option value="200">200</option>
    <option value="500">500</option>
</select>

                                            <div class="d-flex flex-row-reverse">
                                                <h4>
                                                    <b>
                                                        Total:
                                                        <span id="total">$50</span>
                                                    </b>
                                                </h4>
                                            </div>
                                            @error('quantity')
                                            <span class="form-text text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="formFile" class="form-label">Attachment</label>
                                            <input name="cover_image" class="form-control" type="file" id="formFile">
                                            @error('cover_image')
                                            <span class="form-text text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="alert alert-info mt-4">
                                            <h5>{{ __('Important Notes') }}</h5>
                                            <ul class="mb-0">
                                                <li>{{ __('Only advertisements related to freelancing services are allowed on this platform.') }}</li>
                                                <li>{{ __('Illegal, irrelevant, misleading, or unauthorized advertisements are strictly prohibited.') }}</li>
                                                <li>{{ __('If an ad violates our policies, the payment will not be refunded under any circumstances.') }}</li>
                                                <li>{{ __('This is a freelancing platform; therefore, all advertisements must be directly related to freelancing services.') }}</li>
                                                <li>{{ __('Even after successful payment, your advertisement will remain inactive until approved by the admin.') }}</li>
                                                <li>{{ __('You will be notified within 24 hours regarding the approval or rejection of your advertisement.') }}</li>
                                            </ul>
                                        </div>


                                         <button type="button" id="payNowBtn" class="btn-profile btn-bg-1 mt-3" style="border: none;">
                                            {{ __('Pay Now') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="categoryWrap-wrapper-item flex-column">
                                    <h4 id="previewTitle">Advertising</h4>
                                    <img id="previewUploadedImage" class="w-100" src="{{asset('assets/frontend/img/ad.png')}}" alt="">
                                    <div><b id="previewCompanyName">Company Name</b></div>
                                    <div id="previewDescription">Description</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Ad Payment Modal (wallet + PayPro) -->
    <div class="modal fade" id="adPaymentModal" tabindex="-1" aria-labelledby="adPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adPaymentModalLabel">{{ __('Choose Payment Method') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        {{ __('Total Amount:') }}
                        <strong id="modal_total_amount"></strong>
                    </p>
                    <div class="payment-gateway-wrapper payment_getway_image" style="width:480px; max-width: 100%;">
                        <ul id="payment_gateway_list">
                            @if(Auth::check() && Auth::user()->user_wallet && Auth::user()->user_wallet->remaining_balance > 0)
                                <li data-gateway="wallet" class="selected active">
                                    <div class="img-select">
                                        <img src="{{ asset('assets/frontend/img/walet.png') }}" alt="Wallet" />
                                    </div>
                                </li>
                            @endif
                            @foreach(\App\Helper\PaymentGatewayList::listOfPaymentGateways() as $gateway)
                                @if(!empty(get_static_option($gateway.'_gateway')))
                                    <li data-gateway="{{ $gateway }}" class="{{ ($gateway == get_static_option('site_default_payment_gateway')) ? 'selected active' : '' }}">
                                        <div class="img-select">
                                            {!! render_image_markup_by_attachment_id(get_static_option($gateway.'_preview_logo')) !!}
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" id="confirmPaymentBtn" class="btn-profile btn-bg-1" style="border: none;">{{ __('Confirm & Submit') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $('#companyName').on('input', function (){
                $('#previewCompanyName').text($(this).val())
            })
            $('#title').on('input', function (){
                $('#previewTitle').text($(this).val())
            })
            $('#description').on('input', function (){
                $('#previewDescription').text($(this).val())
            })

            function updateTotal() {
                var pricePerUnit = parseFloat($('#optimizeFor option:selected').data('price'));
                var selectedAdType = $('#optimizeFor option:selected').val();
                var quantity = parseInt($('#quantity').val());

                var totalPrice = (pricePerUnit * quantity).toFixed(2);

                $('#total').text('$' + totalPrice);
                $('#modal_total_amount').text('$' + totalPrice);
                $('#previewPricePerQuantity').text('$'+pricePerUnit+' per ' + selectedAdType);
                $('#ppq_input').val(pricePerUnit);
            }

            // Bind the change event to both select boxes
            $('#optimizeFor').change(updateTotal);
            $('#quantity').change(updateTotal);

            // Initial calculation
            updateTotal();

            $('#formFile').change(function() {
                // Check if a file was selected
                if (this.files && this.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        // Set the src attribute of the preview image
                        $('#previewUploadedImage').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Payment Gateway Selection
            $('#payment_gateway_list li').on('click', function() {
                $(this).addClass('selected active').siblings().removeClass('selected active');
                let gateway = $(this).data('gateway');
                $('#selected_payment_gateway').val(gateway);
            });

            // Set initial gateway if wallet not available
            if ($('#payment_gateway_list li.selected').length === 0) {
                 $('#payment_gateway_list li:first').addClass('selected active');
                 $('#selected_payment_gateway').val($('#payment_gateway_list li:first').data('gateway'));
            }

            // Pay Now validation check
            $('#payNowBtn').on('click', function() {
                var fileInput = $('#formFile');
                if (fileInput[0].files.length === 0) {
                    toastr_warning_js('{{ __("Please upload an attachment before proceeding.") }}');
                    return;
                }
                
                // If everything is fine, show the payment modal
                $('#adPaymentModal').modal('show');
            });

            // Confirm & Submit
            $('#confirmPaymentBtn').on('click', function() {
                var btn = $(this);
                btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').attr('disabled', true);
                $('#adCreateForm').submit();
            });
        })
    </script>
@endsection