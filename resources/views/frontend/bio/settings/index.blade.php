@extends('frontend.layout.master')

@section('title', 'Bio Settings')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Bio Page Settings</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('freelancer.bio.settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Bio Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input @error('bio_enabled') is-invalid @enderror" 
                                       type="checkbox" id="bio_enabled" name="bio_enabled" value="1"
                                       {{ old('bio_enabled', $user->bio_enabled) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bio_enabled">
                                    <strong>Enable Bio Page</strong>
                                    <br>
                                    <small class="text-muted">
                                        When enabled, your bio page will be publicly accessible at 
                                        <a href="{{ $user->bio_url }}" target="_blank">{{ $user->bio_url }}</a>
                                    </small>
                                </label>
                            </div>
                            @error('bio_enabled')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Avatar -->
                        <div class="mb-4">
                            <label for="bio_avatar" class="form-label">Profile Avatar</label>
                            <div class="d-flex align-items-center mb-3">
                                @if($user->bio_avatar)
                                    <img src="{{ $user->bio_avatar }}" alt="Current Avatar" 
                                         class="rounded-circle me-3" width="80" height="80">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" 
                                         style="width: 80px; height: 80px;">
                                        <i class="fas fa-user fa-2x text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <input type="file" class="form-control @error('bio_avatar') is-invalid @enderror" 
                                           id="bio_avatar" name="bio_avatar" accept="image/*">
                                    @error('bio_avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Recommended: Square image, at least 200x200px
                                    </small>
                                </div>
                            </div>
                            @if($user->bio_avatar)
                                <button type="button" class="btn btn-sm btn-outline-danger" id="removeAvatar">
                                    <i class="fas fa-trash"></i> Remove Avatar
                                </button>
                            @endif
                        </div>

                        <!-- Bio Description -->
                        <div class="mb-4">
                            <label for="bio_description" class="form-label">Bio Description</label>
                            <textarea class="form-control @error('bio_description') is-invalid @enderror" 
                                      id="bio_description" name="bio_description" rows="4"
                                      placeholder="Tell people about yourself, your services, or what you do..."
                                      maxlength="500">{{ old('bio_description', $user->bio_description) }}</textarea>
                            <div class="form-text">
                                <span id="charCount">{{ strlen(old('bio_description', $user->bio_description ?? '')) }}</span>/500 characters
                            </div>
                            @error('bio_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Theme -->
                        <div class="mb-4">
                            <label class="form-label">Theme</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card theme-option @error('bio_theme') is-invalid @enderror" data-theme="default">
                                        <div class="card-body text-center">
                                            <div class="theme-preview default-theme mb-2"></div>
                                            <h6>Default</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="bio_theme" 
                                                       value="default" id="theme_default"
                                                       {{ old('bio_theme', $user->bio_theme) == 'default' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="theme_default"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card theme-option @error('bio_theme') is-invalid @enderror" data-theme="dark">
                                        <div class="card-body text-center">
                                            <div class="theme-preview dark-theme mb-2"></div>
                                            <h6>Dark</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="bio_theme" 
                                                       value="dark" id="theme_dark"
                                                       {{ old('bio_theme', $user->bio_theme) == 'dark' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="theme_dark"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card theme-option @error('bio_theme') is-invalid @enderror" data-theme="minimal">
                                        <div class="card-body text-center">
                                            <div class="theme-preview minimal-theme mb-2"></div>
                                            <h6>Minimal</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="bio_theme" 
                                                       value="minimal" id="theme_minimal"
                                                       {{ old('bio_theme', $user->bio_theme) == 'minimal' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="theme_minimal"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card theme-option @error('bio_theme') is-invalid @enderror" data-theme="colorful">
                                        <div class="card-body text-center">
                                            <div class="theme-preview colorful-theme mb-2"></div>
                                            <h6>Colorful</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="bio_theme" 
                                                       value="colorful" id="theme_colorful"
                                                       {{ old('bio_theme', $user->bio_theme) == 'colorful' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="theme_colorful"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('bio_theme')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bio URL -->
                        <div class="mb-4">
                            <label class="form-label">Your Bio Page URL</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ url('/u/') }}</span>
                                <input type="text" class="form-control" value="{{ $user->username }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" id="copyBioUrl">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                            <small class="form-text text-muted">
                                Share this URL with your audience to showcase all your links in one place
                            </small>
                        </div>

                        <!-- QR Code -->
                        <div class="mb-4">
                            <label class="form-label">QR Code</label>
                            <div class="d-flex align-items-center">
                                <img src="{{ route('bio.qr.download', [$user->username, 'png']) }}" 
                                     alt="QR Code" class="rounded me-3" width="100" height="100">
                                <div>
                                    <p class="mb-2">Download your QR code to share your bio page offline:</p>
                                    <div class="btn-group">
                                        <a href="{{ route('bio.qr.download', [$user->username, 'png']) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-download"></i> PNG
                                        </a>
                                        <a href="{{ route('bio.qr.download', [$user->username, 'svg']) }}" 
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-download"></i> SVG
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Button -->
                        <div class="mb-4">
                            <button type="button" class="btn btn-outline-info" id="previewBio">
                                <i class="fas fa-eye"></i> Preview Bio Page
                            </button>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('freelancer.bio.links.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Links
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Stats Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Bio Page Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="text-primary">{{ $user->bio_views ?? 0 }}</h3>
                        <p class="text-muted">Total Page Views</p>
                    </div>
                    <div class="text-center mb-3">
                        <h3 class="text-success">{{ $user->total_link_clicks ?? 0 }}</h3>
                        <p class="text-muted">Total Link Clicks</p>
                    </div>
                    <div class="text-center mb-3">
                        <h3 class="text-info">{{ $user->activeBioLinks()->count() }}</h3>
                        <p class="text-muted">Active Links</p>
                    </div>
                    <div class="text-center">
                        <h3 class="text-warning">{{ $user->featuredBioLinks()->count() }}</h3>
                        <p class="text-muted">Featured Links</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ $user->bio_url }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt"></i> View Bio Page
                        </a>
                        <a href="{{ route('freelancer.bio.links.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> Add New Link
                        </a>
                        <button type="button" class="btn btn-outline-info" id="shareBio">
                            <i class="fas fa-share-alt"></i> Share Bio Page
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bio Page Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share Your Bio Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Bio Page URL</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ $user->bio_url }}" id="shareUrl" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="copyShareUrl">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($user->bio_url) }}&text=Check%20out%20my%20bio%20page!" 
                       target="_blank" class="btn btn-info">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($user->bio_url) }}" 
                       target="_blank" class="btn btn-primary">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($user->bio_url) }}" 
                       target="_blank" class="btn btn-secondary">
                        <i class="fab fa-linkedin"></i> LinkedIn
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.theme-option {
    cursor: pointer;
    transition: all 0.3s ease;
}

.theme-option:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.theme-option.selected {
    border: 2px solid #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.theme-preview {
    width: 60px;
    height: 40px;
    border-radius: 5px;
    margin: 0 auto;
}

.default-theme {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.dark-theme {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
}

.minimal-theme {
    background: #ffffff;
    border: 1px solid #e0e0e0;
}

.colorful-theme {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

#previewContent {
    height: 600px;
    overflow-y: auto;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter
    const bioDescription = document.getElementById('bio_description');
    const charCount = document.getElementById('charCount');
    
    bioDescription.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Theme selection
    const themeOptions = document.querySelectorAll('.theme-option');
    
    themeOptions.forEach(option => {
        option.addEventListener('click', function() {
            const theme = this.dataset.theme;
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Update selected state
            themeOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // Set initial selected state
    const selectedTheme = document.querySelector('input[name="bio_theme"]:checked');
    if (selectedTheme) {
        selectedTheme.closest('.theme-option').classList.add('selected');
    }

    // Copy bio URL
    document.getElementById('copyBioUrl').addEventListener('click', function() {
        const url = '{{ $user->bio_url }}';
        navigator.clipboard.writeText(url).then(() => {
            this.innerHTML = '<i class="fas fa-check"></i> Copied!';
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-copy"></i> Copy';
            }, 2000);
        });
    });

    // Remove avatar
    document.getElementById('removeAvatar').addEventListener('click', function() {
        if (confirm('Are you sure you want to remove your avatar?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('freelancer.bio.settings.remove-avatar') }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    });

    // Preview bio page
    document.getElementById('previewBio').addEventListener('click', function() {
        fetch('{{ route('freelancer.bio.settings.preview') }}')
            .then(response => response.json())
            .then(data => {
                // This would render the preview - for now just show the URL
                const previewContent = document.getElementById('previewContent');
                previewContent.innerHTML = `
                    <div class="text-center">
                        <h4>Preview Mode</h4>
                        <p>Your bio page preview will appear here.</p>
                        <a href="${data.bio_url}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt"></i> View Live Page
                        </a>
                    </div>
                `;
            });
        
        new bootstrap.Modal(document.getElementById('previewModal')).show();
    });

    // Share bio page
    document.getElementById('shareBio').addEventListener('click', function() {
        new bootstrap.Modal(document.getElementById('shareModal')).show();
    });

    // Copy share URL
    document.getElementById('copyShareUrl').addEventListener('click', function() {
        const url = document.getElementById('shareUrl').value;
        navigator.clipboard.writeText(url).then(() => {
            this.innerHTML = '<i class="fas fa-check"></i> Copied!';
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-copy"></i> Copy';
            }, 2000);
        });
    });
});
</script>
@endsection
