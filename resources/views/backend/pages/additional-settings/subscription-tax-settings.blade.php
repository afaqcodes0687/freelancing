@extends('backend.layout.master')
@section('site-title') {{ __('Subscription Tax Settings') }} @endsection
@section('content')
<div class="dashboard__body">
    <div class="container-fluid p-0">
        <div class="dashboard__inner">
            <div class="row g-4">
                <div class="col-12">
                    <div class="dashboard__card">
                        <div class="dashboard__card__header">
                            <h4 class="dashboard__card__title">{{ __('Subscription Tax Settings') }}</h4>
                        </div>
                        <div class="dashboard__card__body">
                            <form action="{{ route('admin.subscription.tax.settings') }}" method="post">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Tax Rate (%)') }}</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" class="form-control" name="subscription_tax_rate" value="{{ old('subscription_tax_rate', get_static_option('subscription_tax_rate') ?? env('SUBSCRIPTION_TAX_RATE', 0)) }}" />
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('subscription_tax_rate') <span class="text-danger">{{ $message }}</span> @enderror
                                        <p class="text-muted mt-2" style="font-size: 12px;">{{ __('This rate is applied to all subscription packages. Set 0 to disable tax.') }}</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button class="btn btn-primary" type="submit">{{ __('Save Changes') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
