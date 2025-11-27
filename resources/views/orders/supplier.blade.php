@extends('layouts.app')
@section('title', 'Supplier Order')
@section('content')
<div class="container mt-4">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background: transparent; padding: 2%;">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none" style="color: #0bb364;">
                    <i class="fas fa-home me-1"></i>Home
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="#" class="text-decoration-none" style="color: #0bb364;">
                    <i class="fas fa-store me-1"></i>Supplier Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-clipboard-list me-1"></i>Product Orders
            </li>
        </ol>
    </nav>

    {{-- Page Title --}}
    <h2 class="fw-bold mb-4" style="color: #333;">Product Orders</h2>

    {{-- Quick Stats --}}
    @php
        $allOrders = $orders;
        
        // To Pack: Orders with status 'Packed'
        $toPackOrders = $orders->filter(fn($order) => $order->status === 'Packed');
        
        // To Ship: Orders with at least one product in 'Pending' status
        $toShipOrders = $orders->filter(function($order) {
            return $order->products->some(fn($p) => $p->pivot->product_status === 'Pending');
        });
        
        // To Deliver: Orders with at least one product in 'Shipped' status
        $toReceiveOrders = $orders->filter(function($order) {
            return $order->products->some(fn($p) => $p->pivot->product_status === 'Shipped');
        });
        
        // Completed: All products are delivered
        $completedOrders = $orders->filter(function($order) {
            return $order->products->every(fn($p) => $p->pivot->product_status === 'Delivered');
        });
        
        $cancelledOrders = $orders->filter(fn($order) => $order->status === 'Cancelled');
        $refundOrders = $orders->filter(fn($order) => in_array($order->refund_status, ['Pending', 'Approved', 'Rejected']));
        
        $totalOrders = $allOrders->count();
        $toPackCount = $toPackOrders->count();
        $pendingOrders = $toShipOrders->count();
        $completedCount = $completedOrders->count();
        $cancelCount = $cancelledOrders->count();
        $refundCount = $refundOrders->count();
    @endphp

    @if($totalOrders > 0)
    <div class="row g-4 mb-4">
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-shopping-bag text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #0bb364;">{{ $totalOrders }}</h4>
                    <small class="text-muted">Total Orders</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-box text-secondary" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #6c757d;">{{ $toPackCount }}</h4>
                    <small class="text-muted">Pick Up</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-clock text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #ffc107;">{{ $pendingOrders }}</h4>
                    <small class="text-muted">To Ship</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #28a745;">{{ $completedCount }}</h4>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-ban text-danger" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #dc3545;">{{ $cancelCount }}</h4>
                    <small class="text-muted">Cancelled</small>
                </div>
            </div>
        </div> 
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-undo-alt text-info" style="font-size: 2rem;"></i>
                    </div> 
                    <h4 class="fw-bold mb-1" style="color: #0dcaf0;">{{ $refundCount }}</h4>
                    <small class="text-muted">Refund / Return</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Tabbed Interface --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 8px;">
        <div class="card-body p-0">
            <ul class="nav nav-tabs border-0" id="orderTabs" role="tablist" style="border-bottom: 2px solid #f5f5f5;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4 py-3 border-0 fw-semibold" 
                            id="all-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#all" 
                            type="button" 
                            role="tab"
                            aria-controls="all"
                            aria-selected="true">
                        All ({{ $allOrders->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3 border-0 fw-semibold" 
                            id="to-pack-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#to-pack" 
                            type="button" 
                            role="tab"
                            aria-controls="to-pack"
                            aria-selected="false">
                        Pick Up ({{ $toPackOrders->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3 border-0 fw-semibold" 
                            id="to-ship-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#to-ship" 
                            type="button" 
                            role="tab"
                            aria-controls="to-ship"
                            aria-selected="false">
                        To Ship ({{ $toShipOrders->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3 border-0 fw-semibold" 
                            id="to-receive-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#to-receive" 
                            type="button" 
                            role="tab"
                            aria-controls="to-receive"
                            aria-selected="false">
                        To Deliver ({{ $toReceiveOrders->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3 border-0 fw-semibold" 
                            id="completed-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#completed" 
                            type="button" 
                            role="tab"
                            aria-controls="completed"
                            aria-selected="false">
                        Completed ({{ $completedOrders->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3 border-0 fw-semibold" 
                            id="cancelled-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#cancelled" 
                            type="button" 
                            role="tab"
                            aria-controls="cancelled"
                            aria-selected="false">
                        Cancelled ({{ $cancelledOrders->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3 border-0 fw-semibold" 
                            id="refund-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#refund" 
                            type="button" 
                            role="tab"
                            aria-controls="refund"
                            aria-selected="false">
                        Return/Refund ({{ $refundOrders->count() }})
                    </button>
                </li>
            </ul>

            {{-- Tab Content --}}
            <div class="tab-content p-3" id="orderTabsContent">
                {{-- All Orders --}}
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    @include('orders.partials.supplier-order-list', ['filteredOrders' => $allOrders])
                </div>

                {{-- To Pack --}}
                <div class="tab-pane fade" id="to-pack" role="tabpanel" aria-labelledby="to-pack-tab">
                    @include('orders.partials.supplier-order-list', ['filteredOrders' => $toPackOrders])
                </div>

                {{-- To Ship --}}
                <div class="tab-pane fade" id="to-ship" role="tabpanel" aria-labelledby="to-ship-tab">
                    @include('orders.partials.supplier-order-list', ['filteredOrders' => $toShipOrders])
                </div>

                {{-- To Receive --}}
                <div class="tab-pane fade" id="to-receive" role="tabpanel" aria-labelledby="to-receive-tab">
                    @include('orders.partials.supplier-order-list', ['filteredOrders' => $toReceiveOrders])
                </div>

                {{-- Completed --}}
                <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                    @include('orders.partials.supplier-order-list', ['filteredOrders' => $completedOrders])
                </div>

                {{-- Cancelled --}}
                <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                    @include('orders.partials.supplier-order-list', ['filteredOrders' => $cancelledOrders])
                </div>

                {{-- Return/Refund --}}
                <div class="tab-pane fade" id="refund" role="tabpanel" aria-labelledby="refund-tab">
                    @include('orders.partials.supplier-order-list', ['filteredOrders' => $refundOrders])
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
    :root {
        --supplier-primary: #0bb364;
        --supplier-hover: #5a6fd8;
    }

    /* Tab Styling */
    .nav-tabs .nav-link {
        color: #555;
        position: relative;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        color: var(--supplier-primary);
    }

    .nav-tabs .nav-link.active {
        color: var(--supplier-primary);
        background: transparent;
    }

    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--supplier-primary);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0bb364;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #0bb364, #764ba2);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(45deg, #5a6fd8, #0bb364);
        transform: translateY(-1px);
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .form-check-input:checked {
        background-color: #0bb364;
        border-color: #0bb364;
    }

    /* Tab Content - Ensure visibility */
    .tab-content > .tab-pane {
        display: none;
    }

    .tab-content > .active {
        display: block;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .nav-tabs {
            overflow-x: auto;
            flex-wrap: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        .nav-tabs .nav-link {
            white-space: nowrap;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }

        .card-body {
            padding: 1rem;
        }
        
        .table-responsive {
            font-size: 0.9rem;
        }
        
        .btn {
            font-size: 0.9rem;
        }
    }
body, 
h1, h2, h3, h4, h5, h6, 
p, span, a, div, input, select, button, label {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
}
</style>

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Tab initialization started');
        
        // Get all tab triggers
        const tabButtons = document.querySelectorAll('#orderTabs button[data-bs-toggle="tab"]');
        
        console.log('Found ' + tabButtons.length + ' tab buttons');
        
        // Add click event listeners
        tabButtons.forEach(function(tabButton) {
            tabButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('data-bs-target');
                console.log('Tab clicked:', this.id, 'Target:', targetId);
                
                // Remove active class from all tabs
                tabButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-selected', 'false');
                });
                
                // Remove active class from all tab panes
                const tabPanes = document.querySelectorAll('.tab-pane');
                tabPanes.forEach(pane => {
                    pane.classList.remove('show', 'active');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                
                // Show corresponding tab pane
                const targetPane = document.querySelector(targetId);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                    console.log('Activated pane:', targetId);
                } else {
                    console.error('Target pane not found:', targetId);
                }
                
                // Save active tab to localStorage
                localStorage.setItem('activeSupplierOrderTab', this.id);
            });
        });

        // Restore active tab from localStorage
        const savedTab = localStorage.getItem('activeSupplierOrderTab');
        if (savedTab) {
            const savedButton = document.getElementById(savedTab);
            if (savedButton) {
                console.log('Restoring saved tab:', savedTab);
                savedButton.click();
            }
        }
        
        console.log('Tab initialization complete');
    });

    // Update individual product status
    function updateIndividualStatus(orderId, productId, status) {
        if (!status) return;
        
        // Create a mini form to update individual status
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/supplier/orders/${orderId}/product/${productId}/status`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }
        
        // Add method override
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);
        
        // Add status
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'product_status';
        statusInput.value = status;
        form.appendChild(statusInput);
        
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush
@endsection