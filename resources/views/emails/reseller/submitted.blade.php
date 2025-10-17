<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reseller Application Received</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            background-color: #fff;
            max-width: 600px;
            margin: 30px auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            padding: 25px;
            text-align: center;
        }
        .header h1 {
            font-size: 22px;
            margin: 0;
        }
        .content {
            padding: 25px;
            line-height: 1.6;
        }
        .footer {
            background-color: #f1f1f1;
            padding: 15px;
            text-align: center;
            font-size: 13px;
            color: #777;
        }
        .status {
            background-color: #ffc107;
            color: #333;
            display: inline-block;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="email-container">
    <div class="header">
        <h1>Reseller Application Received</h1>
    </div>

    <div class="content">
        <p>Dear {{ $application->business_name }},</p>

        <p>We have successfully received your reseller application. Our team will review your submission and contact you via email once a decision has been made.</p>

        <p><strong>Application Summary:</strong></p>
        <ul>
            <li><strong>Email:</strong> {{ $application->email_address }}</li>
            <li><strong>Business Name:</strong> {{ $application->business_name }}</li>
            <li><strong>Country:</strong> {{ $application->country }}</li>
            <li><strong>Address:</strong> {{ $application->address }}, {{ $application->city }}, {{ $application->province }}, {{ $application->zip_code }}</li>
            <li><strong>Status:</strong> <span class="status">{{ ucfirst($application->status) }}</span></li>
        </ul>

        <p>Thank you for your interest in becoming an authorized supplier. Please allow 3–5 business days for processing.</p>

        <p>Best regards,<br>
        <strong>YourStore Team</strong></p>
    </div>

    <div class="footer">
        © {{ date('Y') }} YourStore. All rights reserved.
    </div>
</div>

</body>
</html>
