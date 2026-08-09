@extends('frontend.layout.master')
@section('site_title', __('Commissions'))

@section('style')
    <style>
        .affiliate-tool-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
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

        .badge-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
    </style>
@endsection

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('Earned Commissions')" :innerTitle="__('Commissions')" />

        <div class="responsive-overlay"></div>
        <div class="profile-settings-area pat-100 pab-100 section-bg-2">
            <div class="container">
                <div class="row g-4">
                    @include('frontend.user.layout.partials.sidebar')

                    <div class="col-xl-9 col-lg-8">
                        <div class="profile-settings-wrapper">
                            <div class="affiliate-tool-card overflow-hidden">
                                <div class="card-header bg-white border-0 p-4">
                                    <h5 class="fw-semibold mb-1 text-dark">{{ __('Commission History') }}</h5>
                                    <p class="text-muted small mb-0">
                                        {{ __('View all your earnings from successful referrals and purchases.') }}</p>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-custom align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-4">#</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Description') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="pe-4">{{ __('Date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($commissions as $c)
                                                <tr>
                                                    <td class="ps-4 fw-medium text-muted">{{ $c->id }}</td>
                                                    <td class="fw-bold text-success" style="font-size: 15px;">
                                                        $ {{ number_format($c->commission_amount ?? $c->amount, 2) }}
                                                    </td>
                                                    <td>
                                                        <span class="text-dark small fw-medium">{{ $c->description }}</span>
                                                    </td>
                                                    <td>
                                                        @if($c->status == 'approved' || $c->status == 'complete')
                                                            <span class="badge badge-status bg-success">{{ __('Approved') }}</span>
                                                        @elseif($c->status == 'pending')
                                                            <span
                                                                class="badge badge-status bg-warning text-dark">{{ __('Pending') }}</span>
                                                        @elseif($c->status == 'rejected')
                                                            <span class="badge badge-status bg-danger">{{ __('Rejected') }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-status bg-info">{{ ucfirst($c->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="pe-4 text-muted small">
                                                        {{ \Carbon\Carbon::parse($c->created_at)->format('d M, Y') }}
                                                        <br><span
                                                            style="font-size: 10px;">{{ \Carbon\Carbon::parse($c->created_at)->format('H:i') }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-5 text-muted">
                                                        <i class="fas fa-hand-holding-usd fa-2x mb-3 d-block opacity-25"></i>
                                                        {{ __('No commissions earned yet.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="p-4 border-top">
                                    {{ $commissions->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection