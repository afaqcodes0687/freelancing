<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{get_static_option('site_title') . ' ' . __('Mail')}}</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Open Sans', sans-serif;
        }

        .mail-container {
            max-width: 650px;
            margin: 0 auto;
            text-align: center;
            background-color: #f2f2f2;
            padding: 0 0;
        }

        .inner-wrap {
            background-color: #fff;
            margin: 40px;
            padding: 30px 20px;
            text-align: left;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.01);
        }

        .inner-wrap p {
            font-size: 16px;
            line-height: 26px;
            color: #656565;
            margin: 0;
        }

        .message-wrap {
            background-color: #f2f2f2;
            padding: 20px;
            margin-top: 25px;
            border-left: 4px solid
                {{get_static_option('site_color')}}
            ;
        }

        .message-wrap p {
            font-size: 14px;
            line-height: 26px;
            margin-bottom: 0;
        }

        .btn-wrapper {
            text-align: center;
            margin-top: 30px;
        }

        .anchor-btn {
            background-color: #309400;
            color: #fff;
            font-size: 14px;
            line-height: 26px;
            font-weight: 500;
            text-transform: capitalize;
            text-decoration: none;
            padding: 10px 25px;
            display: inline-block;
            border-radius: 5px;
            transition: all 300ms;
        }

        .anchor-btn:hover {
            opacity: .8;
            color: #fff;
        }

        .logo-wrapper img {
            max-width: 200px;
        }

        .footer-copyright-wrapper {
            background-color: #17274a;
            padding: 16px 0;
            color: #7f7f7f;
            text-align: center;
        }

        .footer-copyright-wrapper a {
            color: #5080dd;
            text-decoration: none;
        }

        .footer-body-wrapper {
            overflow: hidden;
            background-color: #fff;
            padding: 20px;
        }

        .footer-body-wrapper table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-body-wrapper td {
            border: none;
        }

        .banner-container img {
            width: 100%;
            display: block;
        }
    </style>
</head>

<body>

    <div class="mail-container">
        <div class="banner-container">
            <img src="{{asset('assets/static/img/mail_banner.jpg')}}" alt="Mail Banner">
        </div>
        <div class="inner-wrap">
            <h3 style="color: #333; margin-top: 0;">{{ __('Action Required: Identity Verification Update') }}</h3>
            <p>{{ __('Hello') }} {{ $data['user_name'] }},</p>
            <p>{{ __('We reviewed your identity verification request. Unfortunately, some required information or documents are missing or were not clear enough for approval.') }}
            </p>

            @if(!empty($data['additional_message']))
                <div class="message-wrap" style="color: #309400;">
                    <p><strong>{{ __('Message from Admin:') }}</strong></p>
                    <p>{{ $data['additional_message'] }}</p>
                </div>
            @endif

            @if(isset($data['screenshot_base64']) && !empty($data['screenshot_base64']))
                <div class="message-wrap" style="background-color: #e8f4fd; border-left: 4px solid #007bff; margin-top: 15px;">
                    <p><strong>{{ __('Verification Reference Attached:') }}</strong></p>
                    <p>{{ __('A Verification Image has been attached to this email to help you understand what needs to be fixed.') }}</p>
                    
                </div>
            @endif

            <p style="margin-top: 25px;">
                {{ __('Please log in to your account and update your verification details to continue.') }}
            </p>

            <div class="btn-wrapper">
                <a href="{{ url('/login') }}" style="color: white;" class="anchor-btn">{{ __('Update Verification Details') }}</a>
            </div>
        </div>
        <footer>
            <div class="footer-body-wrapper">
                <table>
                    <tr>
                        <td style="width: 50%; text-align: center;">
                            <a href="{{url('/')}}">
                                {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
                            </a>
                        </td>
                        <td style="width: 50%; text-align: left; padding-left: 20px; border-left: 1px solid #e2e2e2;">
                            <span style="color: #858484">{{ __('Regards') }}</span>
                            <br>
                            <span
                                style="font-size: 20px; color: black; font-weight: 700;">{{ get_static_option('site_title') }}</span>
                            <br><span style="color: #858484">{{ __('Your Work Partner') }}</span>
                            <br><span><a href="{{url('/')}}"
                                    style="color: {{get_static_option('site_color')}}; text-decoration: none;">{{url('/')}}</a></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="footer-copyright-wrapper">
                &copy; {{ date('Y') }} {{ get_static_option('site_title') }}. {{ __('All rights reserved.') }}
                <br>
                <a target="_blank" href="{{url('/')}}">{{ __('Hire Freelancers for any job, online.') }}</a>
            </div>
        </footer>
    </div>

</body>

</html>