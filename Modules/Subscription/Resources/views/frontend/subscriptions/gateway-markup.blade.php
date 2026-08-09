<div class="modal fade" id="paymentGatewayModal" tabindex="-1" aria-labelledby="paymentGatewayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('subscriptions.buy') }}" method="post" enctype="multipart/form-data" id="payment-form">
            <input type="hidden" name="subscription_id" id="subscription_id">
            <input type="hidden" name="subscription_price" id="subscription_price">
            <input type="hidden" name="downgrade" id="downgrade_status" value="0"> <!-- Hidden field to indicate downgrade -->
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    @if(Auth::guard('web')->check())
                        @if(Auth::guard('web')->user()->user_type == 1)
                            <x-notice.general-notice :description="__('Notice: Please login as a freelancer to buy a Packages.')" />
                        @else
                            <h4 id="modal-title">{{ __('Buy Package') }}</h4>
                        @endif
                    @endif
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>
                <div class="modal-body">
                    <div id="free-plan-message" style="display: none;">
                        <p>{{ __('Are you sure you want to downgrade your current package? This may limit some of the features you\'re currently using. Please confirm.') }}</p>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary" id="free_plan_confirm_button">Yes</button>
                        </div>
                    </div>
                    <div class="confirm-payment payment-border" id="payment-section">
                        <div class="single-checkbox">
                            <div class="checkbox-inlines">
                                <label class="checkbox-label load_after_login" for="choose">
                                <div class="payment-gateway-wrapper payment_getway_image" style="width:480px">
                                    <input type="hidden" name="selected_payment_gateway" id="order_from_user_wallet" value="{{ get_static_option('site_default_payment_gateway') }}">
                                    <ul>
                                        @if(Auth::check() && Auth::user()->user_wallet && Auth::user()->user_wallet->remaining_balance > 0)
                                            <li data-gateway="wallet">
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
                                <input type="hidden" name="return_url" value="https://rightfreelancer.com/packages/paypro/return">
                                <input type="hidden" name="cancel_url" value="https://rightfreelancer.com/paypro/cancel">
                                <input type="hidden" name="callback_url" value="https://rightfreelancer.com/paypro/ipn">
                                @php $subTaxRate = (float) (get_static_option('subscription_tax_rate') ?? env('SUBSCRIPTION_TAX_RATE', 0)); @endphp
                                <div id="subscription_tax_notice" class="text-muted mt-2" style="font-size: 13px; {{ $subTaxRate > 0 ? '' : 'display:none;' }}">
                                    {{ __('Applicable tax of') }} {{ rtrim(rtrim(number_format($subTaxRate, 2, '.', ''), '0'), '.') }}% {{ __('will be added to the package price at checkout.') }}
                                </div>
                                <div id="subscription_amount_breakdown" class="mt-2" style="display:none;">
                                    <div class="d-flex justify-content-between"><span>{{ __('Subtotal') }}</span><span id="sub_brk_subtotal">—</span></div>
                                    <div class="d-flex justify-content-between"><span>{{ __('Tax') }} (<span id="sub_brk_rate">{{ rtrim(rtrim(number_format($subTaxRate, 2, '.', ''), '0'), '.') }}</span>%)</span><span id="sub_brk_tax">—</span></div>
                                    <div class="d-flex justify-content-between fw-bold"><span>{{ __('Total') }}</span><span id="sub_brk_total">—</span></div>
                                </div>
                                    @if (Auth::check() && Auth::user()->user_wallet)
                                        {!! \App\Helper\PaymentGatewayList::renderWalletForm() !!}
                                        <div class="wallet-balance-info mt-2">
                                            <span class="wallet-balance d-block">{{ __('Total Wallet Balance:') }}
                                                <strong class="main-balance text-primary">{{ float_amount_with_currency_symbol(Auth::user()->user_wallet->balance) }}</strong></span>
                                            @if(Auth::user()->user_wallet->signup_bonus > 0)
                                                <span class="signup-bonus d-block">{{ __('Signup Bonus:') }}
                                                    <strong class="bonus-balance text-success">{{ float_amount_with_currency_symbol(Auth::user()->user_wallet->signup_bonus) }}</strong></span>
                                                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                                    * {{ __('Signup bonus is only usable for the Weekly Starter package.') }}
                                                </small>
                                            @endif
                                        </div>
                                        <br>
                                        <span class="display_balance"></span>
                                        <br>
                                        <span class="deposit_link"></span>
                                    @endif
                                    <!-- {!! \App\Helper\PaymentGatewayList::renderPaymentGatewayForForm(false) !!} -->
                                </label>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-profile btn-outline-gray btn-hover-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    @if (Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2)
                        <button type="submit" class="btn-profile btn-bg-1 buy_subscription" id="confirm_buy_subscription_load_spinner">{{ __('Buy Now') }} <span id="buy_subscription_load_spinner"></span></button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
