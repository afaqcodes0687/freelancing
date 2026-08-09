@extends('backend.layout.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Trust and Safety Page</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.trust-safety.page.update') }}"
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
                                            value="{{ $trustSafety->title ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Title</label>
                                        <input type="text" name="meta_title" class="form-control"
                                            value="{{ $trustSafety->meta_title ?? '' }}"
                                            placeholder="SEO Title (60 characters max)">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea name="meta_description" class="form-control" rows="2"
                                            placeholder="SEO Description (160 characters max)">{{ $trustSafety->meta_description ?? '' }}</textarea>
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
                                            value="{{ $trustSafety->banner_title ?? '' }}">
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
                                            value="{{ $trustSafety->content_title ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Introduction -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-info-circle"></i> Introduction</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Introduction Content</label>
                                        <textarea name="introduction" id="introduction" class="form-control"
                                            rows="5">{{ $trustSafety->introduction ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Top Rated Program -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-star"></i> Top Rated Program</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Top Rated Program Content</label>
                                        <textarea name="top_rated_program" id="top_rated_program" class="form-control"
                                            rows="4">{{ $trustSafety->top_rated_program ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Communication Importance -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-comments"></i> Communication Importance</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Communication Content</label>
                                        <textarea name="communication_importance" id="communication_importance" class="form-control"
                                            rows="4">{{ $trustSafety->communication_importance ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Escrow System -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-shield-alt"></i> Escrow System</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Escrow System Content</label>
                                        <textarea name="escrow_system" id="escrow_system" class="form-control"
                                            rows="4">{{ $trustSafety->escrow_system ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Support -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-headset"></i> Customer Support</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Customer Support Content</label>
                                        <textarea name="customer_support" id="customer_support" class="form-control"
                                            rows="4">{{ $trustSafety->customer_support ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Dispute Resolution -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-gavel"></i> Dispute Resolution</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Dispute Resolution Content</label>
                                        <textarea name="dispute_resolution" id="dispute_resolution" class="form-control"
                                            rows="4">{{ $trustSafety->dispute_resolution ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Freelancer Profiles -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-user-tie"></i> Freelancer Profiles</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Freelancer Profiles Content</label>
                                        <textarea name="freelancer_profiles" id="freelancer_profiles" class="form-control"
                                            rows="5">{{ $trustSafety->freelancer_profiles ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Guidelines -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-clipboard-list"></i> Project Guidelines</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Project Guidelines Content</label>
                                        <textarea name="project_guidelines" id="project_guidelines" class="form-control"
                                            rows="5">{{ $trustSafety->project_guidelines ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Scam Protection -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-exclamation-triangle"></i> Scam Protection</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Scam Protection Title</label>
                                        <input type="text" name="scam_protection_title" class="form-control"
                                            value="{{ $trustSafety->scam_protection_title ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Scam Protection Points</label>
                                        <div id="scam-protection-points">
                                            @if($trustSafety && $trustSafety->scam_protection_points)
                                                @foreach($trustSafety->scam_protection_points as $index => $point)
                                                    <div class="input-group mb-2">
                                                        <input type="text" name="scam_protection_points[]" 
                                                               class="form-control" value="{{ $point }}">
                                                        <button type="button" class="btn btn-outline-danger remove-point">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-primary" id="add-scam-point" style="background-color: #309400; border-color: #309400;">
                                            <i class="fas fa-plus"></i> Add Point
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-envelope"></i> Contact Information</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Contact Information</label>
                                        <textarea name="contact_info" id="contact_info" class="form-control"
                                            rows="3">{{ $trustSafety->contact_info ?? '' }}</textarea>
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
        // Initialize CKEditor for all textareas
        ['introduction', 'top_rated_program', 'communication_importance', 'escrow_system', 'customer_support', 'dispute_resolution', 'freelancer_profiles', 'project_guidelines', 'contact_info'].forEach(function (id) {
            CKEDITOR.replace(id, {
                height: 300,
                removeButtons: 'Subscript,Superscript'
            });
        });

        // Add new scam protection point
        $(document).on('click', '#add-scam-point', function() {
            var newPoint = $('<div class="input-group mb-2">' +
                '<input type="text" name="scam_protection_points[]" class="form-control" placeholder="Enter scam protection point...">' +
                '<button type="button" class="btn btn-outline-danger remove-point">' +
                '<i class="fas fa-trash"></i>' +
                '</button>' +
                '</div>');
            $('#scam-protection-points').append(newPoint);
        });

        // Remove scam protection point
        $(document).on('click', '.remove-point', function() {
            $(this).closest('.input-group').remove();
        });
    </script>
@endsection
