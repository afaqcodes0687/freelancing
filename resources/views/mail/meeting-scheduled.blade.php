<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Meeting Scheduled') }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { width: 100%; max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header { background-color: #309400; color: #ffffff; padding: 25px; text-align: center; }
        .content { padding: 30px; }
        .footer { background: #f9f9f9; padding: 20px; text-align: center; font-size: 12px; color: #777; }
        .details-box { background: #f0f7ed; border-left: 4px solid #309400; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .btn { display: inline-block; background-color: #309400; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        .detail-row { margin-bottom: 10px; }
        .detail-label { font-weight: bold; color: #555; width: 120px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="banner-container">
            <img src="{{ asset('assets/static/img/mail_banner.jpg') }}" alt="Mail Banner" style="width: 100%;">
        </div>
        <div class="header">
            <h1>{{ __('Meeting Scheduled') }}</h1>
        </div>
        <div class="content">
            <p>{{ __('Hello') }} <strong>{{ $recipientName }}</strong>,</p>
            <p>{{ __('A new meeting has been scheduled with') }} <strong>{{ $otherPartyName }}</strong>.</p>
            
            <div class="details-box">
                <div class="detail-row">
                    <span class="detail-label">{{ __('Title') }}:</span> <span>{{ $meeting->title }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('Date') }}:</span> <span>{{ $meeting->start_time->format('d M, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('Time') }}:</span> <span>{{ $meeting->start_time->format('h:i A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ __('Duration') }}:</span> <span>{{ $meeting->duration ?? 30 }} {{ __('Minutes') }}</span>
                </div>
            </div>

            <p>{{ __('You can join the meeting by clicking the button below at the scheduled time.') }}</p>
            
            <div style="text-align: center;">
                <a href="{{ $meeting->meeting_link }}" class="btn">{{ __('Join Meeting') }}</a>
            </div>

            <p style="margin-top: 30px;">{{ __('Regards') }},<br>{{ get_static_option('site_title') }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ get_static_option('site_title') }}. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</body>
</html>