<script>
(function(){
    // gateway selection UI
    document.querySelectorAll('.payment_getway_image li').forEach(li => {
        li.addEventListener('click', function () {
            document.querySelectorAll('.payment_getway_image li').forEach(el => el.classList.remove('selected'));
            this.classList.add('selected');
            document.querySelector('#order_from_user_wallet').value = this.getAttribute('data-gateway');
            // Uncheck wallet checkbox when selecting other gateways
            const walletCheckbox = document.getElementById('wallet_selected_payment_gateway');
            if (walletCheckbox && this.getAttribute('data-gateway') !== 'wallet') {
                walletCheckbox.checked = false;
            }
            toggleTaxVisibility();
        });
    });

    // Handle wallet checkbox separately
    const walletCheckbox = document.getElementById('wallet_selected_payment_gateway');
    if (walletCheckbox) {
        walletCheckbox.addEventListener('change', function () {
            if (this.checked) {
                document.querySelectorAll('.payment_getway_image li').forEach(el => el.classList.remove('selected'));
                document.querySelector('#order_from_user_wallet').value = 'wallet';
            } else {
                // Re-select default gateway (paypro) when unchecked
                const payproLi = document.querySelector('.payment_getway_image li[data-gateway="paypro"]');
                if (payproLi) {
                    payproLi.classList.add('selected');
                    document.querySelector('#order_from_user_wallet').value = 'paypro';
                }
            }
            toggleTaxVisibility();
        });
    }

    const taxRate = parseFloat('{{ $subTaxRate }}') || 0;
    const elSubtotal = document.getElementById('sub_brk_subtotal');
    const elTax = document.getElementById('sub_brk_tax');
    const elTotal = document.getElementById('sub_brk_total');
    const elRate = document.getElementById('sub_brk_rate');
    const box = document.getElementById('subscription_amount_breakdown');
    const priceInput = document.getElementById('subscription_price');
    const taxNotice = document.getElementById('subscription_tax_notice');

    function formatCurrency(amount){
        // Fallback to plain fixed-2 formatting; avoid backend helpers in JS context
        const n = Number(amount);
        return isNaN(n) ? '-' : n.toFixed(2);
    }

    function updateBreakdown(){
        // hide breakdown entirely when wallet is selected
        const gw = (document.getElementById('order_from_user_wallet')?.value || '').toLowerCase();
        if (gw === 'wallet') { if (box) box.style.display = 'none'; return; }
        const p = parseFloat(priceInput.value);
        if (!isNaN(p) && p > 0 && taxRate >= 0){
            const tax = +(p * taxRate / 100).toFixed(2);
            const total = +(p + tax).toFixed(2);
            if (elSubtotal) elSubtotal.textContent = formatCurrency(p);
            if (elTax) elTax.textContent = formatCurrency(tax);
            if (elTotal) elTotal.textContent = formatCurrency(total);
            if (elRate) elRate.textContent = (Math.round(taxRate * 100) / 100).toString().replace(/\.0+$/,'');
            if (box) box.style.display = '';
        } else if (box) {
            box.style.display = 'none';
        }
    }

    function toggleTaxVisibility(){
        const gw = (document.getElementById('order_from_user_wallet')?.value || '').toLowerCase();
        if (gw === 'wallet'){
            if (taxNotice) taxNotice.style.display = 'none';
            if (box) box.style.display = 'none';
        } else {
            if (taxNotice) taxNotice.style.display = (taxRate > 0 ? '' : 'none');
            // breakdown visibility handled by updateBreakdown()
            updateBreakdown();
        }
    }

    // Expose helper to set context from package cards
    window.setSubscriptionPaymentContext = function(id, price){
        const idEl = document.getElementById('subscription_id');
        if (idEl) idEl.value = id;
        if (priceInput) priceInput.value = price;
        updateBreakdown();
        const modal = document.getElementById('paymentGatewayModal');
        if (modal && typeof bootstrap !== 'undefined'){
            const bs = bootstrap.Modal.getOrCreateInstance(modal);
            bs.show();
        }
    };

    // Generic delegation: if your Buy buttons have data attributes, this will auto-fill
    document.addEventListener('click', function(e){
        const t = e.target.closest('[data-subscription-id]');
        if (!t) return;
        const id = t.getAttribute('data-subscription-id');
        const price = t.getAttribute('data-price');
        if (id){ document.getElementById('subscription_id').value = id; }
        if (price){ priceInput.value = price; }
        updateBreakdown();
    });

    // Update on modal show in case price was set before
    const modalEl = document.getElementById('paymentGatewayModal');
    if (modalEl){
        modalEl.addEventListener('shown.bs.modal', function(){
            toggleTaxVisibility();
            updateBreakdown();
        });
    }

    // initial state in case markup is rendered visible without interactions
    toggleTaxVisibility();
})();
</script>
