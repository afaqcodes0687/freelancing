@extends('backend.layout.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Investor Relations Page</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.investor.relation.page.update') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

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

                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3"><i class="fas fa-file-alt"></i> Page Content</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Content (WYSIWYG)</label>
                                        <textarea name="content" id="content1" class="form-control"
                                            rows="10">{{ $policy->content ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

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
        CKEDITOR.replace('content1', {
            height: 400,
            removeButtons: 'Subscript,Superscript'
        });
    </script>
@endsection