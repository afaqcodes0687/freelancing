@extends('backend.layout.master')
@section('title', __('Affiliate Payouts'))
@section('style')
    <style>
        .allOrders__list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .allOrders__list__item {
            cursor: pointer;
            padding: 8px 16px;
            border: 1px solid #e5e5e5;
            border-radius: 5px;
            background: #fff;
            transition: all 0.3s;
        }

        .allOrders__list__item.active {
            background: #28a745;
            color: #fff;
            border-color: #28a745;
        }
    </style>
@endsection
@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <x-notice.general-notice :description="__('Notice: Manage affiliate payout requests here.')"
                    :description1="__('Notice: Use tabs to filter by status.')"
                    :description2="__('Notice: You can search by Affiliate ID, Name, or Email.')" />
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="customMarkup__single__item__flex">
                            <h4 class="customMarkup__single__title">{{ __('Affiliate Payouts') }}</h4>
                            <div class="search_wrapper">
                                <form action="{{ route('admin.affiliate.payouts') }}" method="GET" id="search_form">
                                    <input type="hidden" name="status" value="{{ request()->status }}">
                                    <div class="input-group">
                                        <input type="text" name="string_search" class="form-control"
                                            placeholder="{{ __('Search by ID, Name, Email...') }}"
                                            value="{{ request()->string_search }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit"><i
                                                    class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="allOrders__list">
                            <a href="{{ route('admin.affiliate.payouts', ['status' => 'all']) }}"
                                class="order_sort_by_status allOrders__list__item {{ request()->status == 'all' || !request()->status ? 'active' : '' }}">
                                {{ __('All') }} ({{ $all_count }})
                            </a>
                            <a href="{{ route('admin.affiliate.payouts', ['status' => 'pending']) }}"
                                class="order_sort_by_status allOrders__list__item {{ request()->status == 'pending' ? 'active' : '' }}">
                                {{ __('Pending') }} ({{ $pending_count }})
                            </a>
                            <a href="{{ route('admin.affiliate.payouts', ['status' => 'paid']) }}"
                                class="order_sort_by_status allOrders__list__item {{ request()->status == 'paid' ? 'active' : '' }}">
                                {{ __('Paid') }} ({{ $paid_count }})
                            </a>
                            <a href="{{ route('admin.affiliate.payouts', ['status' => 'rejected']) }}"
                                class="order_sort_by_status allOrders__list__item {{ request()->status == 'rejected' ? 'active' : '' }}">
                                {{ __('Rejected') }} ({{ $rejected_count }})
                            </a>
                        </div>
                        <div class="customMarkup__single__inner mt-4">
                            <!-- Table Start -->
                            @fragment('search-results')
                                <div class="custom_table style-04 search_result">
                                    <table class="DataTable_activation">
                                        <thead>
                                            <tr>
                                                <th>{{ __('ID') }}</th>
                                                <th>{{ __('Affiliate') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Method') }}</th>
                                                <th>{{ __('Account Details') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Requested On') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payouts as $p)
                                                <tr id="payout-row-{{ $p->id }}">
                                                    <td>{{ $p->id }}</td>
                                                    <td>
                                                        {{ optional($p->affiliate)->first_name ?? '' }}
                                                        {{ optional($p->affiliate)->last_name ?? '' }}
                                                        <br>
                                                        <small>ID: {{ $p->affiliate_id }}</small>
                                                    </td>
                                                    <td>{{ float_amount_with_currency_symbol($p->amount) }}</td>
                                                    <td>{{ ucfirst($p->payment_method ?? 'n/a') }}</td>
                                                    <td>
                                                        <div style="max-width: 250px; white-space: normal; word-break: break-all;">
                                                            {{ $p->account_details }}
                                                        </div>
                                                    </td>
                                                    <td id="status-{{ $p->id }}">
                                                        <x-status.table.order-status :status="$p->status == 'paid' ? 3 : ($p->status == 'rejected' ? 4 : 0)" />
                                                    </td>
                                                    <td>{{ $p->created_at->format('d M, Y H:i') }}</td>
                                                    <td>
                                                        @if($p->status == 'pending')
                                                            <button class="btn btn-sm btn-success approve-btn"
                                                                data-id="{{ $p->id }}">{{ __('Approve') }}</button>
                                                        @else
                                                            <span class="text-muted">{{ __('No actions') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="mt-4">
                                        {{ $payouts->links() }}
                                    </div>
                                </div>
                            @endfragment
                            <!-- Table End -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <x-sweet-alert.sweet-alert2-js />
    <script>     (function ($) {         "use strict";         $(document).ready(function () {

                 // Approve Action             $(document).on('click', '.approve-btn', function () {                 let id = $(this).data('id');                 Swal.fire({                     title: '{{__("Approve Payout?")}}',                     text: '{{__("This will deduct the amount from affiliate balance and mark as paid.")}}',                     icon: 'warning',                     showCancelButton: true,                     confirmButtonColor: '#3085d6',                     cancelButtonColor: '#d33',                     confirmButtonText: "{{__('Yes, Approve it!')}}"                 }).then((result) => {                     if (result.isConfirmed) {                         $.post("{{ route('admin.affiliate.payouts.approve', ':id') }}".replace(':id', id), {                             _token: "{{ csrf_token() }}"                         }).done(function (res) {                             if (res.status === 'success') {                                 Swal.fire('Approved!', res.msg, 'success');                                 fetchData();                             } else {                                 Swal.fire('Error!', res.msg, 'error');                             }                         }).fail(function (xhr) {                             Swal.fire('Error!', 'Server Error', 'error');                         });                     }                 });             });
             });     })(jQuery);
    </script>
@endsection