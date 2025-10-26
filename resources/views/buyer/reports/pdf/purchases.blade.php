<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-box {
            background: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }
        .summary-box h3 {
            margin: 0 0 10px 0;
            color: #667eea;
            font-size: 14px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            padding: 8px;
            width: 25%;
        }
        .summary-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #667eea;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .status-completed { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-shipped { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .text-right {
            text-align: right;
        }
        .text-success {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p><strong>Customer:</strong> {{ $summary['user_name'] }} ({{ $summary['user_email'] }})</p>
        <p><strong>Report Period:</strong> {{ $summary['date_from'] }} to {{ $summary['date_to'] }}</p>
        <p><strong>Generated:</strong> {{ $date }}</p>
    </div>

    <div class="summary-box">
        <h3>Purchase Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Orders</div>
                <div class="summary-value">{{ number_format($summary['total_orders']) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Completed Orders</div>
                <div class="summary-value">{{ number_format($summary['completed_orders']) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Spent</div>
                <div class="summary-value text-success">₱{{ number_format($summary['total_spent'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Avg Order Value</div>
                <div class="summary-value">₱{{ number_format($summary['average_order_value'], 2) }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Products</th>
                <th>Product Names</th>
                <th class="text-right">Amount</th>
                <th>Payment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td><strong>#{{ $order['order_id'] }}</strong></td>
                <td>{{ \Carbon\Carbon::parse($order['order_date'])->format('M j, Y') }}</td>
                <td>{{ $order['products_count'] }}</td>
                <td style="font-size: 9px;">{{ \Illuminate\Support\Str::limit($order['product_names'], 40) }}</td>
                <td class="text-right text-success">₱{{ number_format($order['total_amount'], 2) }}</td>
                <td>{{ $order['payment_method'] }}</td>
                <td>
                    <span class="status-badge status-{{ strtolower($order['status']) }}">
                        {{ $order['status'] }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated report. Generated on {{ $date }}</p>
        <p>© {{ date('Y') }} FishMarket - All Rights Reserved</p>
    </div>
</body>
</html>