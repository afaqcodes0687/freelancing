@extends('backend.layout.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Terms of Service Page</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.terms.of.service.update') }}">
                            @csrf

                            <!-- SEO & Meta Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-search"></i> SEO &amp; Meta Information</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Page Title</label>
                                        <input type="text" name="title" class="form-control"
                                            value="{{ $policy->title ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Title</label>
                                        <input type="text" name="meta_title" class="form-control"
                                            value="{{ $policy->meta_title ?? '' }}"
                                            placeholder="SEO Title (60 characters max)">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea name="meta_description" class="form-control" rows="2"
                                            placeholder="SEO Description (160 characters max)">{{ $policy->meta_description ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Page Content -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-file-alt"></i> Page Content</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Main Heading</label>
                                        <input type="text" name="heading" class="form-control"
                                            value="{{ $policy->heading ?? '' }}" placeholder="Main heading for the page">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Short Description</label>
                                        <textarea name="short_description" class="form-control" rows="3"
                                            placeholder="Brief description below the main heading">{{ $policy->short_description ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Main Content (WYSIWYG)</label>
                                        <textarea name="content" id="content" class="form-control"
                                            rows="10">{{ $policy->content ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-question-circle"></i> Structured FAQs</h5>
                                    <div id="faqs-container">
                                        @if($policy->faqs && is_array($policy->faqs))
                                            @foreach($policy->faqs as $index => $faq)
                                                <div class="faq-item row mb-3" data-index="{{ $index }}">
                                                    <div class="col-md-5">
                                                        <input type="text" name="faqs[{{ $index }}][question]" class="form-control"
                                                            placeholder="Question" value="{{ $faq['question'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <textarea name="faqs[{{ $index }}][answer]" class="form-control" rows="2"
                                                            placeholder="Answer">{{ $faq['answer'] ?? '' }}</textarea>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-center">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm remove-faq">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm" id="add-faq">Add FAQ</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Terms of Service
                                    </button>
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        // Initialize CKEditor
        CKEDITOR.replace('content', {
            height: 400,
            removeButtons: 'Subscript,Superscript'
        });

        // FAQs Management
        let faqIndex = {{ count($policy->faqs ?? []) }};

        document.getElementById('add-faq').addEventListener('click', function () {
            const container = document.getElementById('faqs-container');
            const newFaq = document.createElement('div');
            newFaq.className = 'faq-item row mb-3';
            newFaq.dataset.index = faqIndex;
            newFaq.innerHTML = `
                                    <div class="col-md-5">
                                        <input type="text" name="faqs[${faqIndex}][question]" class="form-control" placeholder="Question">
                                    </div>
                                    <div class="col-md-5">
                                        <textarea name="faqs[${faqIndex}][answer]" class="form-control" rows="2" placeholder="Answer"></textarea>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-faq">Remove</button>
                                    </div>
                                `;
            container.appendChild(newFaq);
            faqIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-faq')) {
                e.target.closest('.faq-item').remove();
            }
        });
    </script>
@endsection