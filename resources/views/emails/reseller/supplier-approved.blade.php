<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Approved</title>
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
            color: #28a745;
            margin-bottom: 20px;
        }
        .credentials-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .credentials-box h3 {
            margin-top: 0;
            color: #667eea;
            font-size: 18px;
        }
        .credential-item {
            margin: 12px 0;
            padding: 10px;
            background: white;
            border-radius: 5px;
        }
        .credential-item strong {
            display: block;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .credential-item span {
            font-size: 16px;
            color: #333;
            font-weight: 600;
        }
        .password-highlight {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            border: 2px dashed #ffc107;
            text-align: center;
            margin: 20px 0;
        }
        .password-highlight strong {
            color: #856404;
            font-size: 18px;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning-box p {
            margin: 0;
            color: #856404;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: 600;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .steps {
            margin: 25px 0;
        }
        .step {
            display: flex;
            margin: 15px 0;
            align-items: flex-start;
        }
        .step-number {
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .step-content {
            flex: 1;
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
            <h1>🎉 Congratulations!</h1>
            <p>Your supplier application has been approved</p>
        </div>

        <div class="content">
            <div class="success-icon">✓</div>

            <p>Dear <strong>{{ $user->name }}</strong>,</p>

            <p>We're excited to inform you that your supplier application has been <strong>approved</strong>! You can now access your supplier account and start managing your products.</p>

            @if($defaultPassword)
            <div class="credentials-box">
                <h3>🔐 Your Login Credentials</h3>
                
                <div class="credential-item">
                    <strong>Email Address</strong>
                    <span>{{ $user->email }}</span>
                </div>

                <div class="credential-item">
                    <strong>Temporary Password</strong>
                    <span style="color: #dc3545;">{{ $defaultPassword }}</span>
                </div>
            </div>

            <div class="warning-box">
                <p><strong>⚠️ Important:</strong> Please change your password immediately after your first login for security purposes.</p>
            </div>
            @endif

            <div class="steps">
                <h3>Next Steps:</h3>
                
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <strong>Log in to your account</strong>
                        <p>Use the credentials provided above to access your supplier dashboard.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <strong>Change your password</strong>
                        <p>Navigate to your profile settings and update your password.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <strong>Complete your profile</strong>
                        <p>Add business details, bank information, and shipping preferences.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <strong>Start adding products</strong>
                        <p>Upload your first products and start selling!</p>
                    </div>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="btn">Login to Your Account</a>
            </div>

            <p style="margin-top: 30px;">If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

            <p>Best regards,<br>
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