<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ get_static_option('site_title') . ' ' . __('Withdrawal Request Rejected') }}</title>
    <style>
        body { font-family: 'Open Sans', Helvetica, Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f7f6; padding-bottom: 40px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #333333; }
        .header { background-color: #1a2b4c; padding: 20px; text-align: center; }
        .header img { max-width: 180px; }
        .content { padding: 30px; }
        .headline { font-size: 24px; font-weight: 700; color: #dc3545; margin-bottom: 20px; text-align: center; }
        .message { font-size: 16px; line-height: 1.6; color: #555; margin-bottom: 30px; }
        .summary-card { background-color: #f9fbfb; border: 1px solid #e1e9e9; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .summary-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .summary-item:last-child { border-bottom: none; }
        .summary-label { font-weight: 600; color: #777; }
        .summary-value { font-weight: 700; color: #1a2b4c; }
        .highlight { font-size: 28px; font-weight: 800; color: #dc3545; text-align: center; margin: 20px 0; }
        .footer { background-color: #f4f7f6; padding: 20px; text-align: center; font-size: 12px; color: #999; }
        .button-wrap { text-align: center; margin-top: 20px; }
        .button { background-color: #007bff; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: 600; display: inline-block; }
        .reason-box { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .reason-title { font-weight: 700; color: #856404; margin-bottom: 10px; font-size: 16px; }
        .reason-text { color: #856404; font-size: 15px; line-height: 1.5; }
        .alert-icon { font-size: 48px; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main">
            <tr>
                <td class="header">
                    <a href="{{ url('/') }}">
                        {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
                    </a>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <div class="alert-icon">⚠️</div>
                    <div class="headline">{{ __('Withdrawal Request Rejected') }}</div>
                    <div class="message">
                        <p>{{ __('Hello') }} {{ $data['user_name'] }},</p>
                        <p>{{ __('We regret to inform you that your withdrawal request for the One Dollar Game has been rejected.') }}</p>
                    </div>

                    <div class="highlight">
                        ${{ number_format($data['amount'], 2) }}
                    </div>

                    <div class="summary-card">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px 0; color: #777; font-weight: 600;">{{ __('Withdrawal ID') }}</td>
                                <td style="padding: 10px 0; text-align: right; color: #1a2b4c; font-weight: 700;">#{{ $data['withdrawal_id'] }}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px 0; color: #777; font-weight: 600;">{{ __('Request Date') }}</td>
                                <td style="padding: 10px 0; text-align: right; color: #1a2b4c; font-weight: 700;">{{ $data['request_date'] }}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px 0; color: #777; font-weight: 600;">{{ __('Payment Gateway') }}</td>
                                <td style="padding: 10px 0; text-align: right; color: #1a2b4c; font-weight: 700; text-transform: uppercase;">{{ $data['payment_gateway'] }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0; color: #777; font-weight: 600;">{{ __('Account Number') }}</td>
                                <td style="padding: 10px 0; text-align: right; color: #1a2b4c; font-weight: 700;">{{ $data['account_number'] }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="reason-box">
                        <div class="reason-title">{{ __('Reason for Rejection:') }}</div>
                        <div class="reason-text">{{ $data['reject_reason'] }}</div>
                    </div>

                    <div class="message">
                        <p>{{ __('If you have any questions or believe this was a mistake, please contact our support team.') }}</p>
                        <p>{{ __('You can submit a new withdrawal request after addressing the issues mentioned above.') }}</p>
                    </div>

                    <div class="button-wrap">
                        <a href="{{ $data['dashboard_url'] }}" class="button">{{ __('Go to Dashboard') }}</a>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <p>&copy; {{ date('Y') }} {{ get_static_option('site_title') }}. {{ __('All rights reserved.') }}</p>
                    <p>{{ get_static_option('site_global_address') }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
