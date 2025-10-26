{{-- resources/views/admin/reports/pdf/feedback.blade.php --}}
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
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p><strong>Report Period:</strong> {{ $summary['date_from'] }} to {{ $summary['date_to'] }}</p>
        <p><strong>Generated:</strong> {{ $date }}</p>
    </div>

    <div class="summary-box">
        <h3 style="margin-top: 0; color: #667eea;">Summary</h3>
        <div class="summary-row">
            <span class="summary-label">Total Reviews:</span>
            <span class="summary-value">{{ $summary['total_reviews'] }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Average Rating:</span>
            <span class="summary-value">{{ $summary['average_rating'] }}/5</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">5 Stars:</span>
            <span class="summary-value">{{ $summary['rating_distribution']['5_star'] }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">4 Stars:</span>
            <span class="summary-value">{{ $summary['rating_distribution']['4_star'] }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">3 Stars:</span>
            <span class="summary-value">{{ $summary['rating_distribution']['3_star'] }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">2 Stars:</span>
            <span class="summary-value">{{ $summary['rating_distribution']['2_star'] }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">1 Star:</span>
            <span class="summary-value">{{ $summary['rating_distribution']['1_star'] }}</span>
        </div>
    </div>

    <h3 style="color: #667eea; margin-top: 20px;">Customer Reviews</h3>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Customer</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $index => $review)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $review['product_name'] }}</td>
                    <td>{{ $review['customer_name'] }}</td>
                    <td>{{ $review['rating'] }}/5</td>
                    <td>{{ \Illuminate\Support\Str::limit($review['comment'], 50) }}</td>
                    <td>{{ \Carbon\Carbon::parse($review['review_date'])->format('M j, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #6c757d;">
                        No reviews found
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
