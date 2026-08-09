@extends('frontend.layout.master')
@section('site_title', __('Clicks'))

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
    </style>
@endsection

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('Click Reports')" :innerTitle="__('Click Reports')" />

        <div class="responsive-overlay"></div>
        <div class="profile-settings-area pat-100 pab-100 section-bg-2">
            <div class="container">
                <div class="row g-4">
                    @include('frontend.user.layout.partials.sidebar')

                    <div class="col-xl-9 col-lg-8">
                        <div class="profile-settings-wrapper">
                            <div class="affiliate-tool-card overflow-hidden">
                                <div class="card-header bg-white border-0 p-4">
                                    <h5 class="fw-semibold mb-1 text-dark">{{ __('Referral Click History') }}</h5>
                                    <p class="text-muted small mb-0">
                                        {{ __('Track every visitor that clicks on your referral links.') }}</p>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-custom align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-4">#</th>
                                                <th>{{ __('IP Address') }}</th>
                                                <th>{{ __('Referer') }}</th>
                                                <th class="pe-4">{{ __('Date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($clicks as $c)
                                                <tr>
                                                    <td class="ps-4 fw-medium text-muted">{{ $c->id }}</td>
                                                    <td class="fw-semibold text-dark">{{ $c->ip_address }}</td>
                                                    <td>
                                                        <span
                                                            class="text-muted small">{{ $c->referer ?? __('Direct / Unknown') }}</span>
                                                    </td>
                                                    <td class="pe-4 text-muted small">
                                                        {{ \Carbon\Carbon::parse($c->clicked_at)->format('d M, Y') }}
                                                        <br><span
                                                            style="font-size: 10px;">{{ \Carbon\Carbon::parse($c->clicked_at)->format('H:i') }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-5 text-muted">
                                                        <i class="fas fa-mouse-pointer fa-2x mb-3 d-block opacity-25"></i>
                                                        {{ __('No click data recorded yet.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-4 border-top">
                                    {{ $clicks->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection