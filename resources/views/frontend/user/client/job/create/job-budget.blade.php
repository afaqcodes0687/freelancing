<!-- Budget, Skills Start -->
 <style>
#attachment_preview {
    padding: 15px;
}   
#attachment_preview img {
    max-width: 24%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.08);
}
#attachment_preview video {
    max-width: 24%;
    border-radius: 8px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.08);
}
#attachment_preview a,
#attachment_preview span {
    display: block;
    font-size: 14px;
    margin-top: 10px;
    color: #2d3748;
    word-break: break-all;
}
#attachment_preview a {
    color: #30862b;
    text-decoration: underline;
    font-weight: 500;
}
 </style>
<div class="setup-wrapper-contents">
    <div class="setup-wrapper-contents-item">
        <div class="setup-bank-form">
            <div class="setup-bank-form-item">
                <label class="label-title">{{ __('Job type') }}</label>
                <select class="form-control" name="type" id="type">
                    <option value="fixed" selected>{{ __('Fixed-Price (Pay a fixed amount for the job)') }}</option>
                    @if(moduleExists('HourlyJob'))
                    <option value="hourly">{{ __('Hourly Rate (Pay based on total hours worked for the job)') }}</option>
                    @endif
                </select>
            </div>
            @if(moduleExists('HourlyJob'))
            <div class="setup-bank-form-item setup-bank-form-item-icon d-none manage-hourly-jobs">
                <label class="label-title">{{ __('Hourly Rate') }}</label>
                <input type="number" class="form--control" name="hourly_rate" onkeyup="if(value<1) value=1; if(value>100000) value=1;" id="hourly_rate" placeholder="{{ __('Enter Hourly Rate') }}">
                <span class="input-icon">{{ get_static_option('site_global_currency') ?? '' }}</span>
            </div>
            <div class="setup-bank-form-item d-none manage-hourly-jobs">
                <label class="label-title">{{ __('Estimated Hours') }}</label>
                <input type="number" class="form--control" name="estimated_hours" onkeyup="if(value<1) value=1; if(value>100000) value=1;" id="estimated_hours" placeholder="{{ __('Enter Estimated Hours') }}">
            </div>
            @endif
            <div class="setup-bank-form-item setup-bank-form-item-icon manage-fixed-jobs">
                <label class="label-title">{{ __('Enter Budget') }}</label>
                <input type="number" class="form--control" name="budget" id="budget" value="0" placeholder="{{ __('Enter Your Budget') }}">
                <span class="input-icon">{{ get_static_option('site_global_currency') ?? '' }}</span>
            </div>

            <div id="attachment_preview" class="mt-3"></div>
            
            <div class="setup-bank-form-item">
                <label class="photo-uploaded center-text w-100">
                    <div class="photo-uploaded-flex d-flex uploadImage">
                        <div class="photo-uploaded-icon"><i class="fa-solid fa-paperclip"></i></div>
                        <span class="photo-uploaded-para">{{ __('Add attachments') }}</span>
                    </div>
                    <input class="photo-uploaded-file inputTag" type="file" name="attachment" id="attachment"
                    accept=".jpg,.jpeg,.png,.svg,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.mp4,.avi,.mov,.wmv,.mkv,.webp" />
                </label>
                    <p class="mt-2">{{ __('Supported formats: images, PDF, Word, Excel, videos (e.g., MP4, MOV, AVI, MKV)') }}</p>
            </div>
        </div>
    </div>
</div>
<!-- Budget, Skills Ends -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const attachmentInput = document.getElementById('attachment');
        const previewBox = document.getElementById('attachment_preview');

        attachmentInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            previewBox.innerHTML = ''; 

            if (!file) return;

            const fileType = file.type;

            if (fileType.startsWith('image/')) {
                // Image preview
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.maxWidth = '24%';
                img.style.borderRadius = '8px';
                previewBox.appendChild(img);
            } else if (fileType.startsWith('video/')) {
                // Video preview
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.controls = true;
                video.style.maxWidth = '24%';
                video.style.borderRadius = '8px';
                previewBox.appendChild(video);
            } else if (fileType === 'application/pdf') {
                // PDF preview (first page in browser or just name)
                const link = document.createElement('a');
                link.href = URL.createObjectURL(file);
                link.textContent = file.name + ' (Click to Preview PDF)';
                link.target = '_blank';
                previewBox.appendChild(link);
            } else {
                // For DOC, XLS, TXT, etc. — just show filename
                const span = document.createElement('span');
                span.textContent = 'File selected: ' + file.name;
                previewBox.appendChild(span);
            }
        });
    });
</script>



