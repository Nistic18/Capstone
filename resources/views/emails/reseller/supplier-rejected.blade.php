<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseller Application Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        .footer {
            background: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .reason-box {
            background: white;
            border: 1px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Application Status Update</h1>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $application->business_name }}</strong>,</p>

        <p>Thank you for your interest in becoming a reseller with {{ config('app.name') }}. After careful review of your application, we regret to inform you that we are unable to approve your reseller application at this time.</p>

        @if($application->rejection_reason)
        <div class="reason-box">
            <h3 style="margin-top: 0; color: #dc3545;">Reason for Decision:</h3>
            <p style="margin-bottom: 0;">{{ $application->rejection_reason }}</p>
        </div>
        @endif

        <div class="info-box">
            <h3 style="margin-top: 0;">What You Can Do Next:</h3>
            <ul style="margin-bottom: 0;">
                <li>Review the reason provided above</li>
                <li>Address any issues or concerns mentioned</li>
                <li>Contact our support team for clarification</li>
                <li>Reapply after addressing the concerns (if applicable)</li>
            </ul>
        </div>

        <p><strong>Application Details:</strong></p>
        <ul>
            <li>Business Name: {{ $application->business_name }}</li>
            <li>License ID: {{ $application->business_license_id }}</li>
            <li>Submission Date: {{ $application->created_at->format('F d, Y') }}</li>
            <li>Review Date: {{ now()->format('F d, Y') }}</li>
        </ul>

        <center>
            <a href="mailto:support@yourstore.com" class="button">Contact Support</a>
        </center>

        <p>We appreciate your interest in partnering with us. If you believe this decision was made in error or if you have additional information that might affect this decision, please don't hesitate to contact our support team.</p>

        <p>You can continue to shop with us as a regular customer and enjoy our products and services.</p>

        <p>Thank you for your understanding.</p>

        <p>Best regards,<br>
        <strong>The {{ config('app.name') }} Team</strong></p>
    </div>

    <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>Need help? <a href="mailto:fishmarketnotification@gmail.com">Contact Support</a></p>
            <p style="margin-top: 15px; color: #999; font-size: 12px;">
                © {{ date('Y') }} FishMarket. All rights reserved.
            </p>
        </div>
</body>
</html>