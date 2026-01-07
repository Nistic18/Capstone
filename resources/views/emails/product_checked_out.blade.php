<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #28a745;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
        </div>
        <div class="content">
            <h2>Hello {{ $notifiable->name }},</h2>
            <p>Thank you for your order! Your product <strong>{{ $product->name }}</strong> has been successfully checked out.</p>
            
            <p><strong>Order Number:</strong> #{{ $orderNumber }}</p>
            
            <p style="text-align: center;">
                <a href="{{ $orderUrl }}" class="button">View Order Details</a>
            </p>
            
            <p>Thank you for shopping with FishMarket!</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} FishMarket. All rights reserved.</p>
        </div>
    </div>
</body>
</html>