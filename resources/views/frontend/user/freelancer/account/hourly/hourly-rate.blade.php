<!-- Setup Hourly Rate Starts -->
<div class="setup-wrapper-contents">
    <div class="setup-wrapper-contents-item">
        <h3 class="setup-wrapper-contents-title">{{ get_static_option('hourly_rate_title') ?? __('What is your hourly rate?') }}</h3>
        <div class="setup-wrapper-finish">
            <div class="custom-form">
                <form id="hourlyRateForm">
                    <div class="single-input single-input-icon">
                        <input type="number"
                               name="hourly_rate"
                               id="hourly_rate"
                               class="form--control"
                               value="{{ Auth::guard('web')->user()->hourly_rate ?? 20 }}"
                               placeholder="{{ __('Enter your hourly rate') }}"
                               min="1" max="1000" step="1">
                        <span class="input-icon">{{ site_currency_symbol() ?? '$' }}</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Setup Hourly Rate Ends -->
