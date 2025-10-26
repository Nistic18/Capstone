{{-- resources/views/supplier/reports/pdf/sales-revenue.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #667eea; }
        .header h1 { color: #667eea; margin: 0 0 10px 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .summary-box { background: #f8f9fa; padding: 20px; margin-bottom: 30px; border-radius: 8px; border: 1px solid #dee2e6; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .summary-label { font-weight: bold; color: #495057; }
        .summary-value { color: #28a745; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #667eea; color: white; padding: 12px 8px; text-align: left; font-weight: bold; font-size: 11px; }
        td { padding: 10px 8px; border-bottom: 1px solid #dee2e6; font-size: 11px; }
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
        <p><strong>Date Range:</strong> {{ $summary['date_from'] }} to {{ $summary['date_to'] }}</p>
    </div>

    <div class="summary-box">
        <h3 style="margin-top: 0; color: #667eea;">Summary</h3>
        <div class="summary-row">
            <span class="summary-label">Total Revenue:</span>
            <span class="summary-value">₱{{ number_format($summary['total_revenue'], 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Orders:</span>
            <span class="summary-value">{{ number_format($summary['total_orders']) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Items Sold:</span>
            <span class="summary-value">{{ number_format($summary['total_items_sold']) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Average Order Value:</span>
            <span class="summary-value">₱{{ number_format($summary['average_order_value'], 2) }}</span>
        </div>
    </div>

    <h3 style="color: #667eea; margin-top: 30px;">Monthly Sales</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Month</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Orders</th>
                <th class="text-right">Items Sold</th>
                <th class="text-right">Average Order Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthlySales as $index => $month)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $month['month'] }}</td>
                    <td class="text-right text-success">₱{{ number_format($month['revenue'], 2) }}</td>
                    <td class="text-right">{{ $month['orders'] }}</td>
                    <td class="text-right">{{ $month['items_sold'] }}</td>
                    <td class="text-right">₱{{ number_format($month['average_order_value'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #6c757d;">
                        No sales data found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="color: #667eea; margin-top: 30px;">Top Selling Products</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th class="text-right">Quantity Sold</th>
                <th class="text-right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->product_name }}</td>
                    <td class="text-right">{{ $product->total_quantity }}</td>
                    <td class="text-right text-success">₱{{ number_format($product->total_revenue, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #6c757d;">
                        No top selling products found
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
