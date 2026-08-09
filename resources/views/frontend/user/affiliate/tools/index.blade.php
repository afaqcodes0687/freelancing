@extends('frontend.layout.master')
@section('site_title', __('Affiliate Tools'))

@section('style')
    <style>
        .affiliate-tool-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .affiliate-tool-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .code-block-wrapper {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 0;
            position: relative;
            overflow: hidden;
        }
        .code-block-header {
            background: #0f172a;
            padding: 8px 12px;
            border-bottom: 1px solid #334155;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .code-block-label {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
        }
        .code-block {
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 12px;
            color: #e2e8f0;
            word-break: break-all;
            white-space: pre-wrap;
            margin: 0;
            padding: 12px;
            max-height: 120px;
            overflow-y: auto;
            background: #1e293b;
            line-height: 1.6;
        }
        .code-block::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .code-block::-webkit-scrollbar-track {
            background: #0f172a;
        }
        .code-block::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 3px;
        }
        .copy-btn-code {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 4px;
        }
        .banner-preview-box {
            background: #f1f5f9;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 160px;
            margin-bottom: 15px;
            padding: 15px;
        }
        .referral-link-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 15px;
            border-radius: 8px;
        }
        .qr-wrapper img {
            max-width: 150px;
            height: auto;
            border: 4px solid #fff;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .section-separator {
            border-top: 1px solid #f1f5f9;
            margin: 40px 0;
        }
    </style>
@endsection

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('Affiliate Tools')" :innerTitle="__('Affiliate Tools')" />

        <div class="responsive-overlay"></div>
        <div class="profile-settings-area pat-100 pab-100 section-bg-2">
            <div class="container">
                <div class="row g-4">
                    @include('frontend.user.layout.partials.sidebar')

                    <div class="col-xl-9 col-lg-8">
                        <div class="profile-settings-wrapper p-4 affiliate-tool-card">
                            
                            <!-- Referral Link Section -->
                            <div class="referral-section mb-5">
                                <div class="mb-4">
                                    <h4 class="mb-1">{{ __('Referral Program') }}</h4>
                                    <p class="text-muted small">{{ __('Share your unique link and earn commissions on every successful referral.') }}</p>
                                </div>

                                <div class="row g-4 align-items-center">
                                    <div class="col-md-8">
                                        <div class="referral-link-box">
                                            <label class="form-label fw-bold text-dark small mb-2 text-uppercase">{{ __('Your Unique Referral Link') }}</label>
                                            <div class="input-group">
                                                <input id="referral_link" class="form-control" readonly value="{{ $referralLink }}" style="background: #fff; font-weight: 500;">
                                                <button id="copy_referral" type="button" class="btn btn-primary px-4">
                                                    <i class="fas fa-copy me-2"></i>{{ __('Copy') }}
                                                </button>
                                            </div>
                                            <p class="mt-2 mb-0 small text-info">
                                                <i class="fas fa-info-circle me-1"></i> {{ __('Cookies are valid for 30 days.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="qr-wrapper">
                                            <img src="{{ $qrSrc }}" alt="QR" class="rounded">
                                            <div class="mt-3">
                                                <a class="btn btn-sm btn-outline-dark rounded-pill px-3" href="{{ $qrSrc }}" target="_blank">
                                                    <i class="fas fa-qrcode me-1"></i> {{ __('Download QR') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-separator"></div>

                            <!-- Marketing Banners -->
                            <div class="banners-section">
                                <div class="mb-4">
                                    <h4 class="mb-1">{{ __('Marketing Creatives') }}</h4>
                                    <p class="text-muted small">{{ __('Use these professional banners on your website or blog to drive traffic.') }}</p>
                                </div>

                                <div class="row g-4">
                                    @foreach($banners as $index => $banner)
                                        <div class="col-md-6">
                                            <div class="card h-100 border-0 shadow-sm rounded-4" style="background: #fff;">
                                                <div class="card-header bg-white border-0 py-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-0 text-dark">{{ $banner['title'] }}</h6>
                                                            <span class="badge bg-light text-muted fw-normal" style="font-size: 10px;">{{ $banner['width'] }}x{{ $banner['height'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body p-3">
                                                    <!-- Banner Preview -->
                                                    <div class="banner-preview-box mb-3">
                                                        <img src="{{ $banner['image'] }}" style="max-width: 100%; height: auto; max-height: 120px;" alt="Banner Preview">
                                                    </div>

                                                    <!-- Instructions -->
                                                    <div class="alert alert-info py-2 px-3 mb-3" style="font-size: 12px; border-left: 3px solid #0ea5e9;">
                                                        <i class="fas fa-lightbulb me-1"></i>
                                                        <strong>How to use:</strong> Copy the HTML code below and paste it into your website, blog, or social media bio to promote with your referral link.
                                                    </div>

                                                    <!-- Copy Button -->
                                                    <div class="d-grid">
                                                        <button type="button" class="btn btn-primary" onclick="copyBannerCode('banner_code_{{ $index }}', this)">
                                                            <i class="fas fa-code me-2"></i> Copy HTML Code
                                                        </button>
                                                    </div>

                                                    <!-- Hidden code -->
                                                    <input type="hidden" id="banner_code_{{ $index }}_raw" value="{{ htmlspecialchars($banner['html']) }}">
                                                    
                                                    <!-- Collapsible Code Preview -->
                                                    <div class="mt-2">
                                                        <a class="text-muted small" data-bs-toggle="collapse" href="#code_preview_{{ $index }}" role="button" aria-expanded="false">
                                                            <i class="fas fa-eye me-1"></i> Preview code
                                                        </a>
                                                        <div class="collapse mt-2" id="code_preview_{{ $index }}">
                                                            <div class="code-preview-box p-2 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0; font-family: monospace; font-size: 11px; word-break: break-all; color: #475569;">
                                                                {{ $banner['html'] }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        function copyText(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-primary', 'btn-outline-primary');
                
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                }, 2000);
            });
        }

        function copyBannerCode(id, btn) {
            const code = document.getElementById(id + '_raw').value;
            navigator.clipboard.writeText(code).then(() => {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check me-2"></i> Copied to Clipboard!';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-primary');
                
                toastr.success('HTML code copied! Now paste it into your website editor.', 'Success!');
                
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                }, 2500);
            });
        }

        document.getElementById('copy_referral')?.addEventListener('click', function() {
            copyText(document.getElementById('referral_link').value, this);
            toastr.success('Referral link copied to clipboard!', 'Success!');
        });
    </script>
@endsection