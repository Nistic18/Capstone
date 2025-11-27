@extends('layouts.app')
@section('title', 'My Orders')
@section('content')
<div class="container mt-4">
    {{-- SUCCESS BANNER - Dynamic based on action --}}
    @if(session('success'))
        @php
            $isOrderPlaced = str_contains(strtolower(session('success')), 'checkout') || 
                           str_contains(strtolower(session('success')), 'order') ||
                           str_contains(strtolower(session('success')), 'placed');
            $isRefund = str_contains(strtolower(session('success')), 'refund') || 
                       str_contains(strtolower(session('success')), 'return');
        @endphp
        
        <div class="alert alert-success border-0 shadow-lg mb-4 success-banner" 
             style="border-radius: 20px; background: linear-gradient(135deg, {{ $isRefund ? '#17a2b8, #138496' : '#28a745, #20c997' }}); color: white;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="font-size: 2.5rem;">
                        <i class="fas {{ $isRefund ? 'fa-undo-alt' : 'fa-check-circle' }}"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-white">
                            @if($isRefund)
                                🔄 Refund Request Submitted!
                            @elseif($isOrderPlaced)
                                🎉 Order Placed Successfully!
                            @else
                                ✅ Success!
                            @endif
                        </h4>
                        <p class="mb-0">{{ session('success') }}</p>
                        <small class="d-block mt-1 opacity-75">
                            <i class="fas fa-clock me-1"></i>
                            @if($isRefund)
                                Your refund request is being reviewed. We'll contact you soon.
                            @elseif($isOrderPlaced)
                                Your order is being processed. Track your order below.
                            @else
                                Action completed successfully.
                            @endif
                        </small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            
            {{-- Progress Indicator --}}
            <div class="progress mt-3" style="height: 6px; border-radius: 10px; background-color: rgba(255,255,255,0.3);">
                <div class="progress-bar" role="progressbar" style="width: 100%; background-color: white;" 
                     aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" 
             style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle text-danger me-3" style="font-size: 1.5rem;"></i>
                <div>
                    <strong>Error!</strong>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background: transparent; padding: 2%;">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none" style="color: #0bb364;">
                    <i class="fas fa-home me-1"></i>Home
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-shopping-bag me-1"></i>My Orders
            </li>
        </ol>
    </nav>

    {{-- Page Title --}}
    <h2 class="fw-bold mb-4" style="color: #333;">My Orders</h2>

    {{-- Shopee-style Tabs --}}
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
                        To Receive ({{ $toReceiveOrders->count() }})
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
                    @include('orders.partials.order-list', ['filteredOrders' => $allOrders])
                </div>

                {{-- To Pack --}}
                <div class="tab-pane fade" id="to-pack" role="tabpanel" aria-labelledby="to-pack-tab">
                    @include('orders.partials.order-list', ['filteredOrders' => $toPackOrders])
                </div>

                {{-- To Ship --}}
                <div class="tab-pane fade" id="to-ship" role="tabpanel" aria-labelledby="to-ship-tab">
                    @include('orders.partials.order-list', ['filteredOrders' => $toShipOrders])
                </div>

                {{-- To Receive --}}
                <div class="tab-pane fade" id="to-receive" role="tabpanel" aria-labelledby="to-receive-tab">
                    @include('orders.partials.order-list', ['filteredOrders' => $toReceiveOrders])
                </div>

                {{-- Completed --}}
                <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                    @include('orders.partials.order-list', ['filteredOrders' => $completedOrders])
                </div>

                {{-- Cancelled --}}
                <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                    @include('orders.partials.order-list', ['filteredOrders' => $cancelledOrders])
                </div>

                {{-- Return/Refund --}}
                <div class="tab-pane fade" id="refund" role="tabpanel" aria-labelledby="refund-tab">
                    @include('orders.partials.order-list', ['filteredOrders' => $refundOrders])
                </div>
            </div>
        </div>
    </div>

    {{-- Order Summary Stats --}}
    @if ($orders->isNotEmpty())
        @php
            $totalOrders = $orders->count();
            $totalSpent = $orders->where('status', 'Delivered')->sum('total_price');
            $deliveredOrders = $orders->where('status', 'Delivered')->count();
        @endphp
        
        <div class="card border-0 shadow-sm" style="border-radius: 8px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4" style="color: #333;">
                    <i class="fas fa-chart-bar text-primary me-2"></i>Order Statistics
                </h5>
                <div class="row g-4 text-center">
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="fas fa-shopping-bag mb-2" style="font-size: 2rem; color: #0bb364;"></i>
                            <h4 class="fw-bold mb-1" style="color: #0bb364;">{{ $totalOrders }}</h4>
                            <small class="text-muted">Total Orders</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="fas fa-peso-sign mb-2" style="font-size: 2rem; color: #28a745;"></i>
                            <h4 class="fw-bold mb-1" style="color: #28a745;">₱{{ number_format($totalSpent, 2) }}</h4>
                            <small class="text-muted">Total Spent</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="fas fa-check-circle mb-2" style="font-size: 2rem; color: #17a2b8;"></i>
                            <h4 class="fw-bold mb-1" style="color: #17a2b8;">{{ $deliveredOrders }}</h4>
                            <small class="text-muted">Delivered Orders</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Custom CSS --}}
<style>
    :root {
        --shopee-primary: #0bb364;
        --shopee-hover: #0bb364;
    }

    /* Success Banner Animation */
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .success-banner {
        animation: fadeInDown 0.5s ease-out;
    }
    
    .progress-bar {
        animation: progressAnimation 1s ease-out;
    }
    
    @keyframes progressAnimation {
        from { width: 0%; }
        to { width: 100%; }
    }

    /* Tab Styling */
    .nav-tabs .nav-link {
        color: #555;
        position: relative;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        color: var(--shopee-primary);
    }

    .nav-tabs .nav-link.active {
        color: var(--shopee-primary);
        background: transparent;
    }

    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--shopee-primary);
    }

    /* Form Controls */
    .form-control:focus, .form-select:focus {
        border-color: var(--shopee-primary);
        box-shadow: 0 0 0 0.2rem rgba(238, 77, 45, 0.25);
    }

    /* Buttons */
    .btn-primary {
        background-color: var(--shopee-primary);
        border-color: var(--shopee-primary);
    }

    .btn-primary:hover {
        background-color: var(--shopee-hover);
        border-color: var(--shopee-hover);
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    /* Cards */
    .card {
        transition: all 0.2s ease;
    }

    .order-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
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
                localStorage.setItem('activeOrderTab', this.id);
            });
        });

        // Restore active tab from localStorage
        const savedTab = localStorage.getItem('activeOrderTab');
        if (savedTab) {
            const savedButton = document.getElementById(savedTab);
            if (savedButton) {
                console.log('Restoring saved tab:', savedTab);
                savedButton.click();
            }
        }
        
        console.log('Tab initialization complete');
    });
</script>
@endpush
@endsection