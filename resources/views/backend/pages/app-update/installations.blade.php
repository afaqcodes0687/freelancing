@extends('backend.layout.master')
@section('title', __('App Installation Logs'))

@section('content')
<div class="dashboard__body">
    <div class="container-fluid p-0">
        <div class="row g-4">
            <div class="col-12">
                <div class="dashboard__card">
                    <div class="dashboard__card__header d-flex justify-content-between align-items-center">
                        <h4 class="dashboard__card__title mb-0">{{ __('Installation Logs') }}</h4>
                        <a href="{{ route('admin.app.update.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('Back to App Updates') }}</a>
                    </div>
                    <div class="dashboard__card__body">
                        <form method="get" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <select name="platform" class="form-control">
                                    <option value="">{{ __('All Platforms') }}</option>
                                    @foreach($platforms as $platformKey => $platformLabel)
                                        <option value="{{ $platformKey }}" {{ request('platform') === $platformKey ? 'selected' : '' }}>{{ __($platformLabel) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="version" class="form-control">
                                    <option value="">{{ __('All Versions') }}</option>
                                    @foreach($versionOptions as $version)
                                        <option value="{{ $version }}" {{ request('version') === $version ? 'selected' : '' }}>{{ $version }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="device_id" class="form-control" value="{{ request('device_id') }}" placeholder="{{ __('Search device id') }}">
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('User') }}</th>
                                        <th>{{ __('Platform') }}</th>
                                        <th>{{ __('Version') }}</th>
                                        <th>{{ __('Previous') }}</th>
                                        <th>{{ __('Device ID') }}</th>
                                        <th>{{ __('IP') }}</th>
                                        <th>{{ __('Installed At') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($installations as $installation)
                                        <tr>
                                            <td>
                                                @if($installation->user)
                                                    {{ $installation->user->first_name }} {{ $installation->user->last_name }}
                                                    <div><small class="text-muted">{{ $installation->user->email }}</small></div>
                                                @else
                                                    <span class="text-muted">{{ __('Guest / Unknown') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ ucfirst($installation->platform) }}</td>
                                            <td>{{ $installation->version }}</td>
                                            <td>{{ $installation->previous_version ?: '-' }}</td>
                                            <td><small>{{ $installation->device_id }}</small></td>
                                            <td>{{ $installation->ip_address ?: '-' }}</td>
                                            <td>{{ optional($installation->installed_at)->format('d M, Y h:i A') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">{{ __('No installation records found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $installations->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
