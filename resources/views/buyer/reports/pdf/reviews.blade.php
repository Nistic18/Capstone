{{-- resources/views/buyer/reports/pdf/reviews.blade.php --}}
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
        .text-right { text-align: right; }
        .text-success { color: #28a745; font-weight: bold; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p><strong>User:</strong> {{ $summary['user_name'] }}</p>
        <p><strong>Generated:</strong> {{ $date }}</p>
    </div>

    <div class="summary-box">
        <h3>Reviews Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Reviews</div>
                <div class="summary-value">{{ $summary['total_reviews'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Average Rating</div>
                <div class="summary-value text-success">{{ number_format($summary['average_rating'], 2) }}/5</div>
            </div>
        </div>
    </div>

    <h3 style="color: #667eea; margin-top: 20px;">Customer Reviews</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th class="text-right">Rating</th>
                <th>Comment</th>
                <th>Review Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $index => $review)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $review['product_name'] }}</td>
                <td class="text-right">{{ $review['rating'] }}/5</td>
                <td>{{ $review['comment'] }}</td>
                <td>{{ $review['review_date'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px; color: #666;">No reviews found</td>
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
