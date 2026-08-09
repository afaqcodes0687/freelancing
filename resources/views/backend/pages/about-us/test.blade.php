@extends('backend.layout.master')
@section('site_title', __('Test Update'))

@section('content')
    <div class="dashboard__content">
        <div class="dashboard__content__header">
            <h4 class="dashboard__content__title">{{ __('Test Update') }}</h4>
        </div>

        <div class="dashboard__card">
            <div class="dashboard__card__body">
                <form action="{{ route('admin.about-us.update', $aboutUs->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="ceo_name">{{ __('CEO Name') }}</label>
                        <input type="text" class="form-control" id="ceo_name" name="ceo_name" 
                               value="{{ old('ceo_name', $aboutUs->ceo_name) }}">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Test Update') }}
                        </button>
                        <a href="{{ route('admin.about-us.index') }}" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
