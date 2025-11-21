@extends('layouts.app')
@section('title', 'Buyer Dashboard Analytics')

{{-- Add required CSS --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<div class="dashboard-container py-4">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1">
                <i class="fas fa-chart-pie text-primary me-2"></i>My Shopping Dashboard
            </h2>
            <p class="text-muted">Track your purchases and spending analytics</p>
        </div>
    </div>

    {{-- Key Metrics Cards --}}
    <div class="row mb-5">
        {{-- Total Orders Card --}}
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon orders-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="metric-value">{{ number_format($totalOrders) }}</div>
                        <div class="metric-label">Total Orders</div>
                        <div class="metric-stats">
                            <span class="text-success">{{ $completedOrders }} completed</span> • 
                            <span class="text-warning">{{ $ongoingOrders }} ongoing</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Spent Card --}}
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon spent-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="metric-value">₱{{ number_format($totalSpent, 2) }}</div>
                        <div class="metric-label">Total Amount Spent</div>
                        <div class="metric-change {{ $spendingGrowth >= 0 ? 'positive' : 'negative' }}">
                            <i class="fas fa-arrow-{{ $spendingGrowth >= 0 ? 'up' : 'down' }}"></i>
                            {{ $spendingGrowth >= 0 ? '+' : '' }}{{ $spendingGrowth }}% from last month
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Spending Card --}}
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon monthly-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="metric-value">₱{{ number_format($monthlySpending, 2) }}</div>
                        <div class="metric-label">This Month</div>
                        <div class="metric-change {{ $spendingGrowth >= 0 ? 'positive' : 'negative' }}">
                            <i class="fas fa-arrow-{{ $spendingGrowth >= 0 ? 'up' : 'down' }}"></i>
                            {{ $spendingGrowth >= 0 ? '+' : '' }}{{ $spendingGrowth }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Average Order Value Card --}}
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon avg-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="metric-value">₱{{ number_format($averageOrderValue, 2) }}</div>
                        <div class="metric-label">Avg Order Value</div>
                        <div class="metric-stats">
                            <span class="text-muted">Per transaction</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    {{-- Charts Section --}}
    <div class="row mb-5">
        {{-- Monthly Spending Trend --}}
        <div class="col-lg-8 mb-4">
            <div class="card chart-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-chart-area me-2 text-primary"></i>Monthly Spending Trends
                    </h5>
                    <p class="text-muted mb-0">Your spending patterns over the last 12 months</p>
                </div>
                <div class="card-body">
                    <canvas id="monthlySpendingChart" style="height:300px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Payment Method Distribution --}}
        <div class="col-lg-4 mb-4">
            <div class="card chart-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-credit-card me-2 text-success"></i>Payment Methods
                    </h5>
                    <p class="text-muted mb-0">Distribution by payment type</p>
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Categories & Recent Orders --}}
    <div class="row mb-5">
        {{-- Top 5 Products --}}
        <div class="col-lg-6 mb-4">
            <div class="card chart-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-chart-bar me-2 text-warning"></i>Top 5 Most Purchased Products
                    </h5>
                    <p class="text-muted mb-0">Your most frequently purchased items</p>
                </div>
                <div class="card-body">
                    <canvas id="topCategoriesChart" style="height:300px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-clock me-2 text-info"></i>Recent Orders
                    </h5>
                    <p class="text-muted mb-0">Your latest purchases</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="border-0">Order #</th>
                                    <th class="border-0">Items</th>
                                    <th class="border-0">Total</th>
                                    <th class="border-0">Payment</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td class="fw-semibold text-primary">#{{ $order->id }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $order->products->count() }} item(s)
                                            </span>
                                        </td>
                                        <td class="fw-bold text-success">₱{{ number_format($order->total_price, 2) }}</td>
                                        <td>
                                            <span class="badge payment-badge payment-{{ strtolower($order->payment_method) }}">
                                                {{ ucfirst($order->payment_method) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge status-badge status-{{ strtolower($order->status) }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="text-muted">{{ $order->created_at->format('M j, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <br>No orders yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Status Breakdown --}}
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="metric-icon-large pending-icon mb-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-2">{{ $pendingOrders }}</h3>
                    <p class="text-muted mb-0">Pending Orders</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="metric-icon-large shipped-icon mb-3">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-2">{{ $shippedOrders }}</h3>
                    <p class="text-muted mb-0">Shipped Orders</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="metric-icon-large delivered-icon mb-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-2">{{ $deliveredOrders }}</h3>
                    <p class="text-muted mb-0">Delivered Orders</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="metric-icon-large cancelled-icon mb-3">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3 class="fw-bold text-danger mb-2">{{ $cancelledOrders }}</h3>
                    <p class="text-muted mb-0">Cancelled Orders</p>
                </div>
            </div>
        </div>
    </div>
{{-- DOWNLOADABLE REPORTS SECTION --}}
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #0b3d64 0%, #0bb364 100%);">
                <div class="card-body text-center py-4">
                    <h5 class="card-title fw-bold mb-1 text-white">
                        <i class="fas fa-file-download me-2"></i>Download My Reports
                    </h5>
                    <p class="text-white-50 mb-0">Export your shopping data and analytics in PDF and CSV format</p>
                </div>
            </div>
        </div>

        {{-- Purchase History Report --}}
        <div class="col-lg-4 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-shopping-bag me-2 text-primary"></i>Purchase History
                    </h5>
                    <p class="text-muted mb-0">Complete order history with details</p>
                </div>
                <div class="card-body">
                    <form id="purchasesForm" action="{{ route('buyer.reports.download.purchases') }}" method="GET" class="report-form">
                        <div class="row mb-3">
                            <div class="col-12 mb-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ date('Y-m-d', strtotime('-1 year')) }}">
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <small><strong>{{ number_format($totalOrders) }}</strong> total orders • <strong>{{ number_format($completedOrders) }}</strong> completed</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-info" onclick="previewReport('purchases', 'pdf')">
                                <i class="fas fa-eye me-2"></i>Preview PDF
                            </button>
                            <button type="submit" name="format" value="pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="submit" name="format" value="csv" class="btn btn-success">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Spending Analysis Report --}}
        <div class="col-lg-4 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-chart-bar me-2 text-success"></i>Spending Analysis
                    </h5>
                    <p class="text-muted mb-0">Monthly spending trends and insights</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('buyer.reports.download.spending') }}" method="GET" class="report-form">
                        <div class="row mb-3">
                            <div class="col-12 mb-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ date('Y-m-d', strtotime('-1 year')) }}">
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-wallet me-2"></i>
                            <small>Total Spent: <strong>₱{{ number_format($totalSpent, 2) }}</strong></small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-info" onclick="previewReport('spending', 'pdf')">
                                <i class="fas fa-eye me-2"></i>Preview PDF
                            </button>
                            <button type="submit" name="format" value="pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="submit" name="format" value="csv" class="btn btn-success">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- My Reviews Report --}}
        <div class="col-lg-4 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-star me-2 text-warning"></i>My Reviews
                    </h5>
                    <p class="text-muted mb-0">All your product reviews and ratings</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('buyer.reports.download.reviews') }}" method="GET" class="report-form">
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-star me-2"></i>
                            <small>You have written <strong>{{ \App\Models\Review::where('user_id', auth()->id())->count() }}</strong> reviews</small>
                        </div>
                        <p class="text-muted mb-3" style="font-size: 0.9rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            Includes all your product ratings and feedback
                        </p>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-info" onclick="previewReport('reviews', 'pdf')">
                                <i class="fas fa-eye me-2"></i>Preview PDF
                            </button>
                            <button type="submit" name="format" value="pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="submit" name="format" value="csv" class="btn btn-success">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Preview Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i>Report Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading preview...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Close
                </button>
                <button type="button" class="btn btn-primary" id="downloadFromPreview">
                    <i class="fas fa-download me-2"></i>Download Report
                </button>
            </div>
        </div>
    </div>
</div>
{{-- Custom Styles --}}
<style>
    .dashboard-container {
        min-height: 100vh;
        padding: 2rem 0;
        margin-top: 50px;
    }

    .metric-card {
        border-radius: 15px;
        transition: all 0.3s ease;
        background: white;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }

    .metric-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.5rem;
        color: white;
    }

    .orders-icon { background: linear-gradient(45deg, #fd7e14, #e83e8c); }
    .spent-icon { background: linear-gradient(45deg, #28a745, #20c997); }
    .monthly-icon { background: linear-gradient(45deg, #007bff, #6f42c1); }
    .avg-icon { background: linear-gradient(45deg, #17a2b8, #6610f2); }

    .metric-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.2;
    }

    .metric-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .metric-change {
        font-size: 0.8rem;
        font-weight: 600;
    }

    .metric-change.positive { color: #28a745; }
    .metric-change.negative { color: #dc3545; }

    .metric-stats {
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    .report-card {
        border-radius: 15px;
        background: white;
        transition: all 0.3s ease;
    }

    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }

    .report-form .btn {
        font-weight: 600;
        padding: 0.75rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .report-form .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .form-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.625rem 0.75rem;
    }

    .form-control:focus {
        border-color: #088a50;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }

    .alert {
        border-radius: 10px;
        border: none;
    }

    .chart-card {
        border-radius: 15px;
        background: white;
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        border-radius: 10px;
    }

    .status-pending { background-color: #ffc107; color: #212529; }
    .status-shipped { background-color: #17a2b8; color: white; }
    .status-delivered { background-color: #28a745; color: white; }
    .status-cancelled { background-color: #dc3545; color: white; }

    .payment-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        border-radius: 10px;
    }

    .payment-cod { background-color: #ffc107; color: #212529; }
    .payment-pickup { background-color: #17a2b8; color: white; }

    .metric-icon-large {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 1.8rem;
        color: white;
    }

    .pending-icon { background: linear-gradient(45deg, #ffc107, #fd7e14); }
    .shipped-icon { background: linear-gradient(45deg, #17a2b8, #007bff); }
    .delivered-icon { background: linear-gradient(45deg, #28a745, #20c997); }
    .cancelled-icon { background: linear-gradient(45deg, #dc3545, #e83e8c); }

    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
    }

    .card-header {
        padding: 1.5rem 1.5rem 0.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    @media (max-width: 768px) {
        .metric-value {
            font-size: 1.4rem;
        }
        
        .metric-icon {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }
        
        .dashboard-container {
            padding: 1rem 0;
        }
    }
        #previewContent iframe {
        width: 100%;
        height: 70vh;
        border: none;
        border-radius: 8px;
    }

    .modal-xl {
        max-width: 1200px;
    }

    @media (max-width: 768px) {
        .metric-value {
            font-size: 1.4rem;
        }
        
        .metric-icon {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }
        
        .reports-container {
            padding: 1rem 0;
        }
        
        .display-4 {
            font-size: 2rem;
        }

        .info-box {
            margin-bottom: 1rem;
        }

        #previewContent iframe {
            height: 50vh;
        }
    }
body, 
h1, h2, h3, h4, h5, h6, 
p, span, a, div, input, select, button, label {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
}
</style>

{{-- Chart Scripts --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.plugins.legend.labels.usePointStyle = true;

    // Monthly Spending Chart
    const monthlySpendingCtx = document.getElementById('monthlySpendingChart').getContext('2d');
    const monthlySpendingData = @json($monthlySpendingData);

    new Chart(monthlySpendingCtx, {
        type: 'line',
        data: {
            labels: monthlySpendingData.map(item => item.month),
            datasets: [{
                label: 'Amount Spent (₱)',
                data: monthlySpendingData.map(item => item.amount),
                borderColor: '#088a50',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#088a50',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 3,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Spent: ₱${context.parsed.y.toLocaleString()}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Payment Method Chart
    const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
    const paymentMethodData = @json($paymentMethodData);

    new Chart(paymentMethodCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(paymentMethodData),
            datasets: [{
                data: Object.values(paymentMethodData),
                backgroundColor: ['#ffc107', '#17a2b8'],
                borderWidth: 3,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} orders (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Top Categories Chart
    const topCategoriesCtx = document.getElementById('topCategoriesChart').getContext('2d');
    const topCategoriesData = @json($topCategoriesData);

    new Chart(topCategoriesCtx, {
        type: 'bar',
        data: {
            labels: topCategoriesData.map(item => item.category),
            datasets: [{
                label: 'Orders',
                data: topCategoriesData.map(item => item.count),
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
                    'rgba(232, 62, 140, 0.8)'
                ],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Orders: ${context.parsed.x}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
    let currentReportType = '';
    let currentFormat = '';
    let currentFormData = null;

    function getFormData(reportType) {
        // Map report types to correct form IDs
        let formId;
        
        switch(reportType) {
            case 'purchases':
                formId = 'purchasesForm';
                break;
            case 'spending':
                formId = 'spendingForm';
                break;
            case 'reviews':
                formId = 'reviewsForm';
                break;
            default:
                console.error('Unknown report type:', reportType);
                return '';
        }
        
        const form = document.getElementById(formId);
        
        if (!form) {
            console.error('Form not found:', formId);
            return '';
        }
        
        const formData = new FormData(form);
        return new URLSearchParams(formData).toString();
    }

    function previewReport(reportType, format) {
        currentReportType = reportType;
        currentFormat = format;
        currentFormData = getFormData(reportType);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();

        // Reset content
        document.getElementById('previewContent').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading preview...</p>
            </div>
        `;

        // Build preview URL
        const previewUrl = `{{ url('buyer/reports/preview') }}/${reportType}?format=${format}&${currentFormData}`;

        console.log('Preview URL:', previewUrl); // For debugging

        // Load preview
        fetch(previewUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.blob();
            })
            .then(blob => {
                const url = URL.createObjectURL(blob);
                if (format === 'pdf') {
                    document.getElementById('previewContent').innerHTML = `
                        <iframe src="${url}" type="application/pdf"></iframe>
                    `;
                } else {
                    // For CSV, show in a table format
                    blob.text().then(text => {
                        const lines = text.split('\n');
                        let tableHtml = '<div class="table-responsive"><table class="table table-striped table-hover table-sm">';
                        
                        lines.forEach((line, index) => {
                            if (line.trim()) {
                                // Handle CSV properly - split by comma but respect quoted values
                                const cells = line.match(/(".*?"|[^",\s]+)(?=\s*,|\s*$)/g) || [];
                                tableHtml += '<tr>';
                                cells.forEach(cell => {
                                    const tag = index === 0 ? 'th' : 'td';
                                    // Remove quotes from CSV cells
                                    const cleanCell = cell.replace(/^"|"$/g, '').trim();
                                    tableHtml += `<${tag}>${cleanCell}</${tag}>`;
                                });
                                tableHtml += '</tr>';
                            }
                        });
                        
                        tableHtml += '</table></div>';
                        document.getElementById('previewContent').innerHTML = tableHtml;
                    });
                }
            })
            .catch(error => {
                console.error('Preview error:', error);
                document.getElementById('previewContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load preview: ${error.message}
                        <br><small>Please check the console for more details.</small>
                    </div>
                `;
            });
    }

    function downloadReport(reportType, format) {
        const formData = getFormData(reportType);
        const baseUrl = '{{ url("buyer/reports/download") }}';
        const downloadUrl = `${baseUrl}/${reportType}?format=${format}&${formData}`;
        
        console.log('Download URL:', downloadUrl); // For debugging
        
        // Create a temporary link and click it to trigger download
        window.location.href = downloadUrl;
    }

    // Download from preview modal
    document.addEventListener('DOMContentLoaded', function() {
        const downloadBtn = document.getElementById('downloadFromPreview');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function() {
                if (currentReportType && currentFormat) {
                    downloadReport(currentReportType, currentFormat);
                }
            });
        }
    });
</script>
@endpush
@endsection