@extends('backend.layout.master')
@section('title', __($pageTitle))
@section('content')
    <div class="dashboard__body">
        <div class="row align-items-center mb-4">
            <div class="col-lg-6">
                <h3 class="dashboard__body__title">{{ __($pageTitle) }}</h3>
            </div>
            <div class="col-lg-6 text-lg-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fa fa-plus"></i> Add New</button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <x-validation.error />
                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="customMarkup__single__inner mt-4">
                            <!-- Table Start -->
                            <div class="custom_table style-04">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('Page / URL') }}</th>
                                            <th>{{ __('Meta Title') }}</th>
                                            <th>{{ __('Meta Description') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($seoSettings as $seo)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $seo->route_name }}</td>
                                                <td>{{ $seo->meta_title }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($seo->meta_description, 50) }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info editBtn" data-id="{{ $seo->id }}" data-route="{{ $seo->route_name }}" data-title="{{ $seo->meta_title }}" data-description="{{ $seo->meta_description }}" data-keywords="{{ $seo->meta_keywords }}" data-bs-toggle="modal" data-bs-target="#editModal">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('admin.seo.delete', $seo->id) }}" method="POST" class="d-inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">{{ __('No SEO Settings Found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Table End -->
                            @if ($seoSettings->hasPages())
                                <div class="pagination mt-4">
                                    {{ $seoSettings->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.seo.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add New SEO Setting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Page Name / URL Path</label>
                            <input type="text" name="route_name" class="form-control" required placeholder="e.g. /about or home">
                        </div>
                        <div class="form-group mb-3">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" placeholder="Meta Title">
                        </div>
                        <div class="form-group mb-3">
                            <label>Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3" placeholder="Meta Description"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Meta Keywords</label>
                            <textarea name="meta_keywords" class="form-control" rows="3" placeholder="Meta Keywords (comma separated)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit SEO Setting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Page Name / URL Path</label>
                            <input type="text" name="route_name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label>Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Meta Keywords</label>
                            <textarea name="meta_keywords" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    (function ($) {
        "use strict";
        $('.editBtn').on('click', function () {
            var modal = $('#editModal');
            var data = $(this).data();
            var route = '{{ route("admin.seo.update", ":id") }}';
            route = route.replace(':id', data.id);
            $('#editForm').attr('action', route);
            
            modal.find('input[name=route_name]').val(data.route);
            modal.find('input[name=meta_title]').val(data.title);
            modal.find('textarea[name=meta_description]').val(data.description);
            modal.find('textarea[name=meta_keywords]').val(data.keywords);
        });
    })(jQuery);
</script>
@endsection
