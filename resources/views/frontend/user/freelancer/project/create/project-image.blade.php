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
    }
    .file-name-box i {
        font-size: 20px;
        color: #2b7a78;
    }
    .file-name-text {
        font-size: 14px;
    }


    .file-preview-area .preview-wrapper {
        display: inline-block;
        text-align: center;
        margin: 10px;
        max-width: 180px;
        word-break: break-word;
    }

    .file-preview-area .preview-wrapper img,
    .file-preview-area .preview-wrapper video {
        width: 100%;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .file-preview-area .preview-wrapper p {
        font-size: 13px;
        margin-top: 5px;
    }

</style>

<div class="setup-wrapper-contents">
    <div class="create-project-wrapper-item">
        <div class="create-project-wrapper-item-top profile-border-bottom">
            <h4 class="create-project-wrapper-title">{{ __('Upload Project Image') }} </h4>
        </div>
            <div id="file-upload-warning" class="alert alert-danger d-none" style="margin-bottom:10px;"></div>
        <div class="create-project-wrapper-upload">
            <div class="create-project-wrapper-upload-browse center-text radius-10">
                <div class="file-preview-area mb-2">
                    <img src="" alt="" class="project_photo_preview d-none" style="max-width: 200px; max-height: 200px;">
                    <div class="file-name-box d-none">
                        <i class="fa-solid fa-file-lines me-2"></i> <span class="file-name-text"></span>
                    </div>
                </div>

                <span class="create-project-wrapper-upload-browse-icon mt-3">
                    <i class="fa-solid fa-image"></i>
                </span>
                <span class="create-project-wrapper-upload-browse-para">
                    {{ __('Drag and drop or Click to browse file') }}
                </span>

                <input class="upload-gallery" type="file" name="image[]" id="upload_project_photo" accept=".jpg,.jpeg,.png,.svg,.webp,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.mp4,.avi,.mov,.wmv,.mkv" multiple>
            </div>

            <p class="mt-3"><strong>{{ __('info:') }}</strong> {{ __('recommended dimensions 1770x960 pixels.Drag and drop single file only') }}</p>
        </div>
    </div>
</div>
<!-- Upload Gallery Ends -->

<script>
document.getElementById('upload_project_photo').addEventListener('change', function () {
    const previewContainer = document.querySelector('.file-preview-area');
    const warningBox = document.getElementById('file-upload-warning');
    previewContainer.innerHTML = ''; 
    warningBox.classList.add('d-none');
    warningBox.textContent = '';

    const files = this.files;
    if (!files.length) return;

    if (files.length > 5) {
        warningBox.textContent = 'You can upload a maximum of 5 files at a time.';
        warningBox.classList.remove('d-none');
        this.value = '';
        return;
    }

    Array.from(files).forEach(file => {
        const fileType = file.type;
        const fileName = file.name;
        const ext = fileName.split('.').pop().toLowerCase();

        const wrapper = document.createElement('div');
        wrapper.className = 'preview-wrapper';

        if (fileType.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = 'Image';
            wrapper.appendChild(img);
        } else if (fileType.startsWith('video/')) {
            const video = document.createElement('video');
            video.src = URL.createObjectURL(file);
            video.controls = true;
            video.style.maxHeight = '150px';
            wrapper.appendChild(video);
        } else {

            const iconPath = `/assets/img/file-icons/${ext}.png`; 
            const icon = document.createElement('img');
            icon.src = iconPath;
            icon.alt = ext + ' icon';
            icon.style.maxHeight = '60px';
            wrapper.appendChild(icon);

            const name = document.createElement('p');
            name.textContent = fileName;
            wrapper.appendChild(name);
        }

        previewContainer.appendChild(wrapper);
    });
});
</script>
