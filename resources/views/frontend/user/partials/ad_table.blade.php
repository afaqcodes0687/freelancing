<style>
    .btn-profile {
        border: none;
        border-radius: 4px;
        color: #fff;
        font-size: 13px;
        padding: 4px 10px;
        cursor: pointer;
    }

    .btn-bg-1 {
        background-color: #007bff;
    }

    .btn-bg-1:hover {
        background-color: #0056b3;
    }

    .ad-table-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .ad-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
        /* Ensures the table doesn't cramp up too much */
    }

    .ad-table th,
    .ad-table td {
        padding: 12px 15px;
        text-align: left;
        vertical-align: middle;
        border-bottom: 1px solid #eee;
    }

    .ad-table th {
        font-weight: 600;
        background-color: #f8f9fa;
        white-space: nowrap;
    }

    .ad-thumbnail {
        max-height: 40px;
        width: 40px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        text-transform: capitalize;
    }

    .status-active {
        background: #e6f4ea;
        color: #1e7e34;
    }

    .status-pending {
        background: #fff4e6;
        color: #d97706;
    }

    .status-rejected {
        background: #fef2f2;
        color: #dc2626;
    }
</style>
@php($isFreelancer = $isFreelancer ?? false)
<div class="ad-table-container">
    <table class="ad-table">
        <thead>
            <tr>
                <th>{{ __('Image') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Company Name') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Quantity') }}</th>
                <th>{{ __('Payable Amount') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Clicks') }}</th>
                <th>{{ __('Impressions') }}</th>

                <th>{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ads as $ad)
                <tr>
                    <td>
                        <img src="{{ asset('assets/uploads/ads/' . $ad->cover_image) }}" alt="{{ $ad->title }}"
                            class="ad-thumbnail">
                    </td>
                    <td>{{$ad->title}}</td>
                    <td>{{$ad->company}}</td>
                    <td>{{$ad->optimize_for}}</td>
                    <td>{{$ad->quantity}}</td>
                    <td>${{$ad->quantity * $ad->ppq}}</td>
                    <td>
                        <span class="status-badge status-{{ $ad->status }}">
                            {{ $ad->status }}
                        </span>
                    </td>
                    <td>{{ $ad->clicks ?? 0 }}</td>
                    <td>{{ $ad->impressions ?? 0 }}</td>
                    <!-- Action Column -->
                    <td style="white-space: nowrap;">
                        @if(!$ad->is_paid)
                            <!-- Pay Now opens payment modal (wallet or PayPro) -->
                            <button type="button" class="btn-profile btn-bg-1 btn-sm pay-ad-btn" data-id="{{ $ad->id }}"
                                data-amount="{{ $ad->quantity * $ad->ppq }}" data-bs-toggle="modal"
                                data-bs-target="#adPaymentModal">
                                {{ __('Pay Now') }}
                            </button>
                        @endif

                        @if(!$ad->is_paid || in_array($ad->status, ['pending', 'rejected']))
                            <!-- Edit -->
                            <button type="button" class="btn-profile btn-bg-1 btn-sm editAdBtn" data-id="{{ $ad->id }}"
                                data-title="{{ $ad->title }}" data-company="{{ $ad->company }}" data-url="{{ $ad->url }}"
                                data-description="{{ $ad->description }}" data-quantity="{{ $ad->quantity }}"
                                data-optimize_for="{{ $ad->optimize_for }}"
                                data-image="{{ asset('assets/uploads/ads/' . $ad->cover_image) }}" data-bs-toggle="modal"
                                data-bs-target="#editAdModal">
                                {{ __('Edit') }}
                            </button>

                            <!-- Delete -->
                            <form method="POST" action="{{ route('ad.delete', $ad->id) }}" class="d-inline"
                                onsubmit="return confirm('{{ __("Are you sure you want to delete this ad?") }}');">
                                @csrf
                                @method('DELETE')
                                <button class="btn-profile btn-bg-1 btn-sm">{{ __('Del') }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">{{ __('No Ads Found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Ad Payment Modal (wallet + PayPro) -->
<div class="modal fade" id="adPaymentModal" tabindex="-1" aria-labelledby="adPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="adPaymentForm" method="POST"
            action="{{ $isFreelancer ? route('freelancer.ad.pay') : route('client.ad.pay') }}">
            @csrf
            <input type="hidden" name="id" id="ad_payment_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adPaymentModalLabel">{{ __('Pay For Ad') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        {{ __('Payable Amount:') }}
                        <strong id="ad_payment_amount_text"></strong>
                    </p>
                    {!! \App\Helper\PaymentGatewayList::renderPaymentGatewayForForm(false) !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn-profile btn-bg-1">{{ __('Pay Now') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Ad Modal -->
<div class="modal fade" id="editAdModal" tabindex="-1" aria-labelledby="editAdModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editAdForm" method="POST" action="{{ route('ad.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editAdId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAdModalLabel">Edit Advertisement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Error Display -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <!-- End Error Display -->
                    <div class="mb-3">
                        <label>Company</label>
                        <input type="text" class="form-control" name="company" id="editCompany">
                    </div>
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" class="form-control" name="title" id="editTitle">
                    </div>
                    <div class="mb-3">
                        <label>URL</label>
                        <input type="text" class="form-control" name="url" id="editUrl">
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <input type="text" class="form-control" name="description" id="editDescription">
                    </div>
                    <div class="mb-3">
                        <label>Optimize For</label>
                        <select name="optimize_for" id="editOptimizeFor" class="form-control">
                            <option value="click" data-price="0.02">Click</option>
                            <option value="impression" data-price="0.02">Impression</option>
                        </select>
                        <span class="form-text">
                            <b id="editPreviewPricePerQuantity">$0.02 per unit</b>
                        </span>
                    </div>
                    <div class="mb-3">
                        <label>Quantity</label>
                        <select name="quantity" id="editQuantity" class="form-control">
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="150">150</option>
                            <option value="200">200</option>
                            <option value="500">500</option>
                        </select>
                    </div>

                    <div class="d-flex flex-row-reverse mb-3">
                        <h4>
                            <b>
                                {{ __('Total:') }}
                                <span id="editTotalDisplay">$0</span>
                            </b>
                        </h4>
                    </div>
                    <!-- File input -->
                    <div class="mb-3">
                        <label>Cover Image (optional)</label>
                        <input type="file" class="form-control" name="cover_image">
                    </div>

                    <!-- Image preview area -->
                    <div class="mb-3" id="currentImageWrapper" style="display: none;">
                        <label>Current Image</label><br>
                        <img id="currentImagePreview" src="" alt="Current Image"
                            style="max-height: 100px; border: 1px solid #ddd; padding: 4px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

@section('script')
    <x-frontend.payment-gateway.gateway-select-js />
    <script>
        $(document).ready(function () {
            // Handle Pay Now button click
            $(document).on('click', '.pay-ad-btn', function () {
                let adId = $(this).data('id');
                let amount = $(this).data('amount');

                $('#ad_payment_id').val(adId);
                $('#ad_payment_amount_text').text('$' + amount);
            });

            $(document).on('click', '.editAdBtn', function () {
                $('#editAdId').val($(this).data('id'));
                $('#editTitle').val($(this).data('title'));
                $('#editCompany').val($(this).data('company'));
                $('#editUrl').val($(this).data('url'));
                $('#editDescription').val($(this).data('description'));

                let currentQty = $(this).data('quantity');
                // Ensure current quantity exists in the dropdown, otherwise add it as an option
                if ($('#editQuantity option[value="' + currentQty + '"]').length === 0) {
                    $('#editQuantity').append('<option value="' + currentQty + '">' + currentQty + '</option>');
                }
                $('#editQuantity').val(currentQty);

                $('#editOptimizeFor').val($(this).data('optimize_for'));
                let image = $(this).data('image');
                if (image) {
                    $('#currentImagePreview').attr('src', image);
                    $('#currentImageWrapper').show();
                } else {
                    $('#currentImageWrapper').hide();
                }

                updateEditTotal();
            });

            function updateEditTotal() {
                var pricePerUnit = parseFloat($('#editOptimizeFor option:selected').data('price')) || 0.02;
                var selectedAdType = $('#editOptimizeFor').val();
                var quantity = parseInt($('#editQuantity').val()) || 0;
                var totalPrice = (pricePerUnit * quantity).toFixed(2);

                $('#editTotalDisplay').text('$' + totalPrice);
                $('#editPreviewPricePerQuantity').text('$' + pricePerUnit + ' per ' + selectedAdType);
            }

            $('#editOptimizeFor, #editQuantity').on('change', updateEditTotal);
        });
    </script>
@endsection