{{-- resources/views/supplier/reports/pdf/delivered-orders.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #667eea; }
        .header h1 { color: #667eea; margin: 0 0 10px 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .summary-box { background: #f8f9fa; padding: 20px; margin-bottom: 30px; border-radius: 8px; border: 1px solid #dee2e6; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .summary-label { font-weight: bold; color: #495057; }
        .summary-value { color: #28a745; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #667eea; color: white; padding: 12px 8px; text-align: left; font-weight: bold; font-size: 10px; }
        td { padding: 10px 8px; border-bottom: 1px solid #dee2e6; font-size: 10px; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .text-success { color: #28a745; font-weight: bold; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 2px solid #dee2e6; text-align: center; color: #6c757d; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p><strong>Generated:</strong> {{ $date }}</p>
        <p><strong>Supplier:</strong> {{ $supplier ?? 'N/A' }}</p>
        <p><strong>Period:</strong> {{ $summary['date_from'] }} to {{ $summary['date_to'] }}</p>
    </div>

    <div class="summary-box">
        <h3 style="margin-top: 0; color: #667eea;">Summary</h3>
        <div class="summary-row">
            <span class="summary-label">Total Orders:</span>
            <span class="summary-value">{{ number_format($summary['total_orders']) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Products Sold:</span>
            <span class="summary-value">{{ number_format($summary['total_products_sold']) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Revenue:</span>
            <span class="summary-value">₱{{ number_format($summary['total_revenue'], 2) }}</span>
        </div>
    </div>

    <h3 style="color: #667eea; margin-top: 30px;">Delivered Orders Details</h3>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Contact</th>
                <th>Products</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Amount</th>
                <th>Payment</th>
                <th>Order Date</th>
                <th>Delivered</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                <tr>
                    <td><strong>{{ $index + 1 }}</strong></td>
                    <td>#{{ $order['order_id'] }}</td>
                    <td>{{ $order['customer_name'] }}</td>
                    <td>{{ $order['customer_phone'] }}</td>
                    <td style="max-width: 150px; font-size: 9px;">{{ $order['products_list'] }}</td>
                    <td class="text-right">{{ $order['total_quantity'] }}</td>
                    <td class="text-right text-success">₱{{ number_format($order['total_amount'], 2) }}</td>
                    <td>{{ $order['payment_method'] }}</td>
                    <td style="font-size: 9px;">{{ \Carbon\Carbon::parse($order['order_date'])->format('M d, Y') }}</td>
                    <td style="font-size: 9px;">{{ \Carbon\Carbon::parse($order['delivery_date'])->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px; color: #6c757d;">
                        No delivered orders found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the system.</p>
        <p>&copy; {{ date('Y') }} FishMarket. All rights reserved.</p>
    </div>
</body>
</html>