<!-- Budget, Skills Start -->
<style>
#attachment_preview img,
#attachment_preview video {
    max-width: 24%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.08);
}
#attachment_preview a,
#attachment_preview span {
    display: block;
    font-size: 14px;
    margin-top: 10px;
    color: #2d3748;
    word-break: break-word;
}
#attachment_preview a {
    color: #30862b;
    font-weight: 500;
    text-decoration: underline;
}
</style>

<div class="setup-wrapper-contents">
    <div class="setup-wrapper-contents-item">
        <div class="setup-bank-form">
            <div class="setup-bank-form-item">
                <label class="label-title">{{ __('Job type') }}</label>
                <select class="form-control" name="type" id="type">
                    <option value="fixed" {{ $job_details->type == 'fixed' ? 'selected' : ''}}>{{ __('Fixed-Price (Pay a fixed amount for the job)') }}</option>
                    @if(moduleExists('HourlyJob'))
                        <option value="hourly" {{ $job_details->type == 'hourly' ? 'selected' : ''}}>{{ __('Hourly Rate (Pay based on total hours worked for the job)') }}</option>
                    @endif
                </select>
            </div>
            @if(moduleExists('HourlyJob'))
                <div class="setup-bank-form-item setup-bank-form-item-icon d-none manage-hourly-jobs">
                    <label class="label-title">{{ __('Hourly Rate') }}</label>
                    <input type="number" class="form--control" name="hourly_rate" onkeyup="if(value<1) value=1; if(value>100000) value=1;" value="{{ $job_details->hourly_rate ?? '' }}" id="hourly_rate" placeholder="{{ __('Enter Hourly Rate') }}">
                    <span class="input-icon">{{ get_static_option('site_global_currency') ?? '' }}</span>
                </div>
                <div class="setup-bank-form-item d-none manage-hourly-jobs">
                    <label class="label-title">{{ __('Estimated Hours') }}</label>
                    <input type="number" class="form--control" name="estimated_hours" onkeyup="if(value<1) value=1; if(value>100000) value=1;" value="{{ $job_details->estimated_hours ?? '' }}" id="estimated_hours" placeholder="{{ __('Enter Estimated Hours') }}">
                </div>
            @endif
            <div class="setup-bank-form-item setup-bank-form-item-icon manage-fixed-jobs">
                <label class="label-title">{{ __('Enter Budget') }}</label>
                <input type="number" class="form--control" name="budget" id="budget" value="{{ $job_details->budget }}" placeholder="{{ __('Enter Your Budget') }}">
                <span class="input-icon">{{ get_static_option('site_global_currency') ?? '' }}</span>
            </div>
           
            <div class="setup-bank-form-item">
                @php
                    $extension = pathinfo($job_details->attachment ?? '', PATHINFO_EXTENSION);
                    $image_extensions = ['png','jpg','jpeg','bmp','gif','tiff','svg','webp'];
                @endphp

              <div id="attachment_preview" class="mt-3">
                    @php
                        $extension = pathinfo($job_details->attachment ?? '', PATHINFO_EXTENSION);
                        $image_extensions = ['png','jpg','jpeg','bmp','gif','tiff','svg','webp'];
                        $video_extensions = ['mp4','mov','avi','mkv','webm'];
                        $url = cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi'])
                            ? render_frontend_cloud_image_if_module_exists('jobs/'.$job_details->attachment, load_from: $job_details->load_from)
                            : asset('assets/uploads/jobs/'.$job_details->attachment);
                    @endphp

                    @if($job_details->attachment)
                        @if(in_array($extension, $image_extensions))
                            <img src="{{ $url }}" alt="{{ $job_details->attachment }}">
                        @elseif(in_array($extension, $video_extensions))
                            <video src="{{ $url }}" controls></video>
                        @else
                            <a href="{{ $url }}" target="_blank">{{ $job_details->attachment }}</a>
                        @endif
                    @endif
                </div>
                <label class="photo-uploaded center-text w-100 mt-3">
                    <div class="photo-uploaded-flex d-flex uploadImage">
                        <div class="photo-uploaded-icon"><i class="fa-solid fa-paperclip"></i></div>
                        <span class="photo-uploaded-para">{{ __('Add attachments') }}</span>
                    </div>
                    <input class="photo-uploaded-file inputTag" type="file" name="attachment" id="attachment"
                        accept=".jpg,.jpeg,.png,.svg,.gif,.webp,.bmp,.tiff,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.mp4,.mov,.avi,.mkv,.webp">
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
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                previewBox.appendChild(img);
            } else if (fileType.startsWith('video/')) {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.controls = true;
                previewBox.appendChild(video);
            } else if (fileType === 'application/pdf') {
                const link = document.createElement('a');
                link.href = URL.createObjectURL(file);
                link.textContent = file.name + ' (Click to Preview PDF)';
                link.target = '_blank';
                previewBox.appendChild(link);
            } else {
                const span = document.createElement('span');
                span.textContent = 'File selected: ' + file.name;
                previewBox.appendChild(span);
            }
        });
    });
</script>
