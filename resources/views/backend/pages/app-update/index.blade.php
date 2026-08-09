@extends('backend.layout.master')
@section('title', __('App Update Manager'))

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

            

            <div class="col-lg-5">
                <div class="dashboard__card">
                    <div class="dashboard__card__header">
                        <h4 class="dashboard__card__title">{{ __('Global Settings') }}</h4>
                    </div>
                    <div class="dashboard__card__body">
                        <form action="{{ route('admin.app.update.settings') }}" method="post">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Enable App Update Checks') }}</label>
                                    <select name="app_update_enabled" class="form-control">
                                        <option value="1" {{ (string) get_static_option('app_update_enabled', '1') === '1' ? 'selected' : '' }}>{{ __('Enable') }}</option>
                                        <option value="0" {{ (string) get_static_option('app_update_enabled', '1') === '0' ? 'selected' : '' }}>{{ __('Disable') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Support Text') }}</label>
                                    <input type="text" class="form-control" name="app_update_support_text" value="{{ get_static_option('app_update_support_text') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Disabled Message') }}</label>
                                    <textarea name="app_update_disabled_message" class="form-control" rows="2">{{ get_static_option('app_update_disabled_message') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Default Update Message') }}</label>
                                    <textarea name="app_update_default_message" class="form-control" rows="2">{{ get_static_option('app_update_default_message') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Android Fallback URL') }}</label>
                                    <input type="url" class="form-control" name="app_update_android_fallback_url" value="{{ get_static_option('app_update_android_fallback_url') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('iOS Fallback URL') }}</label>
                                    <input type="url" class="form-control" name="app_update_ios_fallback_url" value="{{ get_static_option('app_update_ios_fallback_url') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Android Custom Message') }}</label>
                                    <textarea name="app_update_android_message" class="form-control" rows="2">{{ get_static_option('app_update_android_message') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('iOS Custom Message') }}</label>
                                    <textarea name="app_update_ios_message" class="form-control" rows="2">{{ get_static_option('app_update_ios_message') }}</textarea>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">{{ __('Save Settings') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="dashboard__card">
                    <div class="dashboard__card__header">
                        <h4 class="dashboard__card__title">{{ __('Create New Version') }}</h4>
                    </div>
                    <div class="dashboard__card__body">
                        <form action="{{ route('admin.app.update.store') }}" method="post">
                            @csrf
                            @include('backend.pages.app-update._form')
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">{{ __('Create Version') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="dashboard__card">
                    <div class="dashboard__card__header d-flex justify-content-between align-items-center">
                        <h4 class="dashboard__card__title mb-0">{{ __('All Versions') }}</h4>
                        <!-- <a href="{{ route('admin.app.update.installations') }}" class="btn btn-outline-primary btn-sm">{{ __('View Install Logs') }}</a> -->
                    </div>
                    <div class="dashboard__card__body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Platform') }}</th>
                                        <th>{{ __('Version') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Live') }}</th>
                                        <th>{{ __('Force') }}</th>
                                        <th>{{ __('Min Supported') }}</th>
                                        <th>{{ __('Release Date') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($versions as $item)
                                        <tr>
                                            <td>{{ ucfirst($item->platform) }}</td>
                                            <td>{{ $item->version }}</td>
                                            <td>
                                                <strong>{{ $item->version_name }}</strong>
                                                <div><small class="text-muted">{{ $item->formatted_file_size }}</small></div>
                                            </td>
                                            <td>{!! $item->is_active ? '<span class="badge bg-success">Live</span>' : '<span class="badge bg-secondary">Draft</span>' !!}</td>
                                            <td>{!! $item->force_update ? '<span class="badge bg-danger">Required</span>' : '<span class="badge bg-info text-dark">Optional</span>' !!}</td>
                                            <td>{{ $item->min_supported_version ?: '-' }}</td>
                                            <td>{{ optional($item->release_date)->format('d M, Y') }}</td>
                                            <td class="d-flex flex-wrap gap-2">
                                                <a href="{{ route('admin.app.update.edit', $item->id) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                                                @if(!$item->is_active)
                                                    <form action="{{ route('admin.app.update.activate', $item->id) }}" method="post">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">{{ __('Make Live') }}</button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('admin.app.update.destroy', $item->id) }}" method="post" onsubmit="return confirm('{{ __('Delete this version?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">{{ __('No app versions found yet.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $versions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
