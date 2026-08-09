@extends('backend.layout.master')
@section('page_title', __('How It Works Page'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item active">{{ __('How It Works') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('How It Works Page Settings') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.how-it-works.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic SEO Settings -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">{{ __('Basic SEO Settings') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">{{ __('Page Title') }}</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ $howItWorks->title ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="banner_title">{{ __('Banner Title') }}</label>
                                <input type="text" class="form-control" id="banner_title" name="banner_title" value="{{ $howItWorks->banner_title ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="meta_title">{{ __('Meta Title') }}</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ $howItWorks->meta_title ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="meta_description">{{ __('Meta Description') }}</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ $howItWorks->meta_description ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="meta_keywords">{{ __('Meta Keywords') }}</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ $howItWorks->meta_keywords ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <!-- Hiring Tab Content -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">{{ __('Hiring Tab Content') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hiring_content_title">{{ __('Content Title') }}</label>
                                <input type="text" class="form-control" id="hiring_content_title" name="hiring_content_title" value="{{ $howItWorks->hiring_content_title ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hiring_side_image">{{ __('Side Image URL') }}</label>
                                <input type="text" class="form-control" id="hiring_side_image" name="hiring_side_image" value="{{ $howItWorks->hiring_side_image ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="hiring_content_subtitle">{{ __('Content Subtitle') }}</label>
                                <input type="text" class="form-control" id="hiring_content_subtitle" name="hiring_content_subtitle" value="{{ $howItWorks->hiring_content_subtitle ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="hiring_main_content">{{ __('Main Content') }}</label>
                                <textarea class="form-control" id="hiring_main_content" name="hiring_main_content" rows="4">{{ $howItWorks->hiring_main_content ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('Hiring FAQs') }}</label>
                            <div id="hiring_faqs_container">
                                @if($howItWorks->hiring_faqs)
                                    @foreach($howItWorks->hiring_faqs as $index => $faq)
                                        <div class="faq-item mb-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" name="hiring_faqs[{{ $index }}][question]" placeholder="{{ __('Question') }}" value="{{ $faq['question'] ?? '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="hiring_faqs[{{ $index }}][answer]" placeholder="{{ __('Answer') }}" value="{{ $faq['answer'] ?? '' }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-faq">×</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" id="add_hiring_faq">{{ __('Add FAQ') }}</button>
                        </div>
                    </div>

                    <!-- Hiring Progress Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">{{ __('Hiring Progress Section') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hiring_progress_title">{{ __('Progress Title') }}</label>
                                <input type="text" class="form-control" id="hiring_progress_title" name="hiring_progress_title" value="{{ $howItWorks->hiring_progress_title ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hiring_progress_image">{{ __('Progress Image URL') }}</label>
                                <input type="text" class="form-control" id="hiring_progress_image" name="hiring_progress_image" value="{{ $howItWorks->hiring_progress_image ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="hiring_progress_subtitle">{{ __('Progress Subtitle') }}</label>
                                <input type="text" class="form-control" id="hiring_progress_subtitle" name="hiring_progress_subtitle" value="{{ $howItWorks->hiring_progress_subtitle ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="hiring_progress_content">{{ __('Progress Content') }}</label>
                                <textarea class="form-control" id="hiring_progress_content" name="hiring_progress_content" rows="4">{{ $howItWorks->hiring_progress_content ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('Progress FAQs') }}</label>
                            <div id="hiring_progress_faqs_container">
                                @if($howItWorks->hiring_progress_faqs)
                                    @foreach($howItWorks->hiring_progress_faqs as $index => $faq)
                                        <div class="faq-item mb-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" name="hiring_progress_faqs[{{ $index }}][question]" placeholder="{{ __('Question') }}" value="{{ $faq['question'] ?? '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="hiring_progress_faqs[{{ $index }}][answer]" placeholder="{{ __('Answer') }}" value="{{ $faq['answer'] ?? '' }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-faq">×</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" id="add_hiring_progress_faq">{{ __('Add FAQ') }}</button>
                        </div>
                    </div>

                    <!-- Hiring Payment Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">{{ __('Hiring Payment Section') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hiring_payment_title">{{ __('Payment Title') }}</label>
                                <input type="text" class="form-control" id="hiring_payment_title" name="hiring_payment_title" value="{{ $howItWorks->hiring_payment_title ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hiring_payment_image">{{ __('Payment Image URL') }}</label>
                                <input type="text" class="form-control" id="hiring_payment_image" name="hiring_payment_image" value="{{ $howItWorks->hiring_payment_image ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="hiring_payment_subtitle">{{ __('Payment Subtitle') }}</label>
                                <input type="text" class="form-control" id="hiring_payment_subtitle" name="hiring_payment_subtitle" value="{{ $howItWorks->hiring_payment_subtitle ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="hiring_payment_content">{{ __('Payment Content') }}</label>
                                <textarea class="form-control" id="hiring_payment_content" name="hiring_payment_content" rows="4">{{ $howItWorks->hiring_payment_content ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('Payment FAQs') }}</label>
                            <div id="hiring_payment_faqs_container">
                                @if($howItWorks->hiring_payment_faqs)
                                    @foreach($howItWorks->hiring_payment_faqs as $index => $faq)
                                        <div class="faq-item mb-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" name="hiring_payment_faqs[{{ $index }}][question]" placeholder="{{ __('Question') }}" value="{{ $faq['question'] ?? '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="hiring_payment_faqs[{{ $index }}][answer]" placeholder="{{ __('Answer') }}" value="{{ $faq['answer'] ?? '' }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-faq">×</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" id="add_hiring_payment_faq">{{ __('Add FAQ') }}</button>
                        </div>
                    </div>

                    <!-- Talents Tab Content -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">{{ __('Talents Tab Content') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="talents_content_title">{{ __('Content Title') }}</label>
                                <input type="text" class="form-control" id="talents_content_title" name="talents_content_title" value="{{ $howItWorks->talents_content_title ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="talents_side_image">{{ __('Side Image URL') }}</label>
                                <input type="text" class="form-control" id="talents_side_image" name="talents_side_image" value="{{ $howItWorks->talents_side_image ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="talents_content_subtitle">{{ __('Content Subtitle') }}</label>
                                <input type="text" class="form-control" id="talents_content_subtitle" name="talents_content_subtitle" value="{{ $howItWorks->talents_content_subtitle ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="talents_main_content">{{ __('Main Content') }}</label>
                                <textarea class="form-control" id="talents_main_content" name="talents_main_content" rows="4">{{ $howItWorks->talents_main_content ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('Talents FAQs') }}</label>
                            <div id="talents_faqs_container">
                                @if($howItWorks->talents_faqs)
                                    @foreach($howItWorks->talents_faqs as $index => $faq)
                                        <div class="faq-item mb-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" name="talents_faqs[{{ $index }}][question]" placeholder="{{ __('Question') }}" value="{{ $faq['question'] ?? '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="talents_faqs[{{ $index }}][answer]" placeholder="{{ __('Answer') }}" value="{{ $faq['answer'] ?? '' }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-faq">×</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" id="add_talents_faq">{{ __('Add FAQ') }}</button>
                        </div>
                    </div>

                    <!-- Talents Payment Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">{{ __('Talents Payment Section') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="talents_payment_title">{{ __('Payment Title') }}</label>
                                <input type="text" class="form-control" id="talents_payment_title" name="talents_payment_title" value="{{ $howItWorks->talents_payment_title ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="talents_payment_image">{{ __('Payment Image URL') }}</label>
                                <input type="text" class="form-control" id="talents_payment_image" name="talents_payment_image" value="{{ $howItWorks->talents_payment_image ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="talents_payment_subtitle">{{ __('Payment Subtitle') }}</label>
                                <input type="text" class="form-control" id="talents_payment_subtitle" name="talents_payment_subtitle" value="{{ $howItWorks->talents_payment_subtitle ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="talents_payment_content">{{ __('Payment Content') }}</label>
                                <textarea class="form-control" id="talents_payment_content" name="talents_payment_content" rows="4">{{ $howItWorks->talents_payment_content ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('Talents Payment FAQs') }}</label>
                            <div id="talents_payment_faqs_container">
                                @if($howItWorks->talents_payment_faqs)
                                    @foreach($howItWorks->talents_payment_faqs as $index => $faq)
                                        <div class="faq-item mb-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" name="talents_payment_faqs[{{ $index }}][question]" placeholder="{{ __('Question') }}" value="{{ $faq['question'] ?? '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="talents_payment_faqs[{{ $index }}][answer]" placeholder="{{ __('Answer') }}" value="{{ $faq['answer'] ?? '' }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-faq">×</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" id="add_talents_payment_faq">{{ __('Add FAQ') }}</button>
                        </div>
                    </div>

                    <!-- FAQ Tab Content -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">{{ __('FAQ Tab Content') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="faq_content_title">{{ __('Content Title') }}</label>
                                <input type="text" class="form-control" id="faq_content_title" name="faq_content_title" value="{{ $howItWorks->faq_content_title ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="faq_side_image">{{ __('Side Image URL') }}</label>
                                <input type="text" class="form-control" id="faq_side_image" name="faq_side_image" value="{{ $howItWorks->faq_side_image ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="faq_content_subtitle">{{ __('Content Subtitle') }}</label>
                                <input type="text" class="form-control" id="faq_content_subtitle" name="faq_content_subtitle" value="{{ $howItWorks->faq_content_subtitle ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="faq_main_content">{{ __('Main Content') }}</label>
                                <textarea class="form-control" id="faq_main_content" name="faq_main_content" rows="4">{{ $howItWorks->faq_main_content ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('FAQ Tab FAQs') }}</label>
                            <div id="faq_faqs_container">
                                @if($howItWorks->faq_faqs)
                                    @foreach($howItWorks->faq_faqs as $index => $faq)
                                        <div class="faq-item mb-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" name="faq_faqs[{{ $index }}][question]" placeholder="{{ __('Question') }}" value="{{ $faq['question'] ?? '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="faq_faqs[{{ $index }}][answer]" placeholder="{{ __('Answer') }}" value="{{ $faq['answer'] ?? '' }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-faq">×</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" id="add_faq_faq">{{ __('Add FAQ') }}</button>
                        </div>
                    </div>

                    <!-- Projects Tab Content -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">{{ __('Projects Tab Content') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="projects_content_title">{{ __('Content Title') }}</label>
                                <input type="text" class="form-control" id="projects_content_title" name="projects_content_title" value="{{ $howItWorks->projects_content_title ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="projects_side_image">{{ __('Side Image URL') }}</label>
                                <input type="text" class="form-control" id="projects_side_image" name="projects_side_image" value="{{ $howItWorks->projects_side_image ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="projects_content_subtitle">{{ __('Content Subtitle') }}</label>
                                <input type="text" class="form-control" id="projects_content_subtitle" name="projects_content_subtitle" value="{{ $howItWorks->projects_content_subtitle ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="projects_main_content">{{ __('Main Content') }}</label>
                                <textarea class="form-control" id="projects_main_content" name="projects_main_content" rows="4">{{ $howItWorks->projects_main_content ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>{{ __('Projects FAQs') }}</label>
                            <div id="projects_faqs_container">
                                @if($howItWorks->projects_faqs)
                                    @foreach($howItWorks->projects_faqs as $index => $faq)
                                        <div class="faq-item mb-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control" name="projects_faqs[{{ $index }}][question]" placeholder="{{ __('Question') }}" value="{{ $faq['question'] ?? '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="projects_faqs[{{ $index }}][answer]" placeholder="{{ __('Answer') }}" value="{{ $faq['answer'] ?? '' }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm remove-faq">×</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" id="add_projects_faq">{{ __('Add FAQ') }}</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ __('Update Page') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Management
    const faqContainers = [
        'hiring_faqs_container',
        'hiring_progress_faqs_container', 
        'hiring_payment_faqs_container',
        'talents_faqs_container',
        'talents_payment_faqs_container',
        'faq_faqs_container',
        'projects_faqs_container'
    ];

    const faqButtons = [
        'add_hiring_faq',
        'add_hiring_progress_faq',
        'add_hiring_payment_faq',
        'add_talents_faq',
        'add_talents_payment_faq',
        'add_faq_faq',
        'add_projects_faq'
    ];

    faqButtons.forEach((buttonId, index) => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.addEventListener('click', function() {
                const container = document.getElementById(faqContainers[index]);
                const faqCount = container.querySelectorAll('.faq-item').length;
                const newFaq = document.createElement('div');
                newFaq.className = 'faq-item mb-3';
                newFaq.innerHTML = `
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="${faqContainers[index].replace('_container', '')}[${faqCount}][question]" placeholder="{{ __('Question') }}">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="${faqContainers[index].replace('_container', '')}[${faqCount}][answer]" placeholder="{{ __('Answer') }}">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-faq">×</button>
                        </div>
                    </div>
                `;
                container.appendChild(newFaq);
            });
        }
    });

    // Remove FAQ functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-faq')) {
            e.target.closest('.faq-item').remove();
        }
    });
});
</script>
@endsection
