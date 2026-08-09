<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Status Update</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header.success {
            background: linear-gradient(135deg, #309400 0%, #2d8a00 100%);
        }
        .header.processing {
            background: linear-gradient(135deg, #309400 0%, #2d8a00 100%);
        }
        .header.pending {
            background: linear-gradient(135deg, #309400 0%, #2d8a00 100%);
        }
        .header.cancelled {
            background: linear-gradient(135deg, #309400 0%, #2d8a00 100%);
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
        .withdrawal-details.processing {
            border-left-color: #309400;
        }
        .withdrawal-details.pending {
            border-left-color: #309400;
        }
        .withdrawal-details.cancelled {
            border-left-color: #309400;
        }
        .withdrawal-details h3 {
            color: #309400;
            margin-top: 0;
            font-size: 20px;
            margin-bottom: 20px;
        }
        .withdrawal-details.processing h3 {
            color: #309400;
        }
        .withdrawal-details.pending h3 {
            color: #309400;
        }
        .withdrawal-details.cancelled h3 {
            color: #309400;
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
        .status-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-processing {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status-update-box {
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }
        .status-update-box.success {
            background-color: #e8f5e8;
            border: 1px solid #c3e6cb;
        }
        .status-update-box.processing {
            background-color: #e8f5e8;
            border: 1px solid #c3e6cb;
        }
        .status-update-box.pending {
            background-color: #e8f5e8;
            border: 1px solid #c3e6cb;
        }
        .status-update-box.cancelled {
            background-color: #e8f5e8;
            border: 1px solid #c3e6cb;
        }
        .status-update-box h4 {
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .status-update-box.success p {
            color: #2d5a2d;
        }
        .status-update-box.processing p {
            color: #2d5a2d;
        }
        .status-update-box.pending p {
            color: #2d5a2d;
        }
        .status-update-box.cancelled p {
            color: #2d5a2d;
        }
        .status-update-box p {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
        }
        .action-button {
            display: inline-block;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        .action-button.success {
            background: linear-gradient(135deg, #309400 0%, #2d8a00 100%);
            color: white;
        }
        .action-button.processing {
            background: linear-gradient(135deg, #309400 0%, #2d8a00 100%);
            color: white;
        }
        .action-button.pending {
            background: linear-gradient(135deg, #309400 0%, #2d8a00 100%);
            color: white;
        }
        .action-button.cancelled {
            background: linear-gradient(135deg, #309400 0%, #2d8a00 100%);
            color: white;
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
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Banner Image -->
        <div class="banner">
            <img src="{{ asset('assets/static/img/mail_banner.jpg') }}" alt="Withdrawal Status Update" style="width: 100%; display: block;">
        </div>

        <!-- Header Section -->
        <div class="header {{ $header_class ?? 'success' }}">
            <h1>{{ $header_icon ?? '✅' }} Withdrawal Status Update</h1>
            <p>{{ $header_message ?? 'Your withdrawal request has been updated' }}</p>
        </div>

        <!-- Content Section -->
        <div class="content">
            <div class="greeting">
                Hello {{ $user->first_name }} {{ $user->last_name }}, 👋
            </div>
            <div class="message">
                Your withdrawal request <strong>#{{ $withdraw_request->id }}</strong> status has been updated. Please find the details below.
            </div>

            <!-- Withdrawal Details -->
            <div class="withdrawal-details {{ $details_class ?? 'success' }}">
                <h3>📋 Withdrawal Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Request ID:</span>
                    <span class="detail-value">#{{ $withdraw_request->id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value"><strong>{{ float_amount_with_currency_symbol($withdraw_request->amount) }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Gateway:</span>
                    <span class="detail-value">{{ $withdraw_request->gateway_name->name ?? $withdraw_request->gateway_fields['gateway_name'] ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge {{ $status_class ?? 'status-success' }}">{{ $status_text }}</span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Updated Date:</span>
                    <span class="detail-value">{{ now()->format('M d, Y - h:i A') }}</span>
                </div>
            </div>

            <!-- Status Update Message -->
            <div class="status-update-box {{ $update_box_class ?? 'success' }}">
                <h4>{{ $update_title ?? '✅ Withdrawal Completed' }}</h4>
                <p>{{ $update_message ?? 'Your withdrawal has been successfully processed and the funds have been sent to your selected payment method.' }}</p>
            </div>

            <!-- Action Button -->
            @if(isset($show_button) && $show_button)
            <div style="text-align: center;">
                <a href="{{ route('freelancer.wallet.history') }}" class="action-button {{ $button_class ?? 'success' }}" style="color: white;">
                    View Wallet History
                </a>
            </div>
            @endif

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
