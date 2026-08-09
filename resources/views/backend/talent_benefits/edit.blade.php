@extends('backend.layout.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Talent Benefits Page</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.talent.benefits.update') }}">
                            @csrf

                            <!-- SEO & Meta Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-search"></i> SEO & Meta Information</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Page Title</label>
                                        <input type="text" name="title" class="form-control"
                                            value="{{ $benefit->title ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Title</label>
                                        <input type="text" name="meta_title" class="form-control"
                                            value="{{ $benefit->meta_title ?? '' }}"
                                            placeholder="SEO Title (60 characters max)">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea name="meta_description" class="form-control" rows="2"
                                            placeholder="SEO Description (160 characters max)">{{ $benefit->meta_description ?? '' }}</textarea>
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
                                            value="{{ $benefit->heading ?? '' }}" placeholder="Main heading for the page">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Short Description</label>
                                        <textarea name="short_description" class="form-control" rows="3"
                                            placeholder="Brief description below the main heading">{{ $benefit->short_description ?? '' }}</textarea>
                                    </div>
                                </div>

                            </div>

                            <!-- Structured Benefits -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-star"></i> Freelancer Advantages</h5>
                                    <div id="benefits-container">
                                        @if($benefit->benefits && is_array($benefit->benefits))
                                            @foreach($benefit->benefits as $index => $benefit_item)
                                                <div class="benefit-item row mb-3" data-index="{{ $index }}">
                                                    <div class="col-md-3">
                                                        <input type="text" name="benefits[{{ $index }}][title]" class="form-control"
                                                            placeholder="Benefit Title" value="{{ $benefit_item['title'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" name="benefits[{{ $index }}][description]"
                                                            class="form-control" placeholder="Benefit Description"
                                                            value="{{ $benefit_item['description'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" name="benefits[{{ $index }}][icon]" class="form-control"
                                                            placeholder="Icon (fa-star)" value="{{ $benefit_item['icon'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-center">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm remove-benefit">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm" id="add-benefit">Add
                                        Benefit</button>
                                </div>
                            </div>

                            <!-- FAQ Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-question-circle"></i> Frequently Asked Questions</h5>

                                    <h6 class="mt-4 mb-3">Structured FAQs (Recommended)</h6>
                                    <div id="faqs-container">
                                        @if($benefit->faqs && is_array($benefit->faqs))
                                            @foreach($benefit->faqs as $index => $faq)
                                                <div class="faq-item row mb-3" data-index="{{ $index }}">
                                                    <div class="col-md-5">
                                                        <input type="text" name="faqs[{{ $index }}][question]" class="form-control"
                                                            placeholder="Question" value="{{ $faq['question'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <textarea name="faqs[{{ $index }}][answer]" class="form-control" rows="2"
                                                            placeholder="Answer">{{ $faq['answer'] ?? '' }}</textarea>
                                                    </div>
                                                    <div class="col-md-2">
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
                                        <i class="fas fa-save"></i> Update Talent Benefits
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
    <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <script>
        // Initialize CKEditor
        CKEDITOR.replace('content', {
            height: 400,
            removeButtons: 'Subscript,Superscript'
        });

        CKEDITOR.replace('faq_content', {
            height: 300,
            removeButtons: 'Subscript,Superscript'
        });

        // Benefits Management
        let benefitIndex = {{ count($benefit->benefits ?? []) }};

        document.getElementById('add-benefit').addEventListener('click', function () {
            const container = document.getElementById('benefits-container');
            const newBenefit = document.createElement('div');
            newBenefit.className = 'benefit-item row mb-3';
            newBenefit.dataset.index = benefitIndex;
            newBenefit.innerHTML = `
                <div class="col-md-3">
                    <input type="text" name="benefits[${benefitIndex}][title]" class="form-control" placeholder="Benefit Title">
                </div>
                <div class="col-md-3">
                    <input type="text" name="benefits[${benefitIndex}][description]" class="form-control" placeholder="Benefit Description">
                </div>
                <div class="col-md-4">
                    <input type="text" name="benefits[${benefitIndex}][icon]" class="form-control" placeholder="Icon (fa-star)">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <button type="button" class="btn btn-danger btn-sm remove-benefit">Remove</button>
                </div>
            `;
            container.appendChild(newBenefit);
            benefitIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-benefit')) {
                e.target.closest('.benefit-item').remove();
            }
        });

        // FAQs Management
        let faqIndex = {{ count($benefit->faqs ?? []) }};

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
                <div class="col-md-2">
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