@extends('layouts.app')
@section('title', 'Supplier Dashboard Analytics')

{{-- Add required CSS --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
@endpush

@section('content')
<div class="dashboard-container py-4">
    {{-- Header Section --}}
    {{-- <div class="card border-0 shadow-lg mb-5 header-card">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="fas fa-chart-line header-icon"></i>
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">📊 Supplier Analytics Dashboard</h1>
            <p class="lead text-white-50 mb-0">Track your sales performance and business insights</p>
        </div>
    </div> --}}

    {{-- Key Metrics Cards --}}
    <div class="row mb-5">
        {{-- Total Revenue Card --}}
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon revenue-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="metric-value">₱{{ number_format($totalRevenue, 2) }}</div>
                        <div class="metric-label">Total Revenue</div>
                        <div class="metric-change positive">
                            <i class="fas fa-arrow-up"></i>
                            +{{ $revenueGrowth }}% from last month
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Revenue Card --}}
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon monthly-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="metric-value">₱{{ number_format($monthlyRevenue, 2) }}</div>
                        <div class="metric-label">This Month</div>
                        <div class="metric-change {{ $revenueGrowth >= 0 ? 'positive' : 'negative' }}">
                            <i class="fas fa-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }}"></i>
                            {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                        <div class="metric-change {{ $orderGrowth >= 0 ? 'positive' : 'negative' }}">
                            <i class="fas fa-arrow-{{ $orderGrowth >= 0 ? 'up' : 'down' }}"></i>
                            {{ $orderGrowth >= 0 ? '+' : '' }}{{ $orderGrowth }}% orders this month
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Products Card --}}
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon products-icon">
                        <i class="fas fa-fish"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="metric-value">{{ $totalProducts }}</div>
                        <div class="metric-label">Total Products</div>
                        <div class="metric-stats">
                            <span class="text-success">{{ $activeProducts }} active</span> • 
                            <span class="text-danger">{{ $outOfStockProducts }} out of stock</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="row mb-5">
        {{-- Monthly Sales Chart --}}
        <div class="col-lg-8 mb-4">
            <div class="card chart-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Monthly Sales Trend
                    </h5>
                    <p class="text-muted mb-0">Revenue and order trends over the last 12 months</p>
                </div>
                <div class="card-body">
                    <canvas id="monthlySalesChart" style="height:300px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Order Status Distribution --}}
        <div class="col-lg-4 mb-4">
            <div class="card chart-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-chart-pie me-2 text-success"></i>Order Status
                    </h5>
                    <p class="text-muted mb-0">Distribution of order statuses</p>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Daily Sales & Top Products Section --}}
    <div class="row mb-5">
        {{-- Daily Sales Chart --}}
        <div class="col-lg-7 mb-4">
            <div class="card chart-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-chart-area me-2 text-info"></i>Daily Sales (Last 30 Days)
                    </h5>
                    <p class="text-muted mb-0">Daily revenue and order count</p>
                </div>
                <div class="card-body">
                    <canvas id="dailySalesChart" style="height:300px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-trophy me-2 text-warning"></i>Top Selling Products
                    </h5>
                    <p class="text-muted mb-0">Best performers in the last 30 days</p>
                </div>
                <div class="card-body">
                    <div class="top-products-list">
                        @forelse($topProducts as $index => $product)
                            <div class="product-item d-flex align-items-center mb-3 p-3 rounded">
                                <div class="rank-badge">{{ $index + 1 }}</div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="product-name mb-1">{{ $product->name }}</h6>
                                    <div class="product-stats">
                                        <span class="badge bg-success me-2">{{ $product->total_sold }} sold</span>
                                        <span class="text-success fw-bold">₱{{ number_format($product->total_revenue, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-fish text-muted mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                <p class="text-muted">No sales data available yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Orders & Additional Metrics --}}
    <div class="row mb-5">
        {{-- Recent Orders --}}
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-clock me-2 text-primary"></i>Recent Orders
                    </h5>
                    <p class="text-muted mb-0">Latest orders for your products</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Order #</th>
                                    <th class="border-0">Customer</th>
                                    <th class="border-0">Products</th>
                                    <th class="border-0">Total</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td class="fw-semibold text-primary">#{{ $order->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($order->user->name, 0, 1) }}
                                                </div>
                                                {{ $order->user->name }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $order->products->count() }} item(s)
                                            </span>
                                        </td>
                                        <td class="fw-bold text-success">₱{{ number_format($order->total_price, 2) }}</td>
                                        <td>
                                            <span class="badge status-badge status-{{ strtolower($order->status) }}">
                                                {{ $order->status }}
                                            </span>
                                            {{-- Refund Status (shown only if applicable) --}}
    @if($order->refund_status === 'Approved')
        <div class="mt-1">
            <span class="badge bg-success">Refund Approved</span>
        </div>
    @elseif($order->refund_status === 'Pending')
        <div class="mt-1">
            <span class="badge bg-warning text-dark">Refund Pending</span>
        </div>
    @elseif($order->refund_status === 'Rejected')
        <div class="mt-1">
            <span class="badge bg-danger">Refund Rejected</span>
        </div>
    @endif
                                        </td>
                                        <td class="text-muted">{{ $order->created_at->format('M j, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <br>No recent orders found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional Metrics --}}
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="metric-icon-large aov-icon mb-3">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-2">₱{{ number_format($averageOrderValue, 2) }}</h3>
                    <p class="text-muted mb-0">Average Order Value</p>
                    <small class="text-success">
                        This month: ₱{{ number_format($monthlyAOV, 2) }}
                    </small>
                </div>
            </div>

            {{-- Quick Actions --}}
            {{-- <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-bold mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('products.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Add New Product
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-fish me-2"></i>Manage Products
                        </a> --}}
                        {{-- <a href="{{ route('orders.supplier') }}" class="btn btn-outline-info">
                            <i class="fas fa-shopping-cart me-2"></i>View Orders
                        </a> --}}
                    {{-- </div>
                </div>
            </div> --}}
            <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0" style="height:119px;">
        <div class="card-body text-center">
            <div class="flex-grow-1">
                <div class="metric-value">₱{{ number_format($totalRefunds, 2) }}</div>
                <div class="metric-label">Total Refunds</div>
                <div class="metric-change negative">
                    Refund Rate: {{ $refundRate }}%
                </div>
            </div>
        </div>
    </div>
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

    .header-card {
        background: linear-gradient(135deg, #667eea 0%, #0bb364 100%);
        border-radius: 20px;
    }

    .header-icon {
        font-size: 3rem;
        color: white;
        margin-bottom: 1rem;
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

    .revenue-icon { background: linear-gradient(45deg, #28a745, #20c997); }
    .monthly-icon { background: linear-gradient(45deg, #007bff, #6f42c1); }
    .orders-icon { background: linear-gradient(45deg, #fd7e14, #e83e8c); }
    .products-icon { background: linear-gradient(45deg, #17a2b8, #6610f2); }

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

    .chart-card {
        border-radius: 15px;
        background: white;
    }

    .top-products-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .product-item {
        background: #f8f9fa;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .product-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }

    .rank-badge {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(45deg, #667eea, #0bb364);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .product-name {
        color: #2c3e50;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }

    .product-stats {
        font-size: 0.85rem;
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        border-radius: 10px;
    }

    .status-pending { background-color: #ffc107; color: #212529; }
    .status-processing { background-color: #17a2b8; color: white; }
    .status-delivered { background-color: #28a745; color: white; }
    .status-cancelled { background-color: #dc3545; color: white; }
    .status-refunded { background-color: #dc3545; color: white; }

    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .metric-icon-large {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 2rem;
        color: white;
    }

    .aov-icon {
        background: linear-gradient(45deg, #667eea, #0bb364);
    }

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
        
        .display-4 {
            font-size: 2rem;
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart.js default configuration
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.plugins.legend.labels.usePointStyle = true;

    // Monthly Sales Chart
    const monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
    const monthlySalesData = @json($monthlySalesData);

    new Chart(monthlySalesCtx, {
        type: 'line',
        data: {
            labels: monthlySalesData.map(item => item.month),
            datasets: [
                {
                    label: 'Revenue (₱)',
                    data: monthlySalesData.map(item => item.revenue),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointRadius: 6
                },
                {
                    label: 'Orders',
                    data: monthlySalesData.map(item => item.orders),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#28a745',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return `Revenue: ${context.parsed.y.toLocaleString()}`;
                            } else {
                                return `Orders: ${context.parsed.y}`;
                            }
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });

    // Order Status Chart
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const orderStatusData = @json($orderStatusData);
    const statusLabels = Object.keys(orderStatusData);
    const statusValues = Object.values(orderStatusData);
    const statusColors = {
        'Pending': '#ffc107',
        'Processing': '#17a2b8',
        'Delivered': '#28a745',
        'Cancelled': '#dc3545',
        'Completed': '#28a745',
        'Refunded': '#dc3545'
    };

    new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: statusLabels.map(status => statusColors[status] || '#6c757d'),
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
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Daily Sales Chart
    const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
    const dailySalesData = @json($dailySalesData);

    new Chart(dailySalesCtx, {
        type: 'bar',
        data: {
            labels: dailySalesData.map(item => item.date),
            datasets: [
                {
                    label: 'Daily Revenue',
                    data: dailySalesData.map(item => item.revenue),
                    backgroundColor: 'rgba(102, 126, 234, 0.7)',
                    borderRadius: 6
                },
                {
                    label: 'Daily Orders',
                    data: dailySalesData.map(item => item.orders),
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderRadius: 6,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
