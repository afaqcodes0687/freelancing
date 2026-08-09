<!-- Upload Gallery Start -->
<style>
    .file-name-box {
        background-color: #f9f9f9;
        padding: 10px 15px;
        border-radius: 6px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex-direction: column;
    }

    .file-name-box i {
        font-size: 40px;
        color: #2b7a78;
    }

    .file-name-text {
        font-size: 14px;
        font-weight: 500;
        color: #333;
        word-break: break-all;
        text-align: center;
    }

    .project_photo_preview {
        max-height: 200px;
        border-radius: 6px;
    }

    .file-preview-wrapper {
        position: relative;
        display: inline-block;
        margin: 5px;
    }

    .remove-existing-file {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #e74c3c;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        text-align: center;
        font-size: 14px;
        cursor: pointer;
        line-height: 20px;
    }

    .file-preview-wrapper:hover .remove-existing-file {
        opacity: 1;
    }
</style>

<div class="setup-wrapper-contents">
    <div class="create-project-wrapper-item">
        <div class="create-project-wrapper-item-top profile-border-bottom">
            <h4 class="create-project-wrapper-title">{{ __('Upload Gallery') }}</h4>
        </div>

        <!-- ✅ PREVIEW AREA OUTSIDE upload box -->
        <div class="file-preview-area mb-3">
            @php
                $filePaths = json_decode($project_details->image, true);
            @endphp

            @if(!empty($filePaths) && is_array($filePaths))
                @foreach($filePaths as $index => $file)
                    @php
                        $storageDrivers = ['s3', 'cloudFlareR2', 'wasabi'];
                        $isCloud = cloudStorageExist() && in_array(Storage::getDefaultDriver(), $storageDrivers);
                        $filePath = $isCloud
                            ? render_frontend_cloud_image_if_module_exists('project/'.$file, load_from: $project_details->load_from)
                            : asset('assets/uploads/project/'.$file);
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    @endphp

                    <div class="file-preview-wrapper" data-index="{{ $index }}">
                        <input type="hidden" name="existing_images[]" value="{{ $file }}">
                        <button type="button" class="remove-existing-file">&times;</button>

                        @if(in_array($ext, ['jpg','jpeg','png','svg','bmp','webp','tiff']))
                            <img src="{{ $filePath }}" class="project_photo_preview" alt="Image">
                        @elseif(in_array($ext, ['mp4','mov','avi','mkv','wmv']))
                            <video controls class="project_photo_preview" style="max-height: 200px;">
                                <source src="{{ $filePath }}" type="video/{{ $ext }}">
                            </video>
                        @elseif(in_array($ext, ['pdf','doc','docx','xls','xlsx','csv','txt']))
                            <div class="file-name-box">
                                <i class="fa fa-file"></i>
                                <span class="file-name-text">{{ basename($file) }}</span>
                            </div>
                        @else
                            <p class="text-muted">{{ basename($file) }} — {{ __('No preview available') }}</p>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        <!-- ✅ CLICKABLE UPLOAD BOX -->
        <div class="create-project-wrapper-upload">
            <div class="create-project-wrapper-upload-browse center-text radius-10" style="cursor: pointer;">
                <span class="create-project-wrapper-upload-browse-icon mt-3">
                    <i class="fa-solid fa-image"></i>
                </span>
                <span class="create-project-wrapper-upload-browse-para">
                    {{ __('Drag and drop or Click to browse file') }}
                </span>
                @php $existing_images = json_decode($project_details->image ?? '[]', true); @endphp
                <input class="upload-gallery" type="file" name="image[]" id="upload_project_photo"
                       accept=".jpg,.jpeg,.png,.svg,.webp,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.mp4,.avi,.mov,.wmv,.mkv" multiple data-existing-count="{{ is_array($existing_images) ? count($existing_images) : 0 }}">
            </div>
            <p class="mt-3">
                <strong>{{ __('info:') }}</strong> {{ __('recommended dimensions 1770x960 pixels. Drag and drop single file only') }}
            </p>
        </div>
    </div>
</div>

<!-- Upload Gallery Ends -->
<script>
    const removedFiles = [];

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-existing-file')) {
            const wrapper = e.target.closest('.file-preview-wrapper');
            if (wrapper) {
                const input = wrapper.querySelector('input[name="existing_images[]"]');
                if (input) {
                    removedFiles.push(input.value); // Store the filename
                    document.getElementById('removed_files_input').value = JSON.stringify(removedFiles);
                }
                wrapper.remove(); // Remove UI preview
            }
        }
    });
</script>
