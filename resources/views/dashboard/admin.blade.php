@extends('layouts.app')
@section('title', 'Admin Reports & Analytics')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<div class="reports-container py-4">
    {{-- Header --}}
    <div class="card border-0 shadow-lg mb-5 header-card">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="fas fa-chart-bar header-icon"></i>
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">📊 Admin Reports & Analytics</h1>
            <p class="lead text-white-50 mb-0">Generate comprehensive reports and download data</p>
        </div>
    </div>

    {{-- Summary Statistics --}}
    <div class="row mb-5">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon users-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="metric-value">{{ number_format($totalUsers) }}</div>
                        <div class="metric-label">Total Users</div>
                        <div class="metric-change positive">
                            <i class="fas fa-arrow-up"></i>
                            {{ $monthlyUsers }} this month
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon products-icon">
                        <i class="fas fa-fish"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="metric-value">{{ number_format($totalProducts) }}</div>
                        <div class="metric-label">Total Products</div>
                        <div class="metric-stats">
                            <span class="text-muted">Active listings</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon orders-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="metric-value">{{ number_format($totalOrders) }}</div>
                        <div class="metric-label">Total Orders</div>
                        <div class="metric-change positive">
                            <i class="fas fa-arrow-up"></i>
                            {{ $monthlyOrders }} this month
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                            ₱{{ number_format($monthlyRevenue, 2) }} this month
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Generation Section --}}
    <div class="row mb-5">
        {{-- User Reports --}}
        <div class="col-lg-6 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-users me-2 text-primary"></i>User Reports
                    </h5>
                    <p class="text-muted mb-0">Export comprehensive user data and analytics</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.download.users') }}" method="GET" class="report-form">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ date('Y-m-d', strtotime('-1 year')) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="format" value="pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="submit" name="format" value="csv" class="btn btn-success">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
                            </button>
                            <button type="submit" name="format" value="excel" class="btn btn-primary">
                                <i class="fas fa-file-excel me-2"></i>Download Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Product Reports --}}
        <div class="col-lg-6 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-fish me-2 text-success"></i>Product Reports
                    </h5>
                    <p class="text-muted mb-0">Export product inventory and performance data</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.download.products') }}" method="GET" class="report-form">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Includes all products with sales performance metrics</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="format" value="pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="submit" name="format" value="csv" class="btn btn-success">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
                            </button>
                            <button type="submit" name="format" value="excel" class="btn btn-primary">
                                <i class="fas fa-file-excel me-2"></i>Download Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sales Reports --}}
        <div class="col-lg-6 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-chart-line me-2 text-warning"></i>Sales Reports
                    </h5>
                    <p class="text-muted mb-0">Detailed sales transactions and order history</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.download.sales') }}" method="GET" class="report-form">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ date('Y-m-d', strtotime('-1 month')) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="format" value="pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="submit" name="format" value="csv" class="btn btn-success">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
                            </button>
                            <button type="submit" name="format" value="excel" class="btn btn-primary">
                                <i class="fas fa-file-excel me-2"></i>Download Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Feedback Reports --}}
        <div class="col-lg-6 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-star me-2 text-info"></i>Feedback & Rating Reports
                    </h5>
                    <p class="text-muted mb-0">Customer reviews and satisfaction metrics</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.download.feedback') }}" method="GET" class="report-form">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ date('Y-m-d', strtotime('-1 month')) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-star me-2"></i>
                            <small>Average Rating: <strong>{{ number_format($averageRating, 2) }}/5.0</strong> ({{ $totalReviews }} reviews)</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="format" value="pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="submit" name="format" value="csv" class="btn btn-success">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
                            </button>
                            <button type="submit" name="format" value="excel" class="btn btn-primary">
                                <i class="fas fa-file-excel me-2"></i>Download Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Income Summary Reports --}}
        <div class="col-lg-12 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-money-bill-wave me-2 text-danger"></i>Income Summary Report
                    </h5>
                    <p class="text-muted mb-0">Comprehensive financial overview with monthly breakdown and top suppliers</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.download.income-summary') }}" method="GET" class="report-form">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ date('Y-m-d', strtotime('-1 year')) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="alert alert-primary mb-0">
                                    <small class="d-block text-muted">Total Revenue</small>
                                    <strong>₱{{ number_format($totalRevenue, 2) }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-success mb-0">
                                    <small class="d-block text-muted">This Month</small>
                                    <strong>₱{{ number_format($monthlyRevenue, 2) }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-warning mb-0">
                                    <small class="d-block text-muted">Total Orders</small>
                                    <strong>{{ number_format($totalOrders) }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <button type="submit" name="format" value="pdf" class="btn btn-danger flex-fill">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="submit" name="format" value="csv" class="btn btn-success flex-fill">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
                            </button>
                            <button type="submit" name="format" value="excel" class="btn btn-primary flex-fill">
                                <i class="fas fa-file-excel me-2"></i>Download Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title fw-bold mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Report Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <i class="fas fa-file-alt text-primary mb-2"></i>
                                <h6 class="fw-bold">Report Formats</h6>
                                <p class="text-muted mb-0 small">Download reports in PDF, CSV, or Excel format for easy analysis and sharing.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <i class="fas fa-calendar-alt text-success mb-2"></i>
                                <h6 class="fw-bold">Date Filters</h6>
                                <p class="text-muted mb-0 small">Customize your reports by selecting specific date ranges for targeted insights.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <i class="fas fa-chart-pie text-warning mb-2"></i>
                                <h6 class="fw-bold">Detailed Analytics</h6>
                                <p class="text-muted mb-0 small">All reports include comprehensive metrics and statistics for better decision making.</p>
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
    .reports-container {
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

    .users-icon { background: linear-gradient(45deg, #667eea, #0bb364); }
    .products-icon { background: linear-gradient(45deg, #28a745, #20c997); }
    .orders-icon { background: linear-gradient(45deg, #fd7e14, #e83e8c); }
    .revenue-icon { background: linear-gradient(45deg, #007bff, #6f42c1); }

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

    .info-box {
        text-align: center;
        padding: 1.5rem;
        border-radius: 10px;
        background: #f8f9fa;
        height: 100%;
    }

    .info-box i {
        font-size: 2rem;
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
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }

    .alert {
        border-radius: 10px;
        border: none;
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
    }
</style>
@endsection