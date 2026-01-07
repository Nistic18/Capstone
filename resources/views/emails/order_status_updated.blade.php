<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Update</title>
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
            background: linear-gradient(135deg, #0bb364 0%, #089a52 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin: 15px 0;
            color: white;
        }
        .order-details {
            background: #f9f9f9;
            border-left: 4px solid #0bb364;
            padding: 15px;
            margin: 20px 0;
        }
        .order-details p {
            margin: 8px 0;
        }
        .order-details strong {
            color: #0bb364;
        }
        .refund-alert {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .refund-alert.approved {
            background: #d4edda;
            border-color: #28a745;
        }
        .refund-alert.rejected {
            background: #f8d7da;
            border-color: #dc3545;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #0bb364;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background: #f4f4f4;
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
            <h1>🐟 FishMarket</h1>
            <p style="margin: 5px 0 0 0; font-size: 16px;">Order Status Update</p>
        </div>

        <div class="content">
            <p>Hello {{ $notifiable->name }},</p>

            @php
                // Determine the primary status to display
                $displayStatus = '';
                $displayMessage = '';
                $badgeColor = $statusColor;

                // Check refund status first
                if ($order->refund_status && $order->refund_status !== 'None') {
                    switch($order->refund_status) {
                        case 'Pending':
                            $displayStatus = 'Refund Requested';
                            $displayMessage = 'Your refund request has been submitted and is awaiting approval from the seller.';
                            $badgeColor = '#ffc107';
                            break;
                        case 'Approved':
                            $displayStatus = 'Refund Approved';
                            $displayMessage = 'Your refund request has been approved. The refund will be processed shortly.';
                            $badgeColor = '#28a745';
                            break;
                        case 'Rejected':
                            $displayStatus = 'Refund Rejected';
                            $displayMessage = 'Your refund request has been rejected by the seller.';
                            $badgeColor = '#dc3545';
                            break;
                    }
                } 
                // Check if order was cancelled
                elseif ($order->status === 'Cancelled') {
                    $displayStatus = 'Order Cancelled';
                    $displayMessage = 'Your order has been cancelled as requested.';
                    $badgeColor = '#6c757d';
                }
                // Regular status updates
                else {
                    switch($order->status) {
                        case 'Pending':
                            $displayStatus = 'Order Confirmed';
                            $displayMessage = 'Your order has been confirmed and is being prepared for shipment.';
                            break;
                        case 'Packed':
                            $displayStatus = 'Order Packed';
                            $displayMessage = 'Your order has been packed and is ready for pickup/delivery.';
                            break;
                        case 'Shipped':
                            $displayStatus = 'Order Shipped';
                            $displayMessage = 'Your order is on its way! Please prepare to receive it.';
                            break;
                        case 'Delivered':
                            $displayStatus = 'Order Delivered';
                            $displayMessage = 'Your order has been delivered successfully. We hope you enjoy your purchase!';
                            break;
                        default:
                            $displayStatus = ucfirst($order->status);
                            $displayMessage = 'Your order status has been updated.';
                    }
                }
            @endphp

            <div style="text-align: center;">
                <span class="status-badge" style="background-color: {{ $badgeColor }};">
                    {{ $displayStatus }}
                </span>
            </div>

            <p>{{ $displayMessage }}</p>

            <div class="order-details">
                <p><strong>Order Number:</strong> #{{ $orderNumber }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
                <p><strong>Total Amount:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
                <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'COD') }}</p>
            </div>

            {{-- Show refund reason if exists --}}
            @if($order->refund_status === 'Pending')
                <div class="refund-alert">
                    <strong>📝 Refund Reason:</strong><br>
                    {{ $order->refund_reason }}
                </div>
            @elseif($order->refund_status === 'Approved')
                <div class="refund-alert approved">
                    <strong>✅ Refund Approved</strong><br>
                    Your refund has been approved and will be processed according to our refund policy.
                </div>
            @elseif($order->refund_status === 'Rejected')
                <div class="refund-alert rejected">
                    <strong>❌ Refund Rejected</strong><br>
                    @if($order->refund_reason)
                        <strong>Reason:</strong> {{ $order->refund_reason }}
                    @endif
                </div>
            @endif

            {{-- Show cancellation reason if exists --}}
            @if($order->status === 'Cancelled' && $order->cancel_reason)
                <div class="refund-alert">
                    <strong>📝 Cancellation Reason:</strong><br>
                    {{ $order->cancel_reason }}
                </div>
            @endif

            {{-- Order Items --}}
            <h3 style="color: #0bb364; margin-top: 30px;">Order Items:</h3>
            @foreach($order->products as $product)
                <div style="border-bottom: 1px solid #eee; padding: 15px 0;">
                    <strong>{{ $product->name }}</strong><br>
                    <span style="color: #666;">Quantity: {{ $product->pivot->quantity }}</span><br>
                    <span style="color: #0bb364; font-weight: bold;">₱{{ number_format($product->price, 2) }}</span>
                </div>
            @endforeach

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ $orderUrl }}" class="button">View Order Details</a>
            </div>

            <p style="margin-top: 30px;">
                If you have any questions or concerns about your order, please don't hesitate to contact us.
            </p>

            <p>
                Best regards,<br>
                <strong>The FishMarket Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} FishMarket. All rights reserved.</p>
        </div>
    </div>
</body>
</html>