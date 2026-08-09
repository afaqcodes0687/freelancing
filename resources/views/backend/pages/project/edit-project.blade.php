@extends('backend.layout.master')
@section('site-title')
    {{ __('Edit Project') }}
@endsection

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.project.update', $project->id) }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="title">{{ __('Project Title') }}</label>
                    <input type="text" name="title" id="project_title" class="form-control" value="{{ $project->title }}">
                </div>

                <div class="form-group mb-3 display_label_title">
                    <label for="slug">{{ __('Slug') }}</label>
                    <input type="text" name="slug" id="slug" class="form-control" value="{{ $project->slug }}">
                </div>

                <div class="mb-4">
                    <strong>{{ __('Slug:') }}</strong>
                    <span class="full-slug-show">{{ $project->slug }}</span>
                </div>

                <div class="form-group mb-3">
                    <label for="description">{{ __('Description') }}</label>
                    <textarea name="description" id="description" class="form-control"
                        rows="5">{{ old('description', strip_tags($project->description)) }}</textarea>
                </div>

                <button class="btn btn-primary" type="submit">{{ __('Update Project') }}</button>
            </form>
        </div>
    </div>

@endsection

{{-- Include JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function slugify(text) {
        return text.toString().toLowerCase()
            .trim()
            .replace(/\s+/g, '-')        
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')         
            .replace(/-+$/, '');         
    }

    $(document).ready(function () {

        $('#project_title').on('input', function () {
            const title = $(this).val();
            const slug = slugify(title);
            $('#slug').val(slug);                   
            $('.full-slug-show').text(slug);        
        });
    });
</script>