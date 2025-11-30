<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Status Updated</title>
<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin:0; padding:0; background:#f4f4f4; color:#333; }
    .container { max-width:600px; margin:40px auto; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 4px 6px rgba(0,0,0,0.1); }
    .header { background: linear-gradient(135deg, #667eea 0%, #0bb364 100%); color:#fff; text-align:center; padding:40px 20px; }
    .header h1 { margin:0; font-size:28px; }
    .header p { margin:10px 0 0; font-size:16px; opacity:0.9; }
    .content { padding:40px 30px; }
    .status-box { background:#e9ecef; padding:20px; border-radius:5px; text-align:center; margin-bottom:25px; }
    .status-box h2 { margin:0; font-size:22px; color: {{ $statusColor }}; }
    .detail-item { margin:12px 0; padding:10px; background:#f8f9fa; border-radius:5px; }
    .detail-item strong { display:block; font-size:12px; color:#666; text-transform:uppercase; margin-bottom:5px; }
    .detail-item span { font-size:16px; color:#333; }
    .footer { background:#f8f9fa; padding:30px; text-align:center; color:#666; font-size:14px; }
    .footer a { color:#667eea; text-decoration:none; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📦 Order Update!</h1>
        <p>Your order status has been updated</p>
    </div>
    <div class="content">
        <div class="status-box">
            <h2>{{ $order->status }}</h2>
            <p>Order #{{ $orderNumber }}</p>
        </div>
        <div class="detail-item">
            <strong>Customer Name</strong>
            <span>{{ $notifiable->name }}</span>
        </div>
        <div class="detail-item">
            <strong>Total Amount</strong>
            <span>₱{{ $order->total_price }}</span>
        </div>
        <div class="detail-item">
            <strong>Payment Method</strong>
            <span>{{ $order->payment_method }}</span>
        </div>
        <div class="detail-item">
            <strong>Order Date</strong>
            <span>{{ $order->created_at->format('F d, Y \a\t h:i A') }}</span>
        </div>
        <p style="margin-top:25px;">You can view your order details and track progress by clicking the button below.</p>
        <p style="text-align:center; margin-top:20px;">
            <a href="{{ $orderUrl }}" style="background:#667eea;color:#fff;padding:12px 25px;border-radius:5px;text-decoration:none;">View Order</a>
        </p>
        <p style="margin-top:30px;">Thank you for shopping with us!<br><strong>FishMarket Team</strong></p>
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
