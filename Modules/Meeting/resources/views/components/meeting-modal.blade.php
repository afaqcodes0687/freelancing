<style>
    .meeting-guidance-badge {
        background-color: #309400 !important;
        color: #fff !important;
        width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 11px;
    }
    .text-brand-green {
        color: #309400 !important;
    }
    .btn-brand-green {
        background-color: #309400 !important;
        color: #fff !important;
        border: none;
        padding: 10px 25px;
        border-radius: 5px;
        font-weight: 600;
        transition: opacity 0.3s ease;
    }
    .btn-brand-green:hover {
        opacity: 0.9;
        color: #fff !important;
    }
    .btn-brand-green:disabled {
        background-color: #ccc !important;
        cursor: not-allowed;
    }
</style>

<div class="modal fade" id="meetingModal" tabindex="-1" aria-labelledby="meetingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="meetingModalLabel">{{ __('Schedule Meeting') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php
                    $user_id = Auth::guard('web')->id();
                    $has_google_account = \Modules\Meeting\Entities\UserGoogleAccount::where('user_id', $user_id)->exists();
                    $preferredProvider = get_static_option('preferred_meeting_provider') ?? 'google';
                    $can_schedule = ($preferredProvider == 'jitsi' || $has_google_account);
                @endphp

                @if($preferredProvider == 'google' && !$has_google_account)
                    <div class="alert alert-warning border-0 shadow-sm mb-4" style="background-color: #fff9e6;">
                        <p class="mb-3 small"><strong>{{ __('Action Required:') }}</strong> {{ __('You must connect your Google account to generate Google Meet links.') }}</p>
                        <a href="{{ route(Auth::guard('web')->user()->user_type == 1 ? 'client.meeting.google.redirect' : 'freelancer.meeting.google.redirect') }}" class="btn-brand-green btn-sm d-inline-flex align-items-center">
                            <i class="fa-brands fa-google me-2"></i> {{ __('Connect Now') }}
                        </a>
                    </div>
                @elseif($preferredProvider == 'jitsi')
                    <div class="alert alert-info border-0 shadow-sm mb-4" style="background-color: #e6f7ff;">
                        <p class="mb-0 small"><i class="fa-solid fa-video me-2 text-brand-green"></i>{{ __('Meetings will be hosted on Jitsi Meet (No setup required).') }}</p>
                    </div>
                @endif

                <div class="meeting-guidance mb-4 p-3 bg-light radius-10 border" style="border-left: 4px solid #309400 !important;">
                    <h6 class="mb-3 text-brand-green fs-16"><i class="fa-solid fa-circle-info me-2"></i>{{ __('How it works?') }}</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2 d-flex align-items-start">
                            <span class="meeting-guidance-badge me-2 mt-1">1</span>
                            <span>{{ __('Fill in the meeting title, start time, and duration below.') }}</span>
                        </li>
                        <li class="mb-2 d-flex align-items-start">
                            <span class="meeting-guidance-badge me-2 mt-1">2</span>
                            <span>{{ __('Click "Schedule" to generate an instant meeting link.') }}</span>
                        </li>
                        <li class="d-flex align-items-start">
                            <span class="meeting-guidance-badge me-2 mt-1">3</span>
                            <span>{{ __('The link will be sent to the chat automatically for both of you to join.') }}</span>
                        </li>
                    </ul>
                </div>

                <form id="meetingScheduleForm" @if(!$can_schedule) style="opacity: 0.5; pointer-events: none;" @endif>
                    @csrf
                    <input type="hidden" name="live_chat_id" id="meeting_live_chat_id">
                    <input type="hidden" name="receiver_id" id="meeting_receiver_id">
                    
                    <div class="single-input mb-3">
                        <label class="label-title">{{ __('Meeting Title') }}</label>
                        <input type="text" name="title" class="form-control" placeholder="{{ __('Enter Meeting Title') }}" required>
                    </div>

                    <div class="single-input mb-3">
                        <label class="label-title">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="{{ __('Enter Description') }}"></textarea>
                    </div>

                    <div class="single-input mb-3">
                        <label class="label-title">{{ __('Start Time') }}</label>
                        <input type="datetime-local" name="start_time" id="meeting_start_time" class="form-control" required>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const startTimeInput = document.getElementById('meeting_start_time');
                            
                            function updateMinTime() {
                                const now = new Date();
                                // Format to YYYY-MM-DDTHH:MM
                                const year = now.getFullYear();
                                const month = String(now.getMonth() + 1).padStart(2, '0');
                                const day = String(now.getDate()).padStart(2, '0');
                                const hours = String(now.getHours()).padStart(2, '0');
                                const minutes = String(now.getMinutes()).padStart(2, '0');
                                
                                const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
                                startTimeInput.setAttribute('min', minDateTime);
                            }

                            // Update on page load
                            updateMinTime();

                            // Update when modal is shown (using Bootstrap event)
                            const meetingModal = document.getElementById('meetingModal');
                            if (meetingModal) {
                                meetingModal.addEventListener('shown.bs.modal', function () {
                                    updateMinTime();
                                });
                            }
                        });
                    </script>

                    <div class="single-input mb-3">
                        <label class="label-title">{{ __('Duration (Minutes)') }}</label>
                        <select name="duration" class="form-control" required>
                            <option value="15">15 {{ __('Minutes') }}</option>
                            <option value="30" selected>30 {{ __('Minutes') }}</option>
                            <option value="45">45 {{ __('Minutes') }}</option>
                            <option value="60">60 {{ __('Minutes') }}</option>
                            <option value="90">90 {{ __('Minutes') }}</option>
                            <option value="120">120 {{ __('Minutes') }}</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn-brand-green" id="confirmScheduleMeeting" @if(!$can_schedule) disabled title="{{ __('Please connect Google account first') }}" @endif>{{ __('Schedule Meeting') }}</button>
            </div>
        </div>
    </div>
</div>
