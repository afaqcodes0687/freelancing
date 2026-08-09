@extends('backend.layout.master')
@section('site-title') {{ __('Signup Bonus Settings') }} @endsection
@section('content')
<div class="dashboard__body">
    <div class="container-fluid p-0">
        <div class="dashboard__inner">
            <div class="row g-4">
                <div class="col-12">
                    <div class="dashboard__card">
                        <div class="dashboard__card__header">
                            <h4 class="dashboard__card__title">{{ __('Signup Bonus Settings') }}</h4>
                        </div>
                        <div class="dashboard__card__body">
                            <form action="{{ route('admin.signup.bonus.settings') }}" method="post">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Enable Signup Bonus') }}</label>
                                        <select name="signup_bonus_enable" class="form-control">
                                            @php $enabled = (string) (get_static_option('signup_bonus_enable') ?? '1'); @endphp
                                            <option value="1" {{ $enabled === '1' ? 'selected' : '' }}>{{ __('Enable') }}</option>
                                            <option value="0" {{ $enabled === '0' ? 'selected' : '' }}>{{ __('Disable') }}</option>
                                        </select>
                                        @error('signup_bonus_enable') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Signup Bonus Amount') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ site_currency_symbol() }}</span>
                                            <input type="number" step="0.01" min="0" class="form-control" name="signup_bonus_amount" value="{{ old('signup_bonus_amount', get_static_option('signup_bonus_amount') ?? 10) }}" />
                                        </div>
                                        @error('signup_bonus_amount') <span class="text-danger">{{ $message }}</span> @enderror
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
