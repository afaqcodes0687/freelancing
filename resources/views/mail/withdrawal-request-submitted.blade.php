<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Request Submitted</title>
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
        .withdrawal-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
            border-left: 4px solid #309400;
        }
        .withdrawal-details h3 {
            color: #309400;
            margin-top: 0;
            font-size: 20px;
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        .detail-value {
            color: #555;
            font-size: 14px;
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .info-box {
            background-color: #e8f5e8;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .info-box h4 {
            color: #2d5a2d;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .info-box p {
            margin: 0;
            color: #2d5a2d;
            font-size: 14px;
            line-height: 1.5;
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
        .footer-links {
            margin-top: 20px;
        }
        .footer-links a {
            display: inline-block;
            margin: 0 10px;
            color: #309400;
            text-decoration: none;
        }
        .highlight {
            color: #309400;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Banner Image -->
        <div class="banner">
            <img src="{{ asset('assets/static/img/mail_banner.jpg') }}" alt="Withdrawal Request" style="width: 100%; display: block;">
        </div>

        <!-- Header Section -->
        <div class="header">
            <h1>💰 Withdrawal Request Submitted</h1>
            <p>Your withdrawal request has been received and is being processed</p>
        </div>

        <!-- Content Section -->
        <div class="content">
            <div class="greeting">
                Hello {{ $user->first_name }} {{ $user->last_name }}, 👋
            </div>
            <div class="message">
                Thank you for your withdrawal request. We have successfully received your request and it is now <strong>pending review</strong>. Our team will process it within 1-3 business days.
            </div>

            <!-- Withdrawal Details -->
            <div class="withdrawal-details">
                <h3>📋 Withdrawal Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Request ID:</span>
                    <span class="detail-value">#{{ $withdraw->id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value"><strong>{{ float_amount_with_currency_symbol($withdraw->amount) }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Gateway:</span>
                    <span class="detail-value">{{ $withdraw->gateway_name->name ?? $withdraw->gateway_fields['gateway_name'] ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge status-pending">Pending</span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Submitted Date:</span>
                    <span class="detail-value">{{ $withdraw->created_at->format('M d, Y - h:i A') }}</span>
                </div>
            </div>

            <!-- Information Box -->
            <div class="info-box">
                <h4>⏱️ What happens next?</h4>
                <p>
                    • Our team will review your request within 24 hours<br>
                    • Processing typically takes 1-3 business days<br>
                    • You'll receive an email when the status changes<br>
                    • Track your withdrawal status in your wallet history
                </p>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('freelancer.wallet.history') }}" style="display: inline-block; background: linear-gradient(135deg, #309400 0%, #2d8a00 100%); color: white; text-decoration: none; padding: 15px 30px; border-radius: 6px; font-weight: 600; font-size: 16px;">
                    View Wallet History
                </a>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <p style="font-size: 14px; color: #666;">
                    If you have any questions, please contact our support team.
                </p>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p><strong>Right Freelancer</strong></p>
            <p>Connecting clients with talents around the world</p>
            <div class="footer-links">
                <a href="{{ url('/') }}">Visit Website</a> |
                <a href="{{ url('/contact-us') }}">Contact Support</a> |
                <a href="{{ url('/terms-of-service') }}">Terms of Service</a>
            </div>
            <p style="font-size: 12px; color: #999; margin-top: 20px;">
                This email was sent to {{ $user->email }} on {{ now()->format('M d, Y - h:i A') }}
            </p>
        </div>
    </div>
</body>
</html>
