<!-- Add Portfolio Popup Starts -->
 <style>
    .portfolio_photo_preview,
.portfolio_video_preview {
    max-width: 100%;
    border-radius: 8px;
    margin-top: 10px;
}

 </style>
<div class="popup-fixed portfolioadd-popup">
    <div class="popup-contents">
        <span class="popup-contents-close popup-close"> <i class="fas fa-times"></i> </span>
        <h5 class="popup-contents-title">{{ __('Add Portfolio') }}</h5>
        <div class="error_msg_container mb-3"></div>
        <form action="#" id="add_portfolio_form">
            <div class="photo-uploaded photo-uploaded-padding center-text mt-4">
                <div class="photo-up loaded-flex uploadImage">
                    <div class="photo-uploaded-icon">
                        <img src="" class="portfolio_photo_preview">
                    </div>
                    <span class="create-project-wrapper-upload-browse-icon mt-3">
                        <i class="fa-solid fa-image"></i>
                    </span>
                    <span class="create-project-wrapper-upload-browse-para change_image_text"> {{ __('Click to upload portfolio image') }} </span>
                </div>
                <div class="photo-uploaded-icon">
                    <!-- Image Preview -->
                    <img src="" class="portfolio_photo_preview" style="display: none; max-width: 100%; border-radius: 5px;">

                    <!-- Video Preview -->
                    <video class="portfolio_video_preview" style="display: none; max-width: 100%; border-radius: 5px;" controls muted></video>
                </div>

                <input class="photo-uploaded-file" type="file" name="media" id="upload_portfolio_media" accept=".jpg,.jpeg,.png,.gif,.svg,.webp,.mp4,.mov,.avi,.webm,.pdf,.xls,.xlsx">
                <span><strong>{{ __('info:') }}</strong> {{ __('Max file size: 100MB. Allowed types: JPG, PNG, WebP, SVG, MP4, MOV, PDF, XLS, XLSX, etc.') }}</span>
            </div>

            <div class="popup-contents-form custom-form mt-4">
                <x-form.text :title="__('Title')" :type="'text'" :name="'portfolio_title'" :id="'portfolio_title'" :divClass="'mb-0'" :class="'form--control'" :placeholder="__('Write Project Title')" />
                <span id="portfolio_title_char_length_check"></span>
                <x-form.textarea :title="__('Description')" :name="'portfolio_description'" :id="'portfolio_description'" :divClass="'mb-0'" :class="'form-message'" :placeholder="__('Type Project Details')" />
                <span id="portfolio_description_char_length_check"></span>
            </div>
            <div class="popup-contents-btn flex-btn justify-content-end profile-border-top">
                <x-btn.close :title="__('Cancel')" :class="'btn-profile btn-outline-gray btn-hover-danger popup-close'" />
                <x-btn.submit :title="__('Save')" :class="'btn-profile btn-bg-1 add_portfolio'" />
            </div>
        </form>
    </div>
</div>
<!-- Add Portfolio Popup Ends -->
<script>
    document.querySelector('#upload_portfolio_media').addEventListener('change', function () {
        const maxSizeMB = 100;
        const maxSizeBytes = maxSizeMB * 1024 * 1024;

        const file = this.files[0];
        const imagePreview = document.querySelector('.portfolio_photo_preview');
        const videoPreview = document.querySelector('.portfolio_video_preview');
        const changeText = $("#add_portfolio_form").find('.change_image_text');

        // Remove any previous document preview
        let docPreview = document.querySelector('.portfolio_doc_preview');
        if (docPreview) docPreview.remove();

        // Reset previous previews
        imagePreview.style.display = "none";
        imagePreview.src = "";
        videoPreview.style.display = "none";
        videoPreview.src = "";

        if (file) {
            if (file.size > maxSizeBytes) {
                toastr_warning_js("{{ __('File must be less than 100MB') }}");
                this.value = "";
                changeText.text("{{ __('Click to upload portfolio image') }}");
                return;
            }

            const fileURL = URL.createObjectURL(file);
            const fileName = file.name;
            const ext = fileName.split('.').pop().toLowerCase();

            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'].includes(ext);
            const isVideo = ['mp4', 'mov', 'avi', 'webm'].includes(ext);
            const isDocument = ['pdf', 'doc', 'docx', 'xls', 'xlsx'].includes(ext);

            if (isImage) {
                imagePreview.src = fileURL;
                imagePreview.style.display = "block";
                videoPreview.style.display = "none";
                changeText.text("{{ __('Click to change image') }}");
            } else if (isVideo) {
                videoPreview.src = fileURL;
                videoPreview.style.display = "block";
                imagePreview.style.display = "none";
                changeText.text("{{ __('Click to change video') }}");
            } else if (isDocument) {
                // Show a generic document icon and filename
                const docIcon = {
                    pdf: 'fa-file-pdf',
                    doc: 'fa-file-word',
                    docx: 'fa-file-word',
                    xls: 'fa-file-excel',
                    xlsx: 'fa-file-excel'
                }[ext] || 'fa-file-alt';
                const docDiv = document.createElement('div');
                docDiv.className = 'portfolio_doc_preview';
                docDiv.style = 'margin-top:10px; text-align:center;';
                docDiv.innerHTML = `<i class="fas ${docIcon}" style="font-size:40px;color:#2b7a78;"></i><br><span style="font-size:14px;">${fileName}</span>`;
                imagePreview.parentNode.appendChild(docDiv);
                changeText.text("{{ __('Click to change document') }}");
            } else {
                toastr_warning_js("{{ __('Unsupported file type') }}");
                this.value = "";
                changeText.text("{{ __('Click to upload portfolio image') }}");
            }
        }
    });
</script>
