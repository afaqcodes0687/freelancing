@extends('backend.layout.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Partnership Page</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.partnership.page.update') }}"
                            enctype="multipart/form-data">
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

                            <!-- Banner & Escrow Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-file-alt"></i> Banner / Intro Section</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Banner Title</label>
                                        <input type="text" name="escrow_title" class="form-control"
                                            value="{{ $policy->escrow_title ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Banner Description</label>
                                        <textarea name="escrow_description" class="form-control"
                                            rows="3">{{ $policy->escrow_description ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Banner Image Path</label>
                                        <input type="text" name="escrow_image" class="form-control"
                                            value="{{ $policy->escrow_image ?? '' }}"
                                            placeholder="assets/uploads/partnerimage/...png">
                                    </div>
                                </div>
                            </div>

                            <!-- Why Partner Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-question-circle"></i> Why Partner with Right
                                        Freelancer?</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" name="why_partner_title" class="form-control"
                                            value="{{ $policy->why_partner_title ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Content (WYSIWYG)</label>
                                        <textarea name="why_partner_description" id="content1" class="form-control"
                                            rows="10">{{ $policy->why_partner_description ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Expand Talent Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-users"></i> Expand Your Talent Pool</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" name="expand_talent_title" class="form-control"
                                            value="{{ $policy->expand_talent_title ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Content (WYSIWYG)</label>
                                        <textarea name="expand_talent_description" id="content2" class="form-control"
                                            rows="10">{{ $policy->expand_talent_description ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Image Path</label>
                                        <input type="text" name="expand_talent_image" class="form-control"
                                            value="{{ $policy->expand_talent_image ?? '' }}"
                                            placeholder="assets/uploads/partnerimage/...png">
                                    </div>
                                </div>
                            </div>

                            <!-- Foster Innovation Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-lightbulb"></i> Foster Innovation & Growth</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" name="foster_innovation_title" class="form-control"
                                            value="{{ $policy->foster_innovation_title ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Content (WYSIWYG)</label>
                                        <textarea name="foster_innovation_description" id="content3" class="form-control"
                                            rows="10">{{ $policy->foster_innovation_description ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Image Path</label>
                                        <input type="text" name="foster_innovation_image" class="form-control"
                                            value="{{ $policy->foster_innovation_image ?? '' }}"
                                            placeholder="assets/uploads/partnerimage/...png">
                                    </div>
                                </div>
                            </div>

                            <!-- Market Presence Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-chart-line"></i> Strengthen Your Market Presence</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" name="market_presence_title" class="form-control"
                                            value="{{ $policy->market_presence_title ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Content (WYSIWYG)</label>
                                        <textarea name="market_presence_description" id="content4" class="form-control"
                                            rows="10">{{ $policy->market_presence_description ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Image Path</label>
                                        <input type="text" name="market_presence_image" class="form-control"
                                            value="{{ $policy->market_presence_image ?? '' }}"
                                            placeholder="assets/uploads/partnerimage/...png">
                                    </div>
                                </div>
                            </div>

                            <!-- Economic Empowerment -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-globe"></i> Contribute to Economic Empowerment</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="economic_empowerment_description" class="form-control"
                                            rows="4">{{ $policy->economic_empowerment_description ?? '' }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label">Image Path</label>
                                            <input type="text" name="economic_empowerment_image" class="form-control"
                                                value="{{ $policy->economic_empowerment_image ?? '' }}"
                                                placeholder="assets/uploads/partnerimage/...png">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Partnership Opportunities -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-handshake"></i> Partnership Opportunities</h5>
                                    <div id="opportunities-container">
                                        @if($policy->opportunities && is_array($policy->opportunities))
                                            @foreach($policy->opportunities as $index => $opt)
                                                <div class="opportunity-item row mb-3" data-index="{{ $index }}">
                                                    <div class="col-md-3">
                                                        <input type="text" name="opportunities[{{ $index }}][title]"
                                                            class="form-control" placeholder="Title"
                                                            value="{{ $opt['title'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <textarea name="opportunities[{{ $index }}][description]"
                                                            class="form-control" rows="2"
                                                            placeholder="Description">{{ $opt['description'] ?? '' }}</textarea>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" name="opportunities[{{ $index }}][icon]"
                                                            class="form-control" placeholder="Icon Image Path"
                                                            value="{{ $opt['icon'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-1 d-flex align-items-center">
                                                        <button type="button" class="btn btn-danger btn-sm remove-opt">X</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm" id="add-opt">Add
                                        Opportunity</button>
                                </div>
                            </div>

                            <!-- Partnership Process -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-list-ol"></i> Our Partnership Process</h5>
                                    <div id="process-container">
                                        @if($policy->process && is_array($policy->process))
                                            @foreach($policy->process as $index => $proc)
                                                <div class="process-item row mb-3" data-index="{{ $index }}">
                                                    <div class="col-md-5">
                                                        <input type="text" name="process[{{ $index }}][title]" class="form-control"
                                                            placeholder="Step Title" value="{{ $proc['title'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <textarea name="process[{{ $index }}][description]" class="form-control"
                                                            rows="2"
                                                            placeholder="Description">{{ $proc['description'] ?? '' }}</textarea>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-center">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm remove-proc">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm" id="add-proc">Add Process
                                        Step</button>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-phone"></i> Contact Information</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Contact Email</label>
                                        <input type="text" name="contact_email" class="form-control"
                                            value="{{ $policy->contact_email ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Contact Phone</label>
                                        <input type="text" name="contact_phone" class="form-control"
                                            value="{{ $policy->contact_phone ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Partnership Page
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
        // Initialize CKEditor
        ['content1', 'content2', 'content3', 'content4'].forEach(function (id) {
            CKEDITOR.replace(id, {
                height: 300,
                removeButtons: 'Subscript,Superscript'
            });
        });

        // Opportunities Management
        let optIndex = {{ count($policy->opportunities ?? []) }};
        document.getElementById('add-opt').addEventListener('click', function () {
            const container = document.getElementById('opportunities-container');
            const newOpt = document.createElement('div');
            newOpt.className = 'opportunity-item row mb-3';
            newOpt.innerHTML = `
                        <div class="col-md-3">
                            <input type="text" name="opportunities[${optIndex}][title]" class="form-control" placeholder="Title">
                        </div>
                        <div class="col-md-5">
                            <textarea name="opportunities[${optIndex}][description]" class="form-control" rows="2" placeholder="Description"></textarea>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="opportunities[${optIndex}][icon]" class="form-control" placeholder="Icon Image Path">
                        </div>
                        <div class="col-md-1 d-flex align-items-center">
                            <button type="button" class="btn btn-danger btn-sm remove-opt">X</button>
                        </div>
                    `;
            container.appendChild(newOpt);
            optIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-opt')) {
                e.target.closest('.opportunity-item').remove();
            }
        });

        // Process Management
        let procIndex = {{ count($policy->process ?? []) }};
        document.getElementById('add-proc').addEventListener('click', function () {
            const container = document.getElementById('process-container');
            const newProc = document.createElement('div');
            newProc.className = 'process-item row mb-3';
            newProc.innerHTML = `
                        <div class="col-md-5">
                            <input type="text" name="process[${procIndex}][title]" class="form-control" placeholder="Step Title">
                        </div>
                        <div class="col-md-5">
                            <textarea name="process[${procIndex}][description]" class="form-control" rows="2" placeholder="Description"></textarea>
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <button type="button" class="btn btn-danger btn-sm remove-proc">Remove</button>
                        </div>
                    `;
            container.appendChild(newProc);
            procIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-proc')) {
                e.target.closest('.process-item').remove();
            }
        });
    </script>
@endsection