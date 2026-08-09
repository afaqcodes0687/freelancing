@extends('frontend.layout.master')
@section('site_title', __('Affiliate Dashboard'))

@section('style')
    <style>
        .affiliate-stats-card {
            border-radius: 12px;
            padding: 24px;
            color: #fff;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .affiliate-stats-card:hover {
            transform: translateY(-4px);
        }

        .stat-val {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            opacity: 0.8;
            letter-spacing: 0.05em;
        }

        .card-balance {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .card-pending {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .card-earned {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .card-payouts {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }

        .section-card {
            background: #fff;
            border: 1px id="solid" #e2e8f0;
            border-radius: 12px;
            padding: 24px;
        }
    </style>
@endsection

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('Affiliate Dashboard')" :innerTitle="__('Dashboard')" />

        <div class="responsive-overlay"></div>
        <div class="profile-settings-area pat-100 pab-100 section-bg-2">
            <div class="container">
                <div class="row g-4">
                    @include('frontend.user.layout.partials.sidebar')
                    <div class="col-xl-9 col-lg-8">
                        <div class="profile-settings-wrapper">

                            <div class="section-card">
                                <div class="mb-4">
                                    <h5 class="fw-semibold text-dark mb-1">{{ __('Account Overview') }}</h5>
                                    <p class="text-muted small mb-0">
                                        {{ __('Summary of your referral performance and earnings.') }}</p>
                                </div>

                                <div class="row g-4">
                                    <div class="col-xxl-3 col-lg-6 col-sm-6">
                                        <div class="affiliate-stats-card card-balance">
                                            <div class="stat-val">$ {{ number_format($balance, 2) }}</div>
                                            <div class="stat-label">{{ __('Wallet Balance') }}</div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-3 col-lg-6 col-sm-6">
                                        <div class="affiliate-stats-card card-pending">
                                            <div class="stat-val">$ {{ number_format($pendingCommission, 2) }}</div>
                                            <div class="stat-label">{{ __('Pending Commissions') }}</div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-3 col-lg-6 col-sm-6">
                                        <div class="affiliate-stats-card card-earned">
                                            <div class="stat-val">$ {{ number_format($totalEarned, 2) }}</div>
                                            <div class="stat-label">{{ __('Lifetime Earnings') }}</div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-3 col-lg-6 col-sm-6">
                                        <div class="affiliate-stats-card card-payouts">
                                            <div class="stat-val">{{ $pendingPayoutCount }}</div>
                                            <div class="stat-label">{{ __('Pending Payouts') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded-3 bg-light">
                                                <h6 class="small fw-bold text-muted text-uppercase mb-2">
                                                    {{ __('Stats (Last 30 Days)') }}</h6>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold h5 mb-0">{{ $clicksTotalLast30 }}</div>
                                                        <div class="text-muted small">{{ __('Total Clicks') }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold h5 mb-0">{{ $conversionsLast30 }}</div>
                                                        <div class="text-muted small">{{ __('Conversions') }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold h5 mb-0 text-primary">{{ $conversionRate }}%
                                                        </div>
                                                        <div class="text-muted small">{{ __('Conv. Rate') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div
                                                class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-center">
                                                <h6 class="small fw-bold text-muted text-uppercase mb-2">
                                                    {{ __('Quick Referral Link') }}</h6>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control" value="{{ $referralLink }}"
                                                        readonly id="refLink">
                                                    <button class="btn btn-primary" type="button"
                                                        onclick="copyRef()">{{ __('Copy') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        function copyRef() {
            const copyText = document.getElementById("refLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value).then(() => {
                toastr.success('Referral link copied to clipboard!', 'Success!');
            });
        }
    </script>
@endsection