@extends('frontend.layout.master')
@section('site_title', __('Meetings'))
@section('style')
<style>
    .meeting-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #f0f0f0;
        border-radius: 15px;
        overflow: hidden;
    }
    .meeting-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .meeting-status-badge {
        font-size: 12px;
        padding: 5px 12px;
        border-radius: 20px;
    }
    .google-connect-banner {
        background: linear-gradient(135deg, #4285F4, #34A853);
        color: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
    }
    .google-btn {
        background: white;
        color: #4285F4;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        transition: background 0.3s ease;
    }
    .google-btn:hover {
        background: #f8f9fa;
        color: #3b71db;
    }
    .google-btn i {
        margin-right: 10px;
        font-size: 20px;
    }
    .text-brand-green {
        color: #309400 !important;
    }
    .bg-brand-green {
        background-color: #309400 !important;
        color: #fff !important;
        padding: 5px 12px;
        border-radius: 20px;
    }
    .btn-brand-green {
        background-color: #309400 !important;
        color: #fff !important;
        border: none;
        font-weight: 600;
        text-decoration: none;
        transition: opacity 0.3s ease;
    }
    .btn-brand-green:hover {
        opacity: 0.9;
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<main>
    <x-breadcrumb.user-profile-breadcrumb :title="__('Meetings')" :innerTitle="__('Meetings')"/>
    
    <div class="responsive-overlay"></div>
    <div class="profile-settings-area pat-100 pab-100 section-bg-2">
        <div class="container">
            <div class="row g-4">
                @include('frontend.user.layout.partials.sidebar')
                <div class="col-xl-9 col-lg-8">
                    <div class="profile-settings-wrapper">
                        
                        @php
                            $preferredProvider = get_static_option('preferred_meeting_provider') ?? 'google';
                        @endphp

                        @if($preferredProvider == 'google')
                            @if(!$googleAccount)
                                @if(!$systemAccountConnected)
                                    <div class="google-connect-banner shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-4" style="background: linear-gradient(135deg, #309400, #45a049) !important;">
                                        <div>
                                            <h3>{{ __('Schedule Meetings with Google Meet') }}</h3>
                                            <p class="mb-0 text-white">{{ __('Connect your Google account to start scheduling video meetings directly from the chat.') }}</p>
                                        </div>
                                        <a href="{{ route(Auth::guard('web')->user()->user_type == 1 ? 'client.meeting.google.redirect' : 'freelancer.meeting.google.redirect') }}" class="google-btn" style="color: #309400;">
                                            <i class="fa-brands fa-google"></i> {{ __('Connect Google Calendar') }}
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 radius-10">
                                        <i class="fa-solid fa-circle-info me-3 fs-4 text-brand-green"></i>
                                        <div>
                                            <strong>{{ __('Google Meetings Enabled') }}</strong>
                                            <p class="mb-0 small text-muted">{{ __('The system will automatically generate Google Meet links for you. You do not need to connect your own Gmail account.') }}</p>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 radius-10">
                                    <i class="fa-solid fa-circle-check me-3 fs-4 text-brand-green"></i>
                                    <div>
                                        <strong>{{ __('Google Calendar Connected') }}</strong> ({{ $googleAccount->email }})
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-primary border-0 shadow-sm d-flex align-items-center mb-4 radius-10" style="background: linear-gradient(135deg, #309400, #246e00); color: white;">
                                <i class="fa-solid fa-video me-3 fs-4"></i>
                                <div>
                                    <strong class="text-white">{{ __('Jitsi Meet Integration Active') }}</strong>
                                    <p class="mb-0 small text-white opacity-75">{{ __('You can schedule instant meetings from the chat without any account setup.') }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="single-profile-settings">
                            <div class="single-profile-settings-header">
                                <x-form.form-title :title="__('Upcoming Meetings')" :class="'single-profile-settings-header-title text-brand-green'" />
                            </div>
                            <div class="single-profile-settings-inner profile-border-top">
                                @if($meetings->count() > 0)
                                    <div class="row g-4 mt-2">
                                        @foreach($meetings as $meeting)
                                            <div class="col-md-6">
                                                <div class="card meeting-card h-100 shadow-sm">
                                                    <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center pt-3 px-3">
                                                        <span class="meeting-status-badge @if($meeting->status == 'scheduled') bg-brand-green text-white @elseif($meeting->status == 'completed') bg-success text-white @else bg-secondary text-white @endif">
                                                            {{ ucfirst($meeting->status) }}
                                                        </span>
                                                        <small class="text-muted"><i class="fa-regular fa-calendar-days me-1 text-brand-green"></i> {{ $meeting->start_time->format('d M, Y') }}</small>
                                                    </div>
                                                    <div class="card-body px-3">
                                                        <h5 class="card-title text-brand-green mb-2">{{ $meeting->title }}</h5>
                                                        <p class="card-text text-muted small mb-3">{{ Str::limit($meeting->description, 100) }}</p>
                                                        
                                                        <div class="meeting-info d-flex flex-column gap-2 mb-4">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fa-regular fa-clock me-2 text-brand-green" style="width: 20px;"></i>
                                                                <span>{{ $meeting->start_time->format('h:i A') }} - {{ $meeting->end_time->format('h:i A') }} ({{ $meeting->start_time->diffInMinutes($meeting->end_time) }} {{ __('mins') }})</span>
                                                            </div>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fa-solid fa-user-group me-2 text-brand-green" style="width: 20px;"></i>
                                                                <span>
                                                                    @if(Auth::id() == $meeting->sender_id)
                                                                        {{ __('With') }}: {{ $meeting->receiver?->full_name }}
                                                                    @else
                                                                        {{ __('With') }}: {{ $meeting->sender?->full_name }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="d-grid">
                                                            <a href="{{ $meeting->meeting_link }}" target="_blank" class="btn-brand-green text-center radius-10 py-2">
                                                                <i class="fa-solid fa-video me-2"></i> {{ __('Join Meeting') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-4">
                                        {{ $meetings->links() }}
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <div class="mb-4">
                                            <i class="fa-solid fa-video-slash fs-1 text-muted opacity-25"></i>
                                        </div>
                                        <h5>{{ __('No Meetings Scheduled') }}</h5>
                                        <p class="text-muted">{{ __('You haven\'t scheduled any meetings yet. You can schedule meetings from the Live Chat.') }}</p>
                                        <a href="{{ route(Auth::guard('web')->user()->user_type == 2 ? 'freelancer.live.chat' : 'client.live.chat') }}" class="btn btn-primary mt-3 radius-10">
                                            {{ __('Go to Live Chat') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
