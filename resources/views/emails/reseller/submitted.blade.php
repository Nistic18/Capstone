<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Received</title>
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
            background: linear-gradient(135deg, #667eea 0%, #0bb364 100%);
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
        .detail-item {
            margin: 12px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .detail-item strong {
            display: block;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .detail-item span {
            font-size: 16px;
            color: #333;
        }
        .timeline {
            margin: 25px 0;
        }
        .timeline-item {
            display: flex;
            margin: 15px 0;
            align-items: flex-start;
        }
        .timeline-icon {
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .timeline-content {
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
            <h1>📬 Application Received!</h1>
            <p>Thank you for your submission</p>
        </div>

        <div class="content">
            <div class="success-icon">✓</div>

            <p>Dear <strong>{{ $application->business_name }}</strong>,</p>

            <p>We have successfully received your supplier application. Your application is now under review by our team.</p>

            <div class="info-box">
                <h3>📋 Application Details</h3>
                
                <div class="detail-item">
                    <strong>Business Name</strong>
                    <span>{{ $application->business_name }}</span>
                </div>

                <div class="detail-item">
                    <strong>Email Address</strong>
                    <span>{{ $application->email_address }}</span>
                </div>

                <div class="detail-item">
                    <strong>Business License ID</strong>
                    <span>{{ $application->business_license_id }}</span>
                </div>

                <div class="detail-item">
                    <strong>Location</strong>
                    <span>{{ $application->city }}, {{ $application->province }}, {{ $application->country }}</span>
                </div>

                <div class="detail-item">
                    <strong>Submission Date</strong>
                    <span>{{ $application->created_at->format('F d, Y \a\t h:i A') }}</span>
                </div>
            </div>

            <div class="timeline">
                <h3>What Happens Next?</h3>
                
                <div class="timeline-item">
                    <div class="timeline-icon">1</div>
                    <div class="timeline-content">
                        <strong>Application Review</strong>
                        <p style="margin: 5px 0 0; color: #666;">Our team will review your application and documents (typically 2-5 business days).</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon">2</div>
                    <div class="timeline-content">
                        <strong>Verification Process</strong>
                        <p style="margin: 5px 0 0; color: #666;">We may contact you for additional information or clarification if needed.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon">3</div>
                    <div class="timeline-content">
                        <strong>Decision Notification</strong>
                        <p style="margin: 5px 0 0; color: #666;">You'll receive an email with the final decision and next steps.</p>
                    </div>
                </div>
            </div>

            <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin: 25px 0;">
                <p style="margin: 0; color: #856404;"><strong>⏰ Expected Review Time:</strong> 2-5 business days</p>
            </div>

            <p>In the meantime, please ensure that:</p>
            <ul style="color: #666;">
                <li>Your email address ({{ $application->email_address }}) is active and monitored</li>
                <li>Check your spam/junk folder regularly</li>
                <li>All business documents are valid and up-to-date</li>
            </ul>

            <p style="margin-top: 30px;">If you have any questions or need to update your application, please contact our support team.</p>

            <p>Best regards,<br>
            <strong>The Admin Team</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated confirmation email.</p>
            <p>Need help? <a href="mailto:fishmarketnotification@gmail.com">Contact Support</a></p>
            <p style="margin-top: 15px; color: #999; font-size: 12px;">
                © {{ date('Y') }} FishMarket. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>