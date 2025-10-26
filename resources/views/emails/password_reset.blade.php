<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .success-icon {
            text-align: center;
            font-size: 60px;
            margin-bottom: 20px;
            color: #667eea;
        }
        .info-box {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #0c5460;
            font-size: 18px;
        }
        .reset-button {
            display: inline-block;
            padding: 12px 25px;
            margin: 20px 0;
            background: #667eea;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔑 Password Reset Request</h1>
            <p>We received a request to reset your password</p>
        </div>

        <div class="content">
            <div class="success-icon">🔒</div>

            <p>Hi <strong>{{ $user->name ?? 'User' }}</strong>,</p>

            <p>We received a request to reset the password associated with this email address (<strong>{{ $user->email }}</strong>).</p>

            <div class="info-box">
                <h3>Reset Your Password</h3>
                <p>Click the button below to reset your password. This link will expire in <strong>{{ $expiration ?? '60 minutes' }}</strong>.</p>
                <div style="text-align:center;">
                    <a href="{{ $resetUrl }}" class="reset-button">Reset Password</a>
                </div>
            </div>

            <p>If you did not request a password reset, you can safely ignore this email. Your password will remain unchanged.</p>

            <p>Thanks,<br>
            <strong>The Admin Team</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>Need help? <a href="mailto:fishmarketnotification@gmail.com">Contact Support</a></p>
            <p style="margin-top: 15px; color: #999; font-size: 12px;">
                © {{ date('Y') }} FishMarket. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
