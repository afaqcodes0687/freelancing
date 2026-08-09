<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ get_static_option('site_title') . ' ' . __('Commission Received') }}</title>
    <style>
        body { font-family: 'Open Sans', Helvetica, Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f4f7f6; padding-bottom: 40px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #333333; }
        .header { background-color: #1a2b4c; padding: 20px; text-align: center; }
        .header img { max-width: 180px; }
        .content { padding: 30px; }
        .headline { font-size: 24px; font-weight: 700; color: #1a2b4c; margin-bottom: 20px; text-align: center; }
        .message { font-size: 16px; line-height: 1.6; color: #555; margin-bottom: 30px; }
        .summary-card { background-color: #f9fbfb; border: 1px solid #e1e9e9; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .summary-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .summary-item:last-child { border-bottom: none; }
        .summary-label { font-weight: 600; color: #777; }
        .summary-value { font-weight: 700; color: #1a2b4c; }
        .amount-highlight { font-size: 28px; font-weight: 800; color: #28a745; text-align: center; margin: 20px 0; }
        .footer { background-color: #f4f7f6; padding: 20px; text-align: center; font-size: 12px; color: #999; }
        .button-wrap { text-align: center; margin-top: 20px; }
        .button { background-color: #5080dd; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: 600; display: inline-block; }
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
                    <div class="headline">{{ __('Referral Commission Received!') }}</div>
                    <div class="message">
                        <p>{{ __('Hello') }} {{ $data['user_name'] }},</p>
                        <p>{{ __('Great news! You have just earned a new referral commission from the One Dollar Game.') }}</p>
                    </div>

                    <div class="amount-highlight">
                        ${{ number_format($data['amount'], 2) }}
                    </div>

                    <div class="summary-card">
                        <div style="width: 100%;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 10px 0; color: #777; font-weight: 600;">{{ __('Commission Level') }}</td>
                                    <td style="padding: 10px 0; text-align: right; color: #1a2b4c; font-weight: 700;">{{ __('Level') }} {{ $data['level'] }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 10px 0; color: #777; font-weight: 600;">{{ __('Referred User') }}</td>
                                    <td style="padding: 10px 0; text-align: right; color: #1a2b4c; font-weight: 700;">{{ $data['referred_user'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; color: #777; font-weight: 600;">{{ __('New Wallet Balance') }}</td>
                                    <td style="padding: 10px 0; text-align: right; color: #28a745; font-weight: 800;">${{ number_format($data['new_balance'], 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- <div class="button-wrap">
                        <a href="{{ $data['dashboard_url'] }}" class="button">{{ __('View My Wallet') }}</a>
                    </div> -->
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
