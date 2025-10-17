<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseller Application Approved</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .benefit-box {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Congratulations!</h1>
        <h2 style="margin: 10px 0;">Your Application Has Been Approved</h2>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $application->business_name }}</strong>,</p>

        <div class="success-box">
            <h3 style="margin-top: 0; color: #28a745;">✓ Application Approved</h3>
            <p style="margin-bottom: 0;">We are pleased to inform you that your reseller application has been approved! Welcome to our reseller network.</p>
        </div>

        <h3>Your Reseller Benefits:</h3>
        <div class="benefit-box">
            <strong>✓ Wholesale Pricing</strong>
            <p>Access exclusive wholesale prices on all products</p>
        </div>
        <div class="benefit-box">
            <strong>✓ Priority Support</strong>
            <p>Get dedicated support from our reseller team</p>
        </div>
        <div class="benefit-box">
            <strong>✓ Bulk Orders</strong>
            <p>Place large quantity orders with ease</p>
        </div>
        <div class="benefit-box">
            <strong>✓ Marketing Materials</strong>
            <p>Access promotional materials and resources</p>
        </div>

        <center>
            <a href="{{ url('/') }}" class="button">Access Your Dashboard</a>
        </center>

        <h3>Next Steps:</h3>
        <ol>
            <li>Log in to your account to access reseller features</li>
            <li>Review our reseller guidelines and policies</li>
            <li>Start browsing products at wholesale prices</li>
            <li>Contact your account manager for personalized assistance</li>
        </ol>

        <p><strong>Application Details:</strong></p>
        <ul>
            <li>Business Name: {{ $application->business_name }}</li>
            <li>License ID: {{ $application->business_license_id }}</li>
            <li>Approval Date: {{ now()->format('F d, Y') }}</li>
        </ul>

        <p>If you have any questions about your reseller account or need assistance getting started, please don't hesitate to reach out to our support team.</p>

        <p>We're excited to have you as part of our reseller network!</p>

        <p>Best regards,<br>
        <strong>The {{ config('app.name') }} Team</strong></p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>