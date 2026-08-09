@php($version = $version ?? null)
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">{{ __('Platform') }}</label>
        <select name="platform" class="form-control" required>
            @foreach($platforms as $platformKey => $platformLabel)
                <option value="{{ $platformKey }}" {{ old('platform', $version->platform ?? 'android') === $platformKey ? 'selected' : '' }}>
                    {{ __($platformLabel) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('Version') }}</label>
        <input type="text" class="form-control" name="version" value="{{ old('version', $version->version ?? '') }}" placeholder="1.2.0" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('Version Name') }}</label>
        <input type="text" class="form-control" name="version_name" value="{{ old('version_name', $version->version_name ?? '') }}" placeholder="{{ __('RightFreelancer v1.2.0') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('Download URL') }}</label>
        <input type="url" class="form-control" name="download_url" value="{{ old('download_url', $version->download_url ?? '') }}" placeholder="https://...">
        <small class="text-muted">{{ __('Leave empty to use the platform fallback URL from settings.') }}</small>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('File Size (bytes)') }}</label>
        <input type="number" class="form-control" min="0" name="file_size" value="{{ old('file_size', $version->file_size ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('Release Date') }}</label>
        <input type="date" class="form-control" name="release_date" value="{{ old('release_date', isset($version) && $version->release_date ? $version->release_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('Minimum Supported Version') }}</label>
        <input type="text" class="form-control" name="min_supported_version" value="{{ old('min_supported_version', $version->min_supported_version ?? '') }}" placeholder="1.0.0">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('Checksum') }}</label>
        <input type="text" class="form-control" name="checksum" value="{{ old('checksum', $version->checksum ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('Signature') }}</label>
        <input type="text" class="form-control" name="signature" value="{{ old('signature', $version->signature ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('Make Live') }}</label>
        <select name="is_active" class="form-control">
            <option value="1" {{ (string) old('is_active', isset($version) ? (int) $version->is_active : 1) === '1' ? 'selected' : '' }}>{{ __('Yes') }}</option>
            <option value="0" {{ (string) old('is_active', isset($version) ? (int) $version->is_active : 1) === '0' ? 'selected' : '' }}>{{ __('No') }}</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('Force Update') }}</label>
        <select name="force_update" class="form-control">
            <option value="0" {{ (string) old('force_update', isset($version) ? (int) $version->force_update : 0) === '0' ? 'selected' : '' }}>{{ __('No') }}</option>
            <option value="1" {{ (string) old('force_update', isset($version) ? (int) $version->force_update : 0) === '1' ? 'selected' : '' }}>{{ __('Yes') }}</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('Release Notes') }}</label>
        <textarea name="release_notes" rows="8" class="form-control" placeholder="{{ __('Write one note per line') }}">{{ old('release_notes', $version->release_notes_text ?? '') }}</textarea>
    </div>
</div>
