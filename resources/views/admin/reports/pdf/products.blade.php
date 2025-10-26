{{-- resources/views/admin/reports/pdf/products.blade.php --}}
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
        .status-in-stock { background-color: #d4edda; color: #155724; }
        .status-out-of-stock { background-color: #f8d7da; color: #721c24; }
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
        <p><strong>Total Products:</strong> {{ $total_products }}</p>
        <p><strong>Total Revenue:</strong> ₱{{ number_format($total_revenue, 2) }}</p>
    </div>

    <h3 style="color: #667eea; margin-top: 20px;">Product Details</h3>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Supplier</th>
                <th class="text-right">Price</th>
                <th class="text-right">Stock</th>
                <th class="text-right">Sold</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Rating</th>
                <th>Status</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product['name'] }}</td>
                    <td>{{ $product['supplier'] }}</td>
                    <td class="text-right">₱{{ number_format($product['price'], 2) }}</td>
                    <td class="text-right">{{ $product['current_stock'] }}</td>
                    <td class="text-right">{{ $product['total_sold'] }}</td>
                    <td class="text-right text-success">₱{{ number_format($product['total_revenue'], 2) }}</td>
                    <td class="text-right">{{ number_format($product['average_rating'], 2) }}/5</td>
                    <td>
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $product['status'])) }}">
                            {{ $product['status'] }}
                        </span>
                    </td>
                    <td>{{ $product['created_date'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px; color: #6c757d;">
                        No products found
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
