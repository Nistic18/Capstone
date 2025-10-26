{{-- resources/views/admin/reports/pdf/users.blade.php --}}
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
            display: inline-block;
            width: 48%;
            margin-bottom: 10px;
        }
        .summary-label {
            font-weight: bold;
            color: #495057;
        }
        .summary-value {
            color: #667eea;
            font-weight: bold;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #667eea;
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
        .role-badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }
        .role-admin { background-color: #dc3545; color: white; }
        .role-seller { background-color: #28a745; color: white; }
        .role-buyer { background-color: #007bff; color: white; }
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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p><strong>Generated:</strong> {{ $date }}</p>
        <p><strong>Period:</strong> {{ $period }}</p>
    </div>

    <div class="summary-box">
        <h3 style="margin-top: 0; color: #667eea;">Summary Statistics</h3>
        <div class="summary-row">
            <div class="summary-label">Total Users:</div>
            <div class="summary-value">{{ number_format($total_users) }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Total Revenue Generated:</div>
            <div class="summary-value">₱{{ number_format($total_spent, 2) }}</div>
        </div>
    </div>

    <h3 style="color: #667eea; margin-top: 30px;">User Details</h3>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Phone</th>
                <th class="text-right">Orders</th>
                <th class="text-right">Total Spent</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user['id'] }}</strong></td>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td>
                        <span class="role-badge role-{{ strtolower($user['role']) }}">
                            {{ ucfirst($user['role']) }}
                        </span>
                    </td>
                    <td>{{ $user['phone'] }}</td>
                    <td class="text-right">{{ $user['total_orders'] }}</td>
                    <td class="text-right"><strong>₱{{ number_format($user['total_spent'], 2) }}</strong></td>
                    <td>{{ $user['registered_date'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #6c757d;">
                        No users found for the selected period
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