@php
    $rawPayload = $message->message;
    $isArrayPayload = is_array($rawPayload);
    $project = $isArrayPayload ? json_decode(json_encode($rawPayload['project'] ?? null)) : null;
    $meeting = ($isArrayPayload && isset($rawPayload['type']) && $rawPayload['type'] === 'meeting') ? json_decode(json_encode($rawPayload)) : null;
    $textMessage = $isArrayPayload ? ($rawPayload['message'] ?? '') : (string) $rawPayload;
@endphp

@if($message->from_user == 1)
    <div class="chat-wrapper-details-inner-chat chat-reply" data-message-id="{{ $message->id }}">
        <div class="chat-wrapper-details-inner-chat-flex">
            <div class="chat-wrapper-details-inner-chat-thumb">
                @if($data->client?->image)
                    @if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                        <img src="{{ render_frontend_cloud_image_if_module_exists( 'profile/'. $data?->client?->image, load_from: $data?->client?->load_from ?? '') }}" alt="{{ $data->client?->fullname }}">
                    @else
                        <img src="{{ asset('assets/uploads/profile/'.$data->client?->image) }}" alt="">
                    @endif
                @else
                    <img src="{{ asset('assets/static/img/author/author.jpg') }}" alt="{{ __('author') }}">
                @endif
            </div>
            <div class="chat-wrapper-details-inner-chat-contents {{ !empty($project->type) ? "p-2 text-dark bg-opacity-10" : "" }}">
                <p class="chat-wrapper-details-inner-chat-contents-para {{ !empty($project) ? "d-none" : "" }}">
                @if(!empty($textMessage))
                    <span class="chat-wrapper-details-inner-chat-contents-para-span" data-chat-text>{{ $textMessage }}</span>
                    @endif

                    @if(!empty($message->file))
                        <br />
                        <br />
                        @php
                            $ext = pathinfo($message->file, PATHINFO_EXTENSION);
                        @endphp
                        @if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                            @if($ext == 'pdf' || $ext == 'docx' || $ext == 'zip')
                                <a class="download-pdf-chat mt-2" href="{{ render_frontend_cloud_image_if_module_exists('media-uploader/live-chat/'. $message->file, load_from: $message->load_from) }}" download>{{ __('Download file') }}</a>
                            @else
                                <img src="{{ render_frontend_cloud_image_if_module_exists( 'media-uploader/live-chat/'.$message->file, load_from: $message->load_from) }}">
                                <br />
                                <a class="download-pdf-chat mt-2" href="{{ render_frontend_cloud_image_if_module_exists('media-uploader/live-chat/'. $message->file, load_from: $message->load_from) }}" download>{{ __('Download file') }}</a>
                            @endif
                        @else
                            @if($ext == 'pdf' || $ext == 'docx' || $ext == 'zip')
                                <a class="download-pdf-chat mt-2" href="{{ asset('assets/uploads/media-uploader/live-chat/'. $message->file) }}" download>{{ __('Download file') }}</a>
                            @else
                                <img src="{{ asset('assets/uploads/media-uploader/live-chat/'. $message->file) }}" alt="{{ $message->file ?? '' }}">
                                <br />
                                <a class="download-pdf-chat mt-2" href="{{ asset('assets/uploads/media-uploader/live-chat/'. $message->file) }}" download>{{ __('Download file') }}</a>
                            @endif
                        @endif
                    @endif
                </p>

                @if(!empty($project))
                    <div class="card mb-3" style="max-width: 540px;">
                        <div class="row g-0">
                            <div class="col-md-4 {{ ($project->type ?? '') == 'job'?'d-none' : '' }}">
                                @if(($project->type ?? '') == 'job')
                                    <span></span>
                                @else
                                    @if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                                        <img class="img-fluid rounded-start" src="{{ render_frontend_cloud_image_if_module_exists( 'project/'. $project->image, load_from: $project->load_from ?? '') }}" alt="{{ $project->image }}">
                                    @else
                                        <img src="{{ asset('assets/uploads/project/'.$project->image) }}" class="img-fluid rounded-start" alt="{{ $project->image ?? ''}}">
                                    @endif
                                @endif
                            </div>
                            <div class="{{ ($project->type ?? '') == 'job'?'col-md-12' : 'col-md-8' }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $project->title }}</h5>
                                    @if(($project->type ?? '') == 'job')
                                        <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('job.details', ['username' => $project->username, 'slug' => $project->slug]) }}">{{ __('View details') }}</a>

                                        @php
                                            $proposalId = $project->proposal_id ?? null;
                                        @endphp
                                        @if($proposalId)
                                            <div class="d-flex flex-wrap gap-2 mt-4">
                                                <a href="{{ url('client/job/proposal/details/' . $proposalId) }}"  target="_blank" class="btn btn-success btn-sm">
                                                    {{ __('Accept') }}
                                                </a>
                                                <a href="{{ url('client/job/proposal/details/' . $proposalId) }}" target="_blank" class="btn btn-warning btn-sm">
                                                    {{ __('Add to Shortlist') }}
                                                </a>
                                                <a href="{{ url('client/job/proposal/details/' . $proposalId) }}" target="_blank" class="btn btn-info btn-sm">
                                                    {{ __('Take a Interview') }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                    @else
                                        <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('project.details', ['username' => $project->username, 'slug' => $project->slug]) }}">{{ __('View details') }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        @if(($project->type ?? '') == 'job')
                            <h5>{{ $project->interview_message ?? '' }}</h5>
                        @endif
                    </div>
                @endif
                
                @if(!empty($meeting))
                    <div class="card mb-3 border-info" style="max-width: 540px;">
                        <div class="card-header bg-info text-white d-flex align-items-center">
                            <i class="fa-solid fa-video me-2"></i> {{ __('Google Meeting Scheduled') }}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title text-info">{{ $meeting->title }}</h5>
                            <p class="card-text">
                                <strong>{{ __('Time') }}:</strong> {{ \Carbon\Carbon::parse($meeting->start_time)->format('D, d M Y, h:i A') }}
                            </p>
                            <div class="mt-3">
                                <a href="{{ $meeting->link }}" target="_blank" class="btn btn-info text-white w-100">
                                    <i class="fa-solid fa-video me-1"></i> {{ __('Join Meeting') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
               @php
                    \Carbon\Carbon::setLocale('en');
                @endphp

                    <span class="chat-wrapper-details-inner-chat-contents-time mt-0 d-flex align-items-center justify-content-end" style="font-size: 10px; color: #888;">
                        <span>{{ $message->created_at->diffForHumans() }}</span>
                        <span class="message-status-ticks ms-1" data-status-msg-id="{{ $message->id }}">
                            @if($message->is_seen == 1)
                                <i class="fa-solid fa-check-double text-primary" style="font-size: 10px;"></i>
                            @elseif($message->is_delivered == 1)
                                <i class="fa-solid fa-check-double text-muted" style="font-size: 10px;"></i>
                            @else
                                <i class="fa-solid fa-check text-muted" style="font-size: 10px;"></i>
                            @endif
                        </span>
                    </span>

                @php
                    \Carbon\Carbon::setLocale(app()->getLocale()); // Restore original locale
                @endphp

            </div>
        </div>
    </div>
@endif

@if($message->from_user == 2)
    <div class="chat-wrapper-details-inner-chat" data-message-id="{{ $message->id }}">
        <div class="chat-wrapper-details-inner-chat-flex">
            <div class="chat-wrapper-details-inner-chat-thumb">
                 <a href="{{ route('freelancer.profile.details', $data?->freelancer?->username) }}" target="_blank">
                    @if($data->freelancer?->image)
                         @if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                             <img src="{{ render_frontend_cloud_image_if_module_exists( 'profile/'. $data?->freelancer?->image, load_from: $data?->freelancer?->load_from ?? '') }}" alt="{{ $data->freelancer?->fullname }}">
                         @else
                            <img src="{{ asset('assets/uploads/profile/'.$data->freelancer?->image) }}" alt="">
                         @endif
                    @else
                        <img src="{{ asset('assets/static/img/author/author.jpg') }}" alt="{{ __('author') }}">
                    @endif
                </a>
            </div>
            <div class="chat-wrapper-details-inner-chat-contents">
                <p class="chat-wrapper-details-inner-chat-contents-para">
                    @if(!empty($textMessage))
                    <span class="chat-wrapper-details-inner-chat-contents-para-span" data-chat-text>{{ $textMessage }}</span>
                    @endif
                    @if(!empty($message->file))
                        <br />
                        <br />
                            @php
                                $ext = pathinfo($message->file, PATHINFO_EXTENSION);
                            @endphp
                            @if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                                @if($ext == 'pdf' || $ext == 'docx' || $ext == 'zip')
                                    <a class="download-pdf-chat mt-2" href="{{ render_frontend_cloud_image_if_module_exists('media-uploader/live-chat/'. $message->file, load_from: $message->load_from) }}" download>{{ __('Download file') }}</a>
                                @else
                                    <img src="{{ render_frontend_cloud_image_if_module_exists( 'media-uploader/live-chat/'.$message->file, load_from: $message->load_from) }}">
                                    <br />
                                    <a class="download-pdf-chat mt-2" href="{{ render_frontend_cloud_image_if_module_exists('media-uploader/live-chat/'. $message->file, load_from: $message->load_from) }}" download>{{ __('Download file') }}</a>
                                @endif
                            @else
                                @if($ext == 'pdf' || $ext == 'docx' || $ext == 'zip')
                                    <a class="download-pdf-chat mt-2" href="{{ asset('assets/uploads/media-uploader/live-chat/'. $message->file) }}" download>{{ __('Download file') }}</a>
                                @else
                                    <img src="{{ asset('assets/uploads/media-uploader/live-chat/'. $message->file) }}" alt="{{ $message->file ?? '' }}">
                                    <br />
                                    <a class="download-pdf-chat mt-2" href="{{ asset('assets/uploads/media-uploader/live-chat/'. $message->file) }}" download>{{ __('Download file') }}</a>
                                @endif
                            @endif
                    @endif
                </p>

                @if(!empty($project))
                    <div class="card mb-3" style="max-width: 540px;">
                        <div class="row g-0">
                            <div class="col-md-4 {{ ($project->type ?? '') == 'job'?'d-none' : '' }}">
                                @if(($project->type ?? '') == 'job')
                                    <span></span>
                                @else
                                    @if(cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']))
                                        <img class="img-fluid rounded-start" src="{{ render_frontend_cloud_image_if_module_exists( 'project/'. $project->image, load_from: $project->load_from ?? '') }}" alt="{{ $project->image }}">
                                    @else
                                        <img src="{{ asset('assets/uploads/project/'.$project->image) }}" class="img-fluid rounded-start" alt="{{ $project->image ?? '' }}">
                                    @endif
                                @endif
                            </div>
                            <div class="{{ ($project->type ?? '') == 'job'?'col-md-12' : 'col-md-8' }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $project->title }}</h5>
                                    <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('project.details', ['username' => $project->username, 'slug' => $project->slug]) }}">{{ __('View details') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                   @php
                        \Carbon\Carbon::setLocale('en');
                    @endphp

                    <span class="chat-wrapper-details-inner-chat-contents-time mt-0 d-flex align-items-center justify-content-start" style="font-size: 10px; color: #888;">
                        <span>{{ $message->created_at->diffForHumans() }}</span>
                    </span>

                    @php
                        \Carbon\Carbon::setLocale(app()->getLocale());
                    @endphp

            </div>
        </div>
    </div>
@endif
