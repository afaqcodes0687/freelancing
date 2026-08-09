@extends('backend.layout.master')

@section('title', __('Affiliate Settings'))

@section('style')
    <x-media.css/>
@endsection

@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <h4 class="customMarkup__single__title">{{ __('Affiliate Settings') }}</h4>
                        <x-validation.error/>
                        <div class="customMarkup__single__inner mt-4">
                            <form action="{{ route('admin.general.settings.affiliate') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="single-input mb-3">
                                    <label for="affiliate_first_purchase_percent" class="label-title mb-1">{{__('First Transaction Commission (%)')}}</label>
                                    <input type="number" step="0.0001" min="0" max="100" id="affiliate_first_purchase_percent" name="affiliate_first_purchase_percent" class="form-control" value="{{ get_static_option('affiliate_first_purchase_percent') ?? 0.05 }}">
                                    <small class="form-text text-muted">{{ __('Commission percent for the first payment made by referred user.') }}</small>
                                </div>

                                <div class="single-input mb-3">
                                    <label for="affiliate_recurring_percent" class="label-title mb-1">{{__('Recurring Commission (%)')}}</label>
                                    <input type="number" step="0.0001" min="0" max="100" id="affiliate_recurring_percent" name="affiliate_recurring_percent" class="form-control" value="{{ get_static_option('affiliate_recurring_percent') ?? 0.025 }}">
                                    <small class="form-text text-muted">{{ __('Commission percent for subsequent payments within 12 months.') }}</small>
                                </div>

                                <div class="single-input mb-3">
                                    <label for="affiliate_min_payout" class="label-title mb-1">{{__('Minimum Payout Threshold ($)')}}</label>
                                    <input type="number" step="0.01" min="1" id="affiliate_min_payout" name="affiliate_min_payout" class="form-control" value="{{ get_static_option('affiliate_min_payout') ?? 100 }}">
                                    <small class="form-text text-muted">{{ __('Minimum amount required in available balance before affiliates can request a payout.') }}</small>
                                </div>

                                <button type="submit" id="update" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Changes')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection

@section('script')
    <x-media.js/>
    <script>
        (function($){
            "use strict";
            $(document).ready(function () {
                <x-btn.update/>
            });
        })(jQuery);
    </script>
@endsection
