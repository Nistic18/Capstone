@extends('layouts.app')
@section('title', 'Supplier Reports & Analytics')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<div class="reports-container py-4">
    @if(session('error'))
    <div class="alert alert-danger text-center">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    </div>
    @endif

    {{-- Summary Statistics --}}
    <div class="row mb-5">
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
                            <span class="text-muted">Your listings</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon orders-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="metric-value">{{ number_format($totalDeliveredOrders) }}</div>
                        <div class="metric-label">Delivered Orders</div>
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

        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon rating-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="metric-value">{{ number_format($averageRating ?? 0, 1) }}/5.0</div>
                        <div class="metric-label">Average Rating</div>
                        <div class="metric-stats">
                            <span class="text-muted">{{ $totalReviews }} reviews</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Generation Section --}}
    <div class="row mb-5">
        {{-- Delivered Orders Report --}}
        <div class="col-lg-6 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex flex-column">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-truck me-2 text-success"></i>Delivered Orders Report
                    </h5>
                    <p class="text-muted mb-0">Export all successfully delivered orders</p>
                </div>
                <div class="card-body">
                    <form id="deliveredOrdersForm" class="report-form">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from', date('Y-m-d', strtotime('-1 year'))) }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to', date('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Includes customer details and order information</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-info" onclick="previewReport('delivered-orders', 'pdf')">
                                <i class="fas fa-eye me-2"></i>Preview PDF
                            </button>
                            <button type="button" class="btn" style="background-color: #ff9800; color: #fff;" onclick="downloadReport('delivered-orders', 'pdf')">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="button" class="btn btn-success" onclick="downloadReport('delivered-orders', 'csv')">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Products Report --}}
        <div class="col-lg-6 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex flex-column">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-boxes me-2 text-primary"></i>Products/Items Report
                    </h5>
                    <p class="text-muted mb-0">Complete list of all your products and performance</p>
                </div>
                <div class="card-body">
                    <form id="productsForm" class="report-form">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Includes sales metrics, ratings, and revenue data</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-info" onclick="previewReport('products', 'pdf')">
                                <i class="fas fa-eye me-2"></i>Preview PDF
                            </button>
                            <button type="button" class="btn" style="background-color: #ff9800; color: #fff;" onclick="downloadReport('products', 'pdf')">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="button" class="btn btn-success" onclick="downloadReport('products', 'csv')">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Inventory Report --}}
        <div class="col-lg-6 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex flex-column">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-warehouse me-2 text-warning"></i>Product Inventory Report
                    </h5>
                    <p class="text-muted mb-0">Stock levels, reorder status, and inventory value</p>
                </div>
                <div class="card-body">
                    <form id="inventoryForm" class="report-form">
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <small>Identifies low stock items and reorder needs</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-info" onclick="previewReport('inventory', 'pdf')">
                                <i class="fas fa-eye me-2"></i>Preview PDF
                            </button>
                            <button type="button" class="btn" style="background-color: #ff9800; color: #fff;" onclick="downloadReport('inventory', 'pdf')">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="button" class="btn btn-success" onclick="downloadReport('inventory', 'csv')">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sales Revenue Report --}}
        <div class="col-lg-6 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex flex-column">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-money-bill-wave me-2 text-info"></i>Total Sales Revenue Report
                    </h5>
                    <p class="text-muted mb-0">Monthly breakdown and top selling products</p>
                </div>
                <div class="card-body">
                    <form id="salesRevenueForm" class="report-form">
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
                            <button type="button" class="btn btn-info" onclick="previewReport('sales-revenue', 'pdf')">
                                <i class="fas fa-eye me-2"></i>Preview PDF
                            </button>
                            <button type="button" class="btn" style="background-color: #ff9800; color: #fff;" onclick="downloadReport('sales-revenue', 'pdf')">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="button" class="btn btn-success" onclick="downloadReport('sales-revenue', 'csv')">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Customer Feedback Report --}}
        <div class="col-lg-12 mb-4">
            <div class="card report-card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex flex-column">
                    <h5 class="card-title fw-bold mb-1">
                        <i class="fas fa-comments me-2 text-danger"></i>Customer Feedback Report
                    </h5>
                    <p class="text-muted mb-0">Reviews, ratings, and customer satisfaction analysis</p>
                </div>
                <div class="card-body">
                    <form id="feedbackForm" class="report-form">
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
                                    <small class="d-block">Average Rating</small>
                                    <strong>{{ number_format($averageRating ?? 0, 2) }}/5.0</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-success mb-0">
                                    <small class="d-block">Total Reviews</small>
                                    <strong>{{ number_format($totalReviews) }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-warning mb-0">
                                    <small class="d-block">Customer Satisfaction</small>
                                    <strong>{{ $averageRating >= 4 ? 'Excellent' : ($averageRating >= 3 ? 'Good' : 'Needs Improvement') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <button type="button" class="btn btn-info flex-fill" onclick="previewReport('feedback', 'pdf')">
                                <i class="fas fa-eye me-2"></i>Preview PDF
                            </button>
                            <button type="button" class="btn flex-fill" style="background-color: #ff9800; color: #fff;" onclick="downloadReport('feedback', 'pdf')">
                                <i class="fas fa-file-pdf me-2"></i>Download PDF
                            </button>
                            <button type="button" class="btn btn-success flex-fill" onclick="downloadReport('feedback', 'csv')">
                                <i class="fas fa-file-csv me-2"></i>Download CSV
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
                <div class="card-header bg-transparent border-0 d-flex flex-column">
                    <h5 class="card-title fw-bold mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Report Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="info-box">
                                <i class="fas fa-file-alt text-primary mb-2"></i>
                                <h6 class="fw-bold">Multiple Formats</h6>
                                <p class="text-muted mb-0 small">Download in PDF and CSV format for easy analysis.</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-box">
                                <i class="fas fa-calendar-alt text-success mb-2"></i>
                                <h6 class="fw-bold">Custom Date Range</h6>
                                <p class="text-muted mb-0 small">Filter reports by specific date ranges for targeted insights.</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-box">
                                <i class="fas fa-chart-bar text-warning mb-2"></i>
                                <h6 class="fw-bold">Detailed Analytics</h6>
                                <p class="text-muted mb-0 small">Comprehensive metrics to track your business performance.</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-box">
                                <i class="fas fa-download text-info mb-2"></i>
                                <h6 class="fw-bold">Instant Download</h6>
                                <p class="text-muted mb-0 small">Generate and download reports instantly with one click.</p>
                            </div>
                        </div>
                    </div>
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

    .products-icon { background: linear-gradient(45deg, #28a745, #20c997); }
    .orders-icon { background: linear-gradient(45deg, #fd7e14, #e83e8c); }
    .revenue-icon { background: linear-gradient(45deg, #007bff, #6f42c1); }
    .rating-icon { background: linear-gradient(45deg, #ffc107, #ff9800); }

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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let currentReportType = '';
    let currentFormat = '';
    let currentFormData = null;

    function getFormData(reportType) {
        // Map report types to correct form IDs
        let formId;
        
        switch(reportType) {
            case 'delivered-orders':
                formId = 'deliveredOrdersForm';
                break;
            case 'products':
                formId = 'productsForm';
                break;
            case 'inventory':
                formId = 'inventoryForm';
                break;
            case 'sales-revenue':
                formId = 'salesRevenueForm';
                break;
            case 'feedback':
                formId = 'feedbackForm';
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

        // Build preview URL (adjust this to match your routes)
        const previewUrl = `{{ url('supplier/reports/preview') }}/${reportType}?format=${format}&${currentFormData}`;

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
        
        // Build the download URL dynamically
        const baseUrl = '{{ url("supplier/reports/download") }}';
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