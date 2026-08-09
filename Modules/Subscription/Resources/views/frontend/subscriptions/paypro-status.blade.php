@extends('subscription::frontend.subscriptions.status-layout')

@section('content')
<div class="paypro-status-container" style="padding: 100px 0; text-align: center; background: #f8fafc;">
    <div class="container">
        <div class="card shadow-sm border-0" style="max-width: 600px; margin: 0 auto; border-radius: 20px; overflow: hidden;">
            <div class="card-body p-5">
                <div id="status-processing" class="status-section">
                    <div class="spinner-border text-primary mb-4" role="status" style="width: 4rem; height: 4rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h2 class="fw-bold mb-3">{{ __('Processing Payment...') }}</h2>
                    <p class="text-muted">{{ __('Please wait while we verify your transaction with PayPro. Do not refresh this page.') }}</p>
                </div>

                <div id="status-success" class="status-section d-none">
                    <div class="success-icon mb-4" style="font-size: 5rem; color: #22c55e;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 class="fw-bold mb-3">{{ __('Payment Successful!') }}</h2>
                    <p class="text-muted mb-4">{{ __('Your subscription has been activated successfully.') }}</p>
                    <a href="#" id="success-redirect-btn" class="btn btn-primary btn-lg px-5" style="border-radius: 50px;">
                        {{ __('Go to Dashboard') }}
                    </a>
                </div>

                <div id="status-failed" class="status-section d-none">
                    <div class="error-icon mb-4" style="font-size: 5rem; color: #ef4444;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h2 class="fw-bold mb-3">{{ __('Payment Failed') }}</h2>
                    <p class="text-muted mb-4">{{ __('We couldn\'t verify your payment. If you have already paid, please contact support.') }}</p>
                    <a href="{{ route('subscriptions.all') }}" class="btn btn-outline-danger btn-lg px-5" style="border-radius: 50px;">
                        {{ __('Try Again') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderId = "{{ $orderId }}";
        const checkStatusUrl = "{{ route('subscriptions.paypro.check-status', ['order_id' => $orderId]) }}";
        
        const processingSection = document.getElementById('status-processing');
        const successSection = document.getElementById('status-success');
        const failedSection = document.getElementById('status-failed');
        const redirectBtn = document.getElementById('success-redirect-btn');

        let attempts = 0;
        const maxAttempts = 30; // 60 seconds total

        const checkStatus = setInterval(async () => {
            attempts++;
            if (attempts > maxAttempts) {
                clearInterval(checkStatus);
                processingSection.classList.add('d-none');
                failedSection.classList.remove('d-none');
                return;
            }

            try {
                const response = await fetch(checkStatusUrl);
                const data = await response.json();

                if (data.status === 'complete') {
                    clearInterval(checkStatus);
                    processingSection.classList.add('d-none');
                    successSection.classList.remove('d-none');
                    redirectBtn.href = data.redirect;
                    
                    // Auto redirect after 3 seconds
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 3000);
                }
            } catch (error) {
                console.error('Error checking status:', error);
            }
        }, 2000);
    });
</script>
@endsection
