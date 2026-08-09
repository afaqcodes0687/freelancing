<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're invited to Right Freelancer!</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .banner {
            width: 100%;
        }
        .header {
            background: linear-gradient(135deg, #309400 0%, #2d8a00 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        .message {
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 30px;
            color: #555;
        }
        .benefits {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }
        .benefits h3 {
            color: #309400;
            margin-top: 0;
            font-size: 20px;
        }
        .benefit-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .benefit-item:last-child {
            margin-bottom: 0;
        }
        .benefit-icon {
            width: 20px;
            height: 20px;
            background-color: #309400;
            border-radius: 50%;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #309400 0%, #2d8a00 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(48, 148, 0, 0.3);
        }
        .referral-link {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            word-break: break-all;
            font-family: monospace;
            font-size: 14px;
            color: #309400;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .social-links {
            margin-top: 20px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #309400;
            text-decoration: none;
        }
        .highlight {
            color: #309400;
            font-weight: 600;
        }
        .discount-badge {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .discount-badge h4 {
            color: #856404;
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .discount-badge p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Banner Image -->
        <div class="banner">
            <img src="{{ asset('assets/static/img/mail_banner.jpg') }}" alt="You're Invited" style="width: 100%; display: block;">
        </div>

        <!-- Header Section -->
        <div class="header">
            <h1>🎉 You're Invited!</h1>
            <p>{{ $referrer->first_name }} {{ $referrer->last_name }} thanks you'd love Right Freelancer</p>
        </div>

        <!-- Content Section -->
        <div class="content">
            <div class="greeting">
                Hi there! 👋
            </div>
            <div class="message">
                <strong>{{ $referrer->first_name }}</strong> has invited you to join <strong>Right Freelancer</strong> – the world's fastest and most affordable freelance platform where you can connect with over 1M freelancer experts.
            </div>

            <div class="discount-badge">
                <h4>🎁 Special Offer Just For You!</h4>
                <p>Get <strong>10% off your first order</strong> when you join through this invitation</p>
            </div>

            <div class="benefits">
                <h3>Why Right Freelancer?</h3>
                <div class="benefit-item">
                    <div class="benefit-icon">✓</div>
                    <span>Connect with over 1M verified freelancers worldwide</span>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">✓</div>
                    <span>Secure payments with escrow protection</span>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">✓</div>
                    <span>24/7 live support for all your needs</span>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">✓</div>
                    <span>No fees for clients - start hiring for free</span>
                </div>
            </div>

            <div style="text-align: center;">
             <a href="{{ route('user.register', ['ref' => auth()->user()->referral_code]) }}" class="cta-button" style="color:white">
                Join Right Freelancer Now
            </a>

            </div>

            <div style="text-align: center; margin-top: 20px;">
                <p style="font-size: 14px; color: #666;">
                    Or copy this link to your browser:
                </p>
                <div class="referral-link">
                    {{ $referralLink }}
                </div>
            </div>

            <!-- <div style="margin-top: 30px; padding: 20px; background-color: #e8f5e8; border-radius: 6px;">
                <p style="margin: 0; font-size: 14px; color: #2d5a2d;">
                    <strong>What happens next?</strong><br>
                    When you complete your first order, {{ $referrer->first_name }} will receive Right Freelancer Credits as a thank you for the referral!
                </p>
            </div> -->
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p><strong>Right Freelancer</strong></p>
            <p>Connecting clients with talents around the world</p>
            <p style="font-size: 12px; color: #999;">
                This invitation was sent by {{ $referrer->first_name }} {{ $referrer->last_name }} ({{ $referrer->email }})
            </p>
            <div class="social-links">
                <a href="https://rightfreelancer.com">Visit Website</a> |
                <a href="https://rightfreelancer.com/contact-us">Contact Support</a> |
                <a href="https://rightfreelancer.com/terms-of-service">Terms of Service</a>
            </div>
        </div>
    </div>
</body>
</html>
