@extends('backend.layout.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Support Page</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.support.page.update') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- SEO & Meta Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-search"></i> SEO & Meta Information</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Page Title</label>
                                        <input type="text" name="title" class="form-control"
                                            value="{{ $support->title ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Title</label>
                                        <input type="text" name="meta_title" class="form-control"
                                            value="{{ $support->meta_title ?? '' }}"
                                            placeholder="SEO Title (60 characters max)">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea name="meta_description" class="form-control" rows="2"
                                            placeholder="SEO Description (160 characters max)">{{ $support->meta_description ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Banner Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-file-alt"></i> Banner Section</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Banner Title</label>
                                        <input type="text" name="banner_title" class="form-control"
                                            value="{{ $support->banner_title ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Content Title -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-heading"></i> Content Title</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Main Content Title</label>
                                        <input type="text" name="content_title" class="form-control"
                                            value="{{ $support->content_title ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Main Content -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-info-circle"></i> Main Content</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Main Content</label>
                                        <textarea name="main_content" id="main_content" class="form-control"
                                            rows="8">{{ $support->main_content ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-question-circle"></i> FAQ Section</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">FAQ Title</label>
                                        <input type="text" name="faq_title" class="form-control"
                                            value="{{ $support->faq_title ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">FAQ Items</label>
                                        <div id="faq-container">
                                            @if($support && $support->faqs)
                                                @foreach($support->faqs as $index => $faq)
                                                    <div class="faq-item row mb-3 border p-3" data-index="{{ $index }}">
                                                        <div class="col-md-5">
                                                            <input type="text" name="faqs[{{ $index }}][question]" 
                                                                   class="form-control" placeholder="Question"
                                                                   value="{{ $faq['question'] ?? '' }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <textarea name="faqs[{{ $index }}][answer]" 
                                                                      class="form-control" rows="3" placeholder="Answer">{{ $faq['answer'] ?? '' }}</textarea>
                                                        </div>
                                                        <div class="col-md-1 d-flex align-items-center">
                                                            <button type="button" class="btn btn-danger btn-sm remove-faq">X</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-secondary btn-sm" id="add-faq">
                                            <i class="fas fa-plus"></i> Add FAQ
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Side Image -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-image"></i> Side Image</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Side Image Path</label>
                                        <input type="text" name="side_image" class="form-control"
                                            value="{{ $support->side_image ?? '' }}"
                                            placeholder="assets/frontend/img/static/your-image.jpg">
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Page
                                    </button>
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
        // Initialize CKEditor for main content
        CKEDITOR.replace('main_content', {
            height: 400,
            removeButtons: 'Subscript,Superscript'
        });

        // FAQ Management
        let faqIndex = {{ count($support->faqs ?? []) }};
        document.getElementById('add-faq').addEventListener('click', function () {
            const container = document.getElementById('faq-container');
            const newFaq = document.createElement('div');
            newFaq.className = 'faq-item row mb-3 border p-3';
            newFaq.innerHTML = `
                        <div class="col-md-5">
                            <input type="text" name="faqs[${faqIndex}][question]" class="form-control" placeholder="Question">
                        </div>
                        <div class="col-md-6">
                            <textarea name="faqs[${faqIndex}][answer]" class="form-control" rows="3" placeholder="Answer"></textarea>
                        </div>
                        <div class="col-md-1 d-flex align-items-center">
                            <button type="button" class="btn btn-danger btn-sm remove-faq">X</button>
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
