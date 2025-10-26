{{-- resources/views/buyer/reports/pdf/spending.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #667eea; }
        .header h1 { color: #667eea; margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .summary-box { background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px; border-left: 4px solid #667eea; }
        .summary-box h3 { margin: 0 0 10px 0; color: #667eea; font-size: 14px; }
        .summary-grid { display: table; width: 100%; }
        .summary-item { display: table-cell; padding: 8px; width: 25%; }
        .summary-label { font-size: 10px; color: #666; margin-bottom: 3px; }
        .summary-value { font-size: 14px; font-weight: bold; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #667eea; color: white; padding: 10px; text-align: left; font-size: 11px; font-weight: bold; }
        td { padding: 8px; border-bottom: 1px solid #ddd; font-size: 10px; vertical-align: top; }
        tr:nth-child(even) { background: #f8f9fa; }
        .status-badge { padding: 3px 8px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-shipped { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .text-right { text-align: right; }
        .text-success { color: #28a745; font-weight: bold; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p><strong>User:</strong> {{ $summary['user_name'] }}</p>
        <p><strong>Report Period:</strong> {{ $summary['date_from'] }} to {{ $summary['date_to'] }}</p>
        <p><strong>Generated:</strong> {{ $date }}</p>
    </div>

    <div class="summary-box">
        <h3>Spending Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Orders</div>
                <div class="summary-value">{{ number_format($summary['total_orders']) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Spent</div>
                <div class="summary-value text-success">₱{{ number_format($summary['total_spent'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Avg Order Value</div>
                <div class="summary-value">₱{{ number_format(collect($monthlySpending)->avg('average_order_value'), 2) }}</div>
            </div>
        </div>
    </div>

    <h3 style="color: #667eea; margin-top: 20px;">Monthly Spending</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Month</th>
                <th class="text-right">Total Spent</th>
                <th class="text-right">Orders</th>
                <th class="text-right">Avg Order Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthlySpending as $index => $month)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $month['month'] }}</td>
                <td class="text-right text-success">₱{{ number_format($month['total_spent'], 2) }}</td>
                <td class="text-right">{{ $month['orders'] }}</td>
                <td class="text-right">₱{{ number_format($month['average_order_value'], 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px; color: #666;">No spending data found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="color: #667eea; margin-top: 20px;">Top Purchased Products</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Total Spent</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product->product_name }}</td>
                <td class="text-right">{{ $product->total_quantity }}</td>
                <td class="text-right text-success">₱{{ number_format($product->total_spent, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px; color: #666;">No products found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="color: #667eea; margin-top: 20px;">Payment Methods</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Payment Method</th>
                <th class="text-right">Orders</th>
                <th class="text-right">Total Spent</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paymentMethods as $index => $payment)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $payment->payment_method }}</td>
                <td class="text-right">{{ $payment->count }}</td>
                <td class="text-right text-success">₱{{ number_format($payment->total, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px; color: #666;">No payment data found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated report. Generated on {{ $date }}</p>
        <p>© {{ date('Y') }} FishMarket - All Rights Reserved</p>
    </div>
</body>
</html>
