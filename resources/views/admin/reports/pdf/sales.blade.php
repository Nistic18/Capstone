{{-- resources/views/admin/reports/pdf/sales.blade.php --}}
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
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        .header h1 {
            color: #667eea;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-box {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .summary-label {
            font-weight: bold;
            color: #495057;
        }
        .summary-value {
            color: #28a745;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #667eea;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .status-delivered { background-color: #d4edda; color: #155724; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-processing { background-color: #d1ecf1; color: #0c5460; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-success {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p><strong>Generated:</strong> {{ $date }}</p>
        <p><strong>Period:</strong> {{ $summary['date_from'] }} to {{ $summary['date_to'] }}</p>
    </div>

    <div class="summary-box">
        <h3 style="margin-top: 0; color: #667eea;">Summary</h3>
        <div class="summary-row">
            <span class="summary-label">Total Orders:</span>
            <span class="summary-value">{{ number_format($summary['total_orders']) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Revenue:</span>
            <span class="summary-value">₱{{ number_format($summary['total_revenue'], 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Refunds:</span>
            <span class="summary-value" style="color: #dc3545;">₱{{ number_format($summary['total_refunds'], 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Net Revenue:</span>
            <span class="summary-value">₱{{ number_format($summary['net_revenue'], 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Average Order Value:</span>
            <span class="summary-value">₱{{ number_format($summary['average_order_value'], 2) }}</span>
        </div>
    </div>

    <h3 style="color: #667eea; margin-top: 30px;">Order Details</h3>
    
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Products</th>
                <th class="text-right">Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td><strong>#{{ $order['order_id'] }}</strong></td>
                    <td>{{ $order['customer_name'] }}</td>
                    <td>{{ $order['products_count'] }} item(s)</td>
                    <td class="text-right text-success"><strong>₱{{ number_format($order['total_amount'], 2) }}</strong></td>
                    <td>
                        <span class="status-badge status-{{ strtolower($order['status']) }}">
                            {{ $order['status'] }}
                        </span>
                        @if($order['refund_status'] !== 'N/A')
                            <br><small>Refund: {{ $order['refund_status'] }}</small>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($order['order_date'])->format('M j, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #6c757d;">
                        No orders found for the selected period
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the system.</p>
        <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </div>
</body>
</html>