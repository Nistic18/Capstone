{{-- resources/views/supplier/reports/pdf/products.blade.php --}}
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
    </div>

    @php
        // Convert $products collection of arrays to array for safe calculations
        $productsArray = $products->toArray();

        $totalProducts = count($productsArray);
        $totalStock = array_sum(array_column($productsArray, 'current_stock'));
        $totalSold = array_sum(array_column($productsArray, 'total_sold'));
        $totalRevenue = array_sum(array_column($productsArray, 'total_revenue'));
        $avgRating = $totalProducts ? array_sum(array_column($productsArray, 'average_rating')) / $totalProducts : 0;
    @endphp

    <div class="summary-box">
        <h3 style="margin-top: 0; color: #667eea;">Summary</h3>
        <div class="summary-row">
            <span class="summary-label">Total Products:</span>
            <span class="summary-value">{{ number_format($totalProducts) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Stock:</span>
            <span class="summary-value">{{ number_format($totalStock) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Sold:</span>
            <span class="summary-value">{{ number_format($totalSold) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Revenue:</span>
            <span class="summary-value">₱{{ number_format($totalRevenue, 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Average Rating:</span>
            <span class="summary-value">{{ number_format($avgRating, 2) }}/5</span>
        </div>
    </div>

    <h3 style="color: #667eea; margin-top: 30px;">Products Details</h3>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Category</th>
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
            @forelse($productsArray as $index => $product)
                <tr>
                    <td><strong>{{ $index + 1 }}</strong></td>
                    <td>{{ $product['name'] ?? 'N/A' }}</td>
                    <td>{{ $product['category'] ?? 'N/A' }}</td>
                    <td class="text-right">₱{{ number_format($product['price'], 2) }}</td>
                    <td class="text-right">{{ $product['current_stock'] }}</td>
                    <td class="text-right">{{ $product['total_sold'] }}</td>
                    <td class="text-right text-success">₱{{ number_format($product['total_revenue'], 2) }}</td>
                    <td class="text-right">{{ number_format($product['average_rating'], 2) }}/5</td>
                    <td>{{ $product['status'] }}</td>
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
