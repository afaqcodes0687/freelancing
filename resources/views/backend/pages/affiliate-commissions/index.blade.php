@extends('backend.layout.master')
@section('title', __('All Commissions'))
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
                <x-notice.general-notice :description="__('Notice: Manage affiliate commissions here.')"
                    :description1="__('Notice: Use tabs to filter by status.')"
                    :description2="__('Notice: You can search by Affiliate ID, Name, or Email.')" />
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="customMarkup__single__item__flex">
                            <h4 class="customMarkup__single__title">{{ __('Affiliate Commissions') }}</h4>
                            <div class="search_wrapper">
                                <form action="{{ route('admin.affiliate.commissions') }}" method="GET" id="search_form">
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
                            <a href="{{ route('admin.affiliate.commissions', ['status' => 'all']) }}"
                                class="order_sort_by_status allOrders__list__item {{ request()->status == 'all' || !request()->status ? 'active' : '' }}">
                                {{ __('All') }} ({{ $all_count }})
                            </a>
                            <a href="{{ route('admin.affiliate.commissions', ['status' => 'pending']) }}"
                                class="order_sort_by_status allOrders__list__item {{ request()->status == 'pending' ? 'active' : '' }}">
                                {{ __('Pending') }} ({{ $pending_count }})
                            </a>
                            <a href="{{ route('admin.affiliate.commissions', ['status' => 'approved']) }}"
                                class="order_sort_by_status allOrders__list__item {{ request()->status == 'approved' ? 'active' : '' }}">
                                {{ __('Approved') }} ({{ $approved_count }})
                            </a>
                            <a href="{{ route('admin.affiliate.commissions', ['status' => 'rejected']) }}"
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
                                                <th>{{ __('Note') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Created') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($commissions as $c)
                                                <tr id="commission-row-{{ $c->id }}">
                                                    <td>{{ $c->id }}</td>
                                                    <td>
                                                        {{ optional($c->affiliate)->first_name ?? '' }}
                                                        {{ optional($c->affiliate)->last_name ?? '' }}
                                                        <br>
                                                        <small>ID: {{ $c->affiliate_id }}</small>
                                                    </td>
                                                    <td>{{ float_amount_with_currency_symbol($c->commission_amount) }}</td>
                                                    <td>{{ Str::limit($c->description, 40) }}</td>
                                                    <td id="status-{{ $c->id }}">
                                                        {{-- Mapping status to order-status component integers: 3=Complete/Approved,
                                                        4=Cancel/Rejected, 0=Pending --}}
                                                        <x-status.table.order-status :status="$c->status == 'approved' ? 3 : ($c->status == 'rejected' ? 4 : 0)" />
                                                    </td>
                                                    <td>{{ $c->created_at->format('d M, Y H:i') }}</td>
                                                    <td>
                                                        @if($c->status == 'pending')
                                                            <button class="btn btn-sm btn-success approve-btn"
                                                                data-id="{{ $c->id }}">{{ __('Approve') }}</button>
                                                            <button class="btn btn-sm btn-danger reject-btn"
                                                                data-id="{{ $c->id }}">{{ __('Reject') }}</button>
                                                        @else
                                                            <span class="text-muted">{{ __('No actions') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="mt-4">
                                        {{ $commissions->links() }}
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
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {

                $(document).on('click', '.approve-btn, .reject-btn', function () {
                    const btn = $(this);
                    const id = btn.data('id');
                    const isApprove = btn.hasClass('approve-btn');
                    const action = isApprove ? 'approve' : 'reject';
                    const actionText = isApprove ? "{{__('Approve')}}" : "{{__('Reject')}}";
                    const btnColor = isApprove ? '#28a745' : '#d33';

                    // ✅ Correct URL
                    const url = isApprove
                        ? "{{ url('admin/affiliate/commissions') }}/" + id + "/approve"
                        : "{{ url('admin/affiliate/commissions') }}/" + id + "/reject";

                    Swal.fire({
                        title: `{{__('Are you sure?')}}`,
                        text: `{{__('You want to')}} ${actionText} {{__('this commission?')}}`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: btnColor,
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: `{{__('Yes')}}, ${actionText} {{__('it!')}}`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.post(url, { _token: "{{ csrf_token() }}" })
                                .done(res => {
                                    if (res.status === 'success') {
                                        Swal.fire({
                                            title: "{{__('Success!')}}",
                                            text: res.msg,
                                            icon: "success"
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire("{{__('Error!')}}", res.msg || "{{__('Something went wrong.')}}", "error");
                                    }
                                })
                                .fail(xhr => {
                                    Swal.fire("{{__('Error!')}}", "{{__('Server Error')}}", "error");
                                });
                        }
                    });
                });

            });
        })(jQuery);
    </script>
@endsection