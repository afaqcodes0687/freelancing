@extends('frontend.layout.master')

@section('title', 'Edit Bio Link')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Link</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('freelancer.bio.links.update', $bioLink) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $bioLink->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- URL -->
                        <div class="mb-3">
                            <label for="url" class="form-label">URL <span class="text-danger">*</span></label>
                            <input type="url" class="form-control @error('url') is-invalid @enderror" 
                                   id="url" name="url" value="{{ old('url', $bioLink->url) }}" required>
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Link Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="social" {{ old('type', $bioLink->type) == 'social' ? 'selected' : '' }}>Social Media</option>
                                <option value="affiliate" {{ old('type', $bioLink->type) == 'affiliate' ? 'selected' : '' }}>Affiliate</option>
                                <option value="service" {{ old('type', $bioLink->type) == 'service' ? 'selected' : '' }}>Service</option>
                                <option value="external" {{ old('type', $bioLink->type) == 'external' ? 'selected' : '' }}>External</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Icon -->
                        <div class="mb-3">
                            <label for="icon" class="form-label">Icon (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i id="iconPreview" class="{{ $bioLink->icon ?: 'fas fa-link' }}"></i>
                                </span>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" name="icon" value="{{ old('icon', $bioLink->icon) }}" 
                                       placeholder="e.g., fab fa-twitter, fas fa-globe">
                            </div>
                            <small class="form-text text-muted">
                                Use <a href="https://fontawesome.com/icons" target="_blank">Font Awesome</a> icon classes
                            </small>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Color -->
                        <div class="mb-3">
                            <label for="color" class="form-label">Color (Optional)</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color" name="color" value="{{ old('color', $bioLink->color ?? '#667eea') }}">
                                <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                       id="colorText" name="colorText" value="{{ old('color', $bioLink->color ?? '#667eea') }}" 
                                       placeholder="#667eea">
                            </div>
                            <small class="form-text text-muted">
                                Choose a custom color for your link button
                            </small>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('is_active') is-invalid @enderror" 
                                       type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $bioLink->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active</strong>
                                    <br>
                                    <small class="text-muted">Inactive links will not be displayed on your bio page</small>
                                </label>
                            </div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Featured (for affiliate links) -->
                        <div class="mb-3" id="featuredField" {{ old('type', $bioLink->type) !== 'affiliate' ? 'style="display: none;"' : '' }}>
                            <div class="form-check">
                                <input class="form-check-input @error('is_featured') is-invalid @enderror" 
                                       type="checkbox" id="is_featured" name="is_featured" value="1"
                                       {{ old('is_featured', $bioLink->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    <strong>Featured Link</strong>
                                    <br>
                                    <small class="text-muted">Featured links will be highlighted and shown first</small>
                                </label>
                            </div>
                            @error('is_featured')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Link Statistics -->
                        <div class="card bg-light mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Link Statistics</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <strong>{{ $bioLink->clicks->count() }}</strong>
                                        <br>
                                        <small class="text-muted">Total Clicks</small>
                                    </div>
                                    <div class="col-4">
                                        <strong>{{ $bioLink->clicks()->today()->count() }}</strong>
                                        <br>
                                        <small class="text-muted">Today</small>
                                    </div>
                                    <div class="col-4">
                                        <strong>{{ $bioLink->clicks()->where('created_at', '>=', now()->subDays(7))->count() }}</strong>
                                        <br>
                                        <small class="text-muted">Last 7 Days</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Link Type Help -->
                        <div class="alert alert-info">
                            <h6>Link Types:</h6>
                            <ul class="mb-0">
                                <li><strong>Social Media:</strong> Social profiles (Twitter, Instagram, LinkedIn, etc.)</li>
                                <li><strong>Affiliate:</strong> Links with referral tracking (automatically adds your referral code)</li>
                                <li><strong>Service:</strong> Links to your services or products</li>
                                <li><strong>External:</strong> Any other external website</li>
                            </ul>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('freelancer.bio.links.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Links
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Link
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const featuredField = document.getElementById('featuredField');
    const iconInput = document.getElementById('icon');
    const iconPreview = document.getElementById('iconPreview');
    const colorInput = document.getElementById('color');
    const colorText = document.getElementById('colorText');

    // Show/hide featured option based on link type
    typeSelect.addEventListener('change', function() {
        if (this.value === 'affiliate') {
            featuredField.style.display = 'block';
        } else {
            featuredField.style.display = 'none';
            document.getElementById('is_featured').checked = false;
        }
    });

    // Update icon preview
    iconInput.addEventListener('input', function() {
        const iconClass = this.value.trim();
        if (iconClass) {
            iconPreview.className = iconClass;
        } else {
            iconPreview.className = 'fas fa-link';
        }
    });

    // Sync color inputs
    colorInput.addEventListener('input', function() {
        colorText.value = this.value;
    });

    colorText.addEventListener('input', function() {
        if (/^#[0-9A-F]{6}$/i.test(this.value)) {
            colorInput.value = this.value;
        }
    });
});
</script>
@endsection
