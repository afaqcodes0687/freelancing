@extends('frontend.layout.master')
@section('site_title', __('Affiliate Payouts'))

@section('style')
    <style>
        .affiliate-tool-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .balance-card {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
        }
        .table-custom thead {
            background: #f8fafc;
        }
        .table-custom th {
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
        }
    </style>
@endsection

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('Affiliate Payouts')" :innerTitle="__('Affiliate Payouts')" />

        <div class="profile-settings-area pat-100 pab-100 section-bg-2">
            <div class="container">
                <div class="row g-4">
                    @include('frontend.user.layout.partials.sidebar')

                    <div class="col-xl-9 col-lg-8">
                        <div class="profile-settings-wrapper">

                            <div class="row g-4">
                                <div class="col-md-5">
                                    <div class="balance-card h-100 d-flex flex-column justify-content-center">
                                        <h6 class="text-white-50 text-uppercase mb-2 small fw-bold">{{ __('Available Balance') }}</h6>
                                        <h2 class="text-white mb-0 mt-1">$ {{ number_format($affiliate->balance, 2) }}</h2>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="affiliate-tool-card p-4 h-100">
                                        <h5 class="mb-3 fw-semibold text-dark">{{ __('Request Payout') }}</h5>
                                        <form id="payoutForm">
                                            @csrf
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">{{ __('Amount') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0">$</span>
                                                        <input type="number" step="0.01" name="amount" class="form-control border-start-0 ps-1" placeholder="0.00" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">{{ __('Payment Method') }}</label>
                                                    <select name="payment_method" class="form-select" required>
                                                        <option value="">{{ __('Select Method') }}</option>
                                                        <option value="jazzcash">JazzCash</option>
                                                        <option value="easypaisa">Easypaisa</option>
                                                        <option value="bank">Bank Transfer</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">{{ __('Account Details') }}</label>
                                                    <textarea name="account_details" class="form-control" rows="3" placeholder="{{ __('Enter your account number, title, or any other necessary details...') }}" required></textarea>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary w-100 mt-1">
                                                        <i class="fas fa-paper-plane me-2"></i> {{ __('Request Payout') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Payout Information --}}
                            <div class="alert alert-info mt-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); border-left: 4px solid #0ea5e9 !important;">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-info-circle me-3 mt-1" style="color: #0284c7; font-size: 20px;"></i>
                                    <div>
                                        <h6 class="fw-bold mb-2" style="color: #0c4a6e;">
                                            <i class="fas fa-wallet me-2"></i>Payout Requirements
                                        </h6>
                                        <ul class="mb-0 ps-3" style="color: #075985; font-size: 13px; line-height: 1.8;">
                                            <li><strong>Minimum Payout:</strong> You can request a payout when your available balance reaches <strong>${{ number_format($minPayout ?? 100, 2) }}</strong> or more.</li>
                                            <li><strong>Pending Commissions:</strong> Commissions marked as "Pending" must be approved by admin before they appear in your available balance.</li>
                                            <li><strong>Processing Time:</strong> Payout requests are typically processed within 3-5 business days after approval.</li>
                                            <li><strong>Payment Methods:</strong> Choose your preferred payment method (JazzCash, Easypaisa, or Bank Transfer) when requesting a payout.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- History --}}
                            <div class="affiliate-tool-card mt-4 overflow-hidden">
                                <div class="card-header bg-white border-0 p-4">
                                    <h5 class="fw-semibold mb-0 text-dark">{{ __('Payout History') }}</h5>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-custom align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-4">#</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Method') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="pe-4">{{ __('Date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($payouts as $p)
                                                <tr>
                                                    <td class="ps-4 fw-medium text-muted">{{ $p->id }}</td>
                                                    <td class="fw-bold text-dark">$ {{ number_format($p->amount, 2) }}</td>
                                                    <td>
                                                        <span class="text-uppercase small fw-bold text-muted">{{ $p->payment_method }}</span>
                                                    </td>
                                                    <td>
                                                        @if($p->status == 'pending')
                                                            <span class="badge bg-warning text-dark px-3 rounded-pill">{{ __('Pending') }}</span>
                                                        @elseif($p->status == 'paid')
                                                            <span class="badge bg-success px-3 rounded-pill">{{ __('Paid') }}</span>
                                                        @else
                                                            <span class="badge bg-danger px-3 rounded-pill">{{ __('Failed') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="pe-4 text-muted small">{{ $p->created_at->format('d M, Y') }}<br><span style="font-size: 10px;">{{ $p->created_at->format('H:i') }}</span></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-5 text-muted">
                                                        <i class="fas fa-history fa-2x mb-3 d-block opacity-25"></i>
                                                        {{ __('No payout records found.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="p-4 border-top">
                                    {{ $payouts->links() }}
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 3000
        };

        $('#payoutForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const btn = form.find('button');
            const originalContent = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> {{ __("Processing...") }}');

            $.ajax({
                url: "{{ route('affiliate.payouts.request') }}",
                type: "POST",
                data: form.serialize(),
                success: function (res) {
                    if (res.status === 'success') {
                        toastr.success(res.msg);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        toastr.warning(res.msg || '{{ __("Something went wrong.") }}');
                        btn.prop('disabled', false).html(originalContent);
                    }
                },
                error: function () {
                    toastr.error('{{ __("Server error occurred.") }}');
                    btn.prop('disabled', false).html(originalContent);
                }
            });
        });
    </script>
@endsection