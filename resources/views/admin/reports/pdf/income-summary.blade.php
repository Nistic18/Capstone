{{-- resources/views/admin/reports/pdf/income-summary.blade.php --}}
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
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #dc3545;
        }
        .header h1 {
            color: #dc3545;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-box {
            background: linear-gradient(135deg, #667eea 0%, #0bb364 100%);
            color: white;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border-right: 1px solid rgba(255,255,255,0.2);
        }
        .summary-item:last-child {
            border-right: none;
        }
        .summary-label {
            font-weight: normal;
            display: block;
            margin-bottom: 8px;
            opacity: 0.9;
            font-size: 10px;
        }
        .summary-value {
            font-weight: bold;
            font-size: 18px;
        }
        .section-title {
            color: #dc3545;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 16px;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #dc3545;
            color: white;
            padding: 10px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            padding: 8px 6px;
            border-bottom: 1px solid #dee2e6;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-success {
            color: #28a745;
            font-weight: bold;
        }
        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 10px;
        }
        .highlight-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .highlight-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
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
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Gross Revenue</span>
                <div class="summary-value">₱{{ number_format($summary['total_gross_revenue'], 2) }}</div>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Refunds</span>
                <div class="summary-value">₱{{ number_format($summary['total_refunds'], 2) }}</div>
            </div>
            <div class="summary-item">
                <span class="summary-label">Net Revenue</span>
                <div class="summary-value">₱{{ number_format($summary['total_net_revenue'], 2) }}</div>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Orders</span>
                <div class="summary-value">{{ number_format($summary['total_orders']) }}</div>
            </div>
        </div>
    </div>

    <h3 class="section-title">Monthly Income Breakdown</h3>
    
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th class="text-right">Gross Revenue</th>
                <th class="text-right">Refunds</th>
                <th class="text-right">Net Revenue</th>
                <th class="text-right">Orders</th>
                <th class="text-right">Avg Order Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthlyIncome as $month)
                <tr>
                    <td><strong>{{ $month['month'] }}</strong></td>
                    <td class="text-right">₱{{ number_format($month['gross_revenue'], 2) }}</td>
                    <td class="text-right text-danger">₱{{ number_format($month['refunds'], 2) }}</td>
                    <td class="text-right text-success">₱{{ number_format($month['net_revenue'], 2) }}</td>
                    <td class="text-right">{{ number_format($month['orders']) }}</td>
                    <td class="text-right">₱{{ number_format($month['average_order_value'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #6c757d;">
                        No income data found for the selected period
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td>TOTAL</td>
                <td class="text-right">₱{{ number_format($summary['total_gross_revenue'], 2) }}</td>
                <td class="text-right text-danger">₱{{ number_format($summary['total_refunds'], 2) }}</td>
                <td class="text-right text-success">₱{{ number_format($summary['total_net_revenue'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['total_orders']) }}</td>
                <td class="text-right">₱{{ number_format($summary['total_orders'] > 0 ? $summary['total_gross_revenue'] / $summary['total_orders'] : 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <h3 class="section-title">Top Performing Suppliers</h3>
    
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Supplier Name</th>
                <th class="text-right">Total Revenue</th>
                <th class="text-right">Total Orders</th>
                <th class="text-right">Avg Order Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topSuppliers as $index => $supplier)
                <tr>
                    <td><strong>#{{ $index + 1 }}</strong></td>
                    <td>{{ $supplier->supplier_name }}</td>
                    <td class="text-right text-success">₱{{ number_format($supplier->total_revenue, 2) }}</td>
                    <td class="text-right">{{ number_format($supplier->total_orders) }}</td>
                    <td class="text-right">₱{{ number_format($supplier->total_orders > 0 ? $supplier->total_revenue / $supplier->total_orders : 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #6c757d;">
                        No supplier data available
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="highlight-box">
        <div class="highlight-title">Key Insights</div>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Total Net Revenue after refunds: <strong>₱{{ number_format($summary['total_net_revenue'], 2) }}</strong></li>
            <li>Refund Rate: <strong>{{ $summary['total_gross_revenue'] > 0 ? number_format(($summary['total_refunds'] / $summary['total_gross_revenue']) * 100, 2) : 0 }}%</strong></li>
            <li>Average Monthly Revenue: <strong>₱{{ number_format(count($monthlyIncome) > 0 ? $summary['total_gross_revenue'] / count($monthlyIncome) : 0, 2) }}</strong></li>
            <li>Top Supplier Revenue: <strong>₱{{ isset($topSuppliers[0]) ? number_format($topSuppliers[0]->total_revenue, 2) : '0.00' }}</strong></li>
        </ul>
    </div>

    <div class="footer">
        <p>This report was generated automatically by the system.</p>
        <p>&copy; {{ date('Y') }} Fishmarket. All rights reserved.</p>
    </div>
</body>
</html>