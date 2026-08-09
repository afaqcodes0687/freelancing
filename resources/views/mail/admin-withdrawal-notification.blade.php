<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Withdrawal Request</title>
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
        .user-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .user-info h4 {
            color: #856404;
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        .user-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .user-detail:last-child {
            margin-bottom: 0;
        }
        .action-button {
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
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(48, 148, 0, 0.3);
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
            <img src="{{ asset('assets/static/img/mail_banner.jpg') }}" alt="New Withdrawal Request" style="width: 100%; display: block;">
        </div>

        <!-- Header Section -->
        <div class="header">
            <h1>🚨 New Withdrawal Request</h1>
            <p>A freelancer has submitted a withdrawal request that needs your attention</p>
        </div>

        <!-- Content Section -->
        <div class="content">
            <div class="greeting">
                Hello Admin, 👋
            </div>
            <div class="message">
                A new withdrawal request has been submitted by <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>. Please review and process this request as soon as possible.
            </div>

            <!-- User Information -->
            <div class="user-info">
                <h4>👤 Freelancer Information</h4>
                <div class="user-detail">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value">{{ $user->first_name }} {{ $user->last_name }}</span>
                </div>
                <div class="user-detail">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>
                <div class="user-detail">
                    <span class="detail-label">User ID:</span>
                    <span class="detail-value">#{{ $user->id }}</span>
                </div>
            </div>

            <!-- Withdrawal Details -->
            <div class="withdrawal-details">
                <h3>💰 Withdrawal Details</h3>
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
                        <span style="background-color: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                            PENDING
                        </span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Submitted Date:</span>
                    <span class="detail-value">{{ $withdraw->created_at->format('M d, Y - h:i A') }}</span>
                </div>
            </div>

            <!-- Action Button -->
            <div style="text-align: center;">
                <a href="{{ url('/admin/withdraw/request/all') }}" class="action-button" style="color: white;">
                    Review Withdrawal Request
                </a>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <p style="font-size: 14px; color: #666;">
                    Please process this request within 24 hours to maintain good service quality.
                </p>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p><strong>Right Freelancer Admin Panel</strong></p>
            <p>Managing withdrawal requests efficiently</p>
            <p style="font-size: 12px; color: #999; margin-top: 20px;">
                This notification was sent on {{ now()->format('M d, Y - h:i A') }}
            </p>
        </div>
    </div>
</body>
</html>
