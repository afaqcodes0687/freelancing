@extends('backend.layout.master')
@section('title', __('Ad Management'))
@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="customMarkup__single__item__flex">
                            <h4 class="customMarkup__single__title">{{ __('All Advertisements') }}</h4>
                        </div>
                        <div class="customMarkup__single__inner mt-4">
                            <!-- Table Start -->
                            <div class="custom_table style-04">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('Image') }}</th>
                                            <th>{{ __('Title') }}</th>
                                            <th>{{ __('Company') }}</th>
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Payment') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($all_ads as $ad)
                                            <tr>
                                                <td>{{ $ad->id }}</td>
                                                <td>
                                                    <div class="img-wrap"
                                                        style="width: 60px; height: 60px; overflow: hidden; border-radius: 5px;">
                                                        <img src="{{ asset('assets/uploads/ads/' . $ad->cover_image) }}" alt=""
                                                            style="width: 100%; height: 100%; object-fit: cover;">
                                                    </div>
                                                </td>
                                                <td>{{ $ad->title }}</td>
                                                <td>{{ $ad->company }}</td>
                                                <td>{{ optional($ad->user)->first_name }} {{ optional($ad->user)->last_name }}
                                                </td>
                                                <td>{{ $ad->quantity }}</td>
                                                <td>
                                                    @php
                                                        $display_budget = $ad->budget;
                                                        if ($display_budget <= 0 && $ad->quantity > 0 && $ad->ppq > 0) {
                                                            $display_budget = $ad->quantity * $ad->ppq;
                                                        }
                                                    @endphp
                                                    {{ amount_with_currency_symbol($display_budget) }}
                                                </td>
                                                <td>
                                                    @if($ad->status === 'active')
                                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                                    @elseif($ad->status === 'pending')
                                                        <span class="badge bg-warning">{{ __('Pending') }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ ucfirst($ad->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($ad->is_paid)
                                                        <span class="badge bg-info">{{ __('Paid') }}
                                                            ({{ $ad->gateway_slug }})</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ __('Unpaid') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="customMarkup__select__item">
                                                        <x-status.table.select-action :title="__('Select Action')" />
                                                        <ul class="dropdown-menu">
                                                            <li class="status_dropdown__item">
                                                                <a class="dropdown-item view_details_btn"
                                                                    data-id="{{ $ad->id }}" data-title="{{ $ad->title }}"
                                                                    data-company="{{ $ad->company }}"
                                                                    data-description="{{ $ad->description }}"
                                                                    data-url="{{ $ad->url }}" data-status="{{ $ad->status }}"
                                                                    data-budget="{{ $ad->quantity * $ad->ppq > 0 ? amount_with_currency_symbol($ad->quantity * $ad->ppq) : amount_with_currency_symbol($ad->budget) }}"
                                                                    data-quantity="{{ $ad->quantity }}"
                                                                    data-clicks="{{ $ad->clicks }}"
                                                                    data-impressions="{{ $ad->impressions }}"
                                                                    data-impressions="{{ $ad->impressions }}"
                                                                    data-ppq="{{ $ad->ppq }}" data-tax="{{ $ad->tax }}"
                                                                    data-user-name="{{ optional($ad->user)->first_name }} {{ optional($ad->user)->last_name }}"
                                                                    data-user-email="{{ optional($ad->user)->email }}"
                                                                    data-user-phone="{{ optional($ad->user)->phone }}"
                                                                    data-user-country="{{ data_get($ad, 'user.country.name', '') }}"
                                                                    data-image="{{ asset('assets/uploads/ads/' . $ad->cover_image) }}"
                                                                    href="#" data-bs-toggle="modal"
                                                                    data-bs-target="#adDetailsModal">{{ __('View Details / Status') }}</a>
                                                            </li>
                                                            <!-- <li class="status_dropdown__item">
                                                                                                            <a class="dropdown-item status_change_btn" data-id="{{ $ad->id }}" data-status="active" href="#">{{ __('Approve') }}</a>
                                                                                                        </li>
                                                                                                        <li class="status_dropdown__item">
                                                                                                            <a class="dropdown-item status_change_btn" data-id="{{ $ad->id }}" data-status="rejected" href="#">{{ __('Reject') }}</a>
                                                                                                        </li>
                                                                                                        <li class="status_dropdown__item">
                                                                                                            <a class="dropdown-item status_change_btn" data-id="{{ $ad->id }}" data-status="pending" href="#">{{ __('Pending') }}</a>
                                                                                                        </li> -->
                                                            <li class="status_dropdown__item">
                                                                <x-popup.delete-popup :title="__('Delete')"
                                                                    :url="route('admin.ads.delete', $ad->id)" />
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- Table End -->
                            <div class="pagination mt-4">
                                <x-pagination.laravel-paginate :allData="$all_ads" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Ad Details Modal -->
    <div class="modal fade" id="adDetailsModal" tabindex="-1" aria-labelledby="adDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="adDetailsModalLabel" style="font-weight: 600;">
                        <i class="fas fa-info-circle me-2"></i> {{ __('Ad Details & Action') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Left Column: Ad Details -->
                        <div class="col-md-7 border-end">
                            <h6 class="text-primary fw-bold mb-3"><i class="fas fa-ad me-2"></i> {{ __('Ad Information') }}
                            </h6>

                            <div class="mb-4 text-center bg-light p-2 rounded">
                                <img id="modal_ad_image" src="" alt="Ad Image" class="img-fluid rounded shadow-sm"
                                    style="max-height: 250px; object-fit: contain;">
                            </div>

                            <div class="list-group list-group-flush mb-3">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-muted">{{ __('Title') }}</span>
                                    <span id="modal_ad_title" class="fw-bold"></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-muted">{{ __('Company') }}</span>
                                    <span id="modal_ad_company"></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-muted">{{ __('Budget/Amount') }}</span>
                                    <span id="modal_ad_budget" class="text-success fw-bold"></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-muted">{{ __('Quantity') }}</span>
                                    <span id="modal_ad_quantity"></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-muted">{{ __('Status') }}</span>
                                    <span id="modal_ad_status" class="badge"></span>
                                </div>
                                <div class="list-group-item">
                                    <span class="fw-bold text-muted d-block mb-1">{{ __('URL') }}</span>
                                    <a href="#" id="modal_ad_url" target="_blank" class="text-break text-decoration-none"><i
                                            class="fas fa-external-link-alt small me-1"></i> Open Link</a>
                                </div>
                            </div>

                            <h6 class="text-muted fw-bold mt-4 mb-2">{{ __('Description') }}</h6>
                            <div class="p-3 bg-light rounded border text-secondary"
                                style="font-size: 0.95rem; max-height: 150px; overflow-y: auto;">
                                <p id="modal_ad_description" class="mb-0"></p>
                            </div>
                        </div>

                        <!-- Right Column: User Info & Action -->
                        <div class="col-md-5">
                            <!-- User Info Card -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="card-title text-primary fw-bold mb-3"><i class="fas fa-user-circle me-2"></i>
                                        {{ __('User Information') }}</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2"><i class="fas fa-user text-muted me-2" style="width: 20px;"></i>
                                            <strong class="text-dark">{{ __('Name:') }}</strong> <span id="modal_user_name"
                                                class="text-secondary"></span>
                                        </li>
                                        <li class="mb-2"><i class="fas fa-envelope text-muted me-2"
                                                style="width: 20px;"></i> <strong
                                                class="text-dark">{{ __('Email:') }}</strong> <span id="modal_user_email"
                                                class="text-secondary"></span></li>
                                        <li class="mb-2"><i class="fas fa-phone text-muted me-2" style="width: 20px;"></i>
                                            <strong class="text-dark">{{ __('Phone:') }}</strong> <span
                                                id="modal_user_phone" class="text-secondary"></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <hr class="text-muted my-4">

                            <!-- Action Section -->
                            <h6 class="text-primary fw-bold mb-3"><i class="fas fa-gavel me-2"></i> {{ __('Take Action') }}
                            </h6>
                            <form id="statusChangeForm">
                                <input type="hidden" id="modal_ad_id" name="id">
                                <div class="mb-3">
                                    <label for="feedback"
                                        class="form-label fw-bold text-muted small">{{ __('Feedback / Reason') }}</label>
                                    <textarea class="form-control shadow-sm" id="feedback" name="feedback" rows="5"
                                        placeholder="{{ __('Enter reason for approval or rejection...') }}"
                                        style="resize: none;"></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success status_submit_btn fw-bold py-2"
                                        data-status="active"><i class="fas fa-check-circle me-1"></i>
                                        {{ __('Approve Ad') }}</button>
                                    <button type="button" class="btn btn-danger status_submit_btn fw-bold py-2"
                                        data-status="rejected"><i class="fas fa-times-circle me-1"></i>
                                        {{ __('Reject Ad') }}</button>
                                    <button type="button" class="btn btn-warning text-white status_submit_btn fw-bold py-2"
                                        data-status="pending"><i class="fas fa-clock me-1"></i>
                                        {{ __('Set Pending') }}</button>
                                </div>
                                <div class="mt-4 border-top pt-3">
                                    <h6 class="text-primary fw-bold mb-3"><i class="fas fa-chart-line me-2"></i>
                                        {{ __('Manage Stats') }}</h6>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">{{ __('Quantity') }}</label>
                                            <input type="number" id="edit_quantity" name="quantity"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">{{ __('Clicks') }}</label>
                                            <input type="number" id="edit_clicks" name="clicks"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">{{ __('Impressions') }}</label>
                                            <input type="number" id="edit_impressions" name="impressions"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div class="col-12 mt-2">
                                            <button type="button" class="btn btn-primary btn-sm w-100"
                                                id="update_stats_btn">{{ __('Update Stats') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
        $(document).ready(function () {
            // Open Modal and Populate Data
            $(document).on('click', '.view_details_btn', function () {
                let btn = $(this);
                $('#modal_ad_id').val(btn.data('id'));
                $('#modal_ad_title').text(btn.data('title'));
                $('#modal_ad_company').text(btn.data('company'));
                $('#modal_ad_description').text(btn.data('description'));
                $('#modal_ad_url').attr('href', btn.data('url')).text(btn.data('url'));
                $('#modal_ad_budget').text(btn.data('budget'));
                $('#modal_ad_quantity').text(btn.data('quantity'));
                $('#modal_ad_status').text(btn.data('status'));
                $('#modal_ad_image').attr('src', btn.data('image'));

                $('#modal_user_name').text(btn.data('user-name'));
                $('#modal_user_email').text(btn.data('user-email'));
                $('#modal_user_phone').text(btn.data('user-phone'));
                $('#modal_user_country').text(btn.data('user-country'));

                // Populate Stats
                $('#edit_quantity').val(btn.data('quantity'));
                $('#edit_clicks').val(btn.data('clicks'));
                $('#edit_impressions').val(btn.data('impressions'));

                // Clear feedback box
                $('#feedback').val('');
            });

            // Submit Status Change from Modal
            $(document).on('click', '.status_submit_btn', function (e) {
                e.preventDefault();
                let ad_id = $('#modal_ad_id').val();
                let status = $(this).data('status');
                let feedback = $('#feedback').val();

                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
                    text: "{{ __("You want to change status to ") }}" + status + "?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __("Yes, do it!") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Sending email notification...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('admin.ads.change.status') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: ad_id,
                                status: status,
                                feedback: feedback
                            },
                            success: function (data) {
                                if (data.status === 'success') {
                                    Swal.fire(
                                        '{{ __("Success!") }}',
                                        data.msg,
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function (xhr) {
                                Swal.fire(
                                    '{{ __("Error!") }}',
                                    'Something went wrong. Please try again.',
                                    'error'
                                );
                            }
                        });
                    }
                })
            });

            // Existing dropdown status change handler (optional, keeping it for quick actions if needed, or removing it if duplicate)
            $(document).on('click', '.status_change_btn', function (e) {
                e.preventDefault();
                let ad_id = $(this).data('id');
                let status = $(this).data('status');
                // ... (rest of old logic can remain or be removed. I will modify to use the feedback logic if necessary, but modal is preferred now)
            });
            // Update Stats Button Click
            $(document).on('click', '#update_stats_btn', function (e) {
                e.preventDefault();
                let ad_id = $('#modal_ad_id').val();
                let quantity = $('#edit_quantity').val();
                let clicks = $('#edit_clicks').val();
                let impressions = $('#edit_impressions').val();

                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Updating ad stats...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('admin.ads.update.stats') }}", // We will create this route
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: ad_id,
                        quantity: quantity,
                        clicks: clicks,
                        impressions: impressions
                    },
                    success: function (data) {
                        if (data.status === 'success') {
                            Swal.fire(
                                '{{ __("Success!") }}',
                                data.msg,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(
                            '{{ __("Error!") }}',
                            'Something went wrong. Please try again.',
                            'error'
                        );
                    }
                });
            });
        });
    </script>
@endsection