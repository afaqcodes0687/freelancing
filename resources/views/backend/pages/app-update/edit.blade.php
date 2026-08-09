@extends('backend.layout.master')
@section('title', __('Edit App Version'))

@section('content')
<div class="dashboard__body">
    <div class="container-fluid p-0">
        <div class="row g-4">
            @if($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="col-lg-10">
                <div class="dashboard__card">
                    <div class="dashboard__card__header d-flex justify-content-between align-items-center">
                        <h4 class="dashboard__card__title mb-0">{{ __('Edit Version') }}: {{ $version->version }}</h4>
                        <a href="{{ route('admin.app.update.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Back') }}</a>
                    </div>
                    <div class="dashboard__card__body">
                        <form action="{{ route('admin.app.update.update', $version->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            @include('backend.pages.app-update._form', ['version' => $version])
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">{{ __('Update Version') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
