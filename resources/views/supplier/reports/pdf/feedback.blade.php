{{-- resources/views/supplier/reports/pdf/feedback.blade.php --}}
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
        td { padding: 10px 8px; border-bottom: 1px solid #dee2e6; font-size: 11px; vertical-align: top; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .text-success { color: #28a745; font-weight: bold; }
        .text-warning { color: #ffc107; font-weight: bold; }
        .text-danger { color: #dc3545; font-weight: bold; }
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
            <span class="summary-label">Total Reviews:</span>
            <span class="summary-value">{{ $summary['total_reviews'] }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Average Rating:</span>
            <span class="summary-value">{{ $summary['average_rating'] }}/5</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Positive Reviews (4-5 stars):</span>
            <span class="summary-value">{{ $summary['positive_reviews'] }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Negative Reviews (1-2 stars):</span>
            <span class="summary-value">{{ $summary['negative_reviews'] }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Rating Distribution:</span>
            <span class="summary-value">
                5★: {{ $summary['rating_distribution']['5_star'] }},
                4★: {{ $summary['rating_distribution']['4_star'] }},
                3★: {{ $summary['rating_distribution']['3_star'] }},
                2★: {{ $summary['rating_distribution']['2_star'] }},
                1★: {{ $summary['rating_distribution']['1_star'] }}
            </span>
        </div>
    </div>

    <h3 style="color: #667eea; margin-top: 30px;">Customer Reviews</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Customer Name</th>
                <th>Customer Email</th>
                <th class="text-right">Rating</th>
                <th>Comment</th>
                <th>Review Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $index => $review)
                @php
                    $ratingClass = $review['rating'] >= 4 ? 'text-success' : ($review['rating'] <= 2 ? 'text-danger' : 'text-warning');
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $review['product_name'] }}</td>
                    <td>{{ $review['customer_name'] }}</td>
                    <td>{{ $review['customer_email'] }}</td>
                    <td class="text-right {{ $ratingClass }}">{{ $review['rating'] }}</td>
                    <td>{{ $review['comment'] }}</td>
                    <td>{{ $review['review_date'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #6c757d;">
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
