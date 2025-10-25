@extends('layouts.app')
@section('title', 'Reseller Order')
@section('content')
<div class="container mt-4">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background: transparent; padding: 2%;">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none" style="color: #667eea;">
                    <i class="fas fa-home me-1"></i>Home
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="#" class="text-decoration-none" style="color: #667eea;">
                    <i class="fas fa-store me-1"></i>Supplier Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-clipboard-list me-1"></i>Product Orders
            </li>
        </ol>
    </nav>

    {{-- Page Header --}}
    {{-- <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
        <div class="card-body text-center py-4">
            <div class="mb-3">
                <i class="fas fa-clipboard-list text-white" style="font-size: 2.5rem;"></i>
            </div>
            <h1 class="text-white fw-bold mb-2">📋 Your Product Orders</h1>
            <p class="text-white-50 mb-0">Manage and track orders for your fish products</p>
        </div>
    </div> --}}

    {{-- Quick Stats --}}
    @php
        $activeOrders = $orders->filter(fn($order) => $order->status !== 'Cancelled');
        $totalOrders = $orders->count();
        $pendingOrders = $orders->filter(function($order) {
            return $order->products->some(fn($p) => $p->pivot->product_status === 'Pending');
        })->count();
        $completedOrders = $orders->filter(function($order) {
            return $order->products->every(fn($p) => $p->pivot->product_status === 'Delivered');
        })->count();
        $cancelOrders = $orders->filter(function($order) {
            return $order->products->every(fn($p) => $p->pivot->product_status === 'Cancelled');
        })->count();
    @endphp

    @if($totalOrders > 0)
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-shopping-bag text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #667eea;">{{ $totalOrders }}</h4>
                    <small class="text-muted">Total Orders</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-clock text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #ffc107;">{{ $pendingOrders }}</h4>
                    <small class="text-muted">Pending Orders</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #28a745;">{{ $completedOrders }}</h4>
                    <small class="text-muted">Completed Orders</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-ban text-danger text-success" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: #d41111;">{{ number_format($cancelOrders) }}</h4>
                    <small class="text-muted">Cancelled Orders</small>
                </div>
            </div>
        </div> 
    </div>
    @endif

    @forelse ($orders as $order)
        @php
            $products = $order->products;
            $hasDeliveredAll = $products->every(fn($p) => $p->pivot->product_status === 'Delivered');
            $pendingCount = $products->where('pivot.product_status', 'Pending')->count();
            $shippedCount = $products->where('pivot.product_status', 'Shipped')->count();
            $deliveredCount = $products->where('pivot.product_status', 'Delivered')->count();
            $isCancelled = $order->status === 'Cancelled'; 
            $isRefunded = $order->refund_status === 'Approved';


            // Determine overall order status
        if ($isCancelled) {
            $overallStatus = 'Cancelled';
        } elseif ($hasDeliveredAll) {
            $overallStatus = 'Completed';
        } elseif ($pendingCount > 0) {
            $overallStatus = 'Processing';
        } else {
            $overallStatus = 'In Transit';
        }
            $statusColor = match($overallStatus) {
                'Completed' => 'success',
                'In Transit' => 'info', 
                default => 'warning'
            };
        @endphp

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; overflow: hidden;">
            {{-- Order Header --}}
            <div class="card-header border-0" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 1.5rem;">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-1 fw-bold" style="color: #2c3e50;">
                            <i class="fas fa-receipt text-primary me-2"></i>
                            Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </h5>
                        <div class="d-flex flex-column mt-2">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i> Buyer: {{ $order->user->name }}
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> Address: {{ $order->user->address ?? 'N/A' }}
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ $order->created_at->format('M d, Y') }} at {{ $order->created_at->format('h:i A') }}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                        <div class="d-flex flex-column align-items-md-end">
                            <span class="badge mb-2" 
                                  style="background: var(--bs-{{ $statusColor }}); border-radius: 15px; padding: 8px 15px; font-size: 0.9rem;">
                                @if($overallStatus === 'Completed')
                                    <i class="fas fa-check-circle me-1"></i>{{ $overallStatus }}
                                @elseif($overallStatus === 'In Transit')
                                    <i class="fas fa-truck me-1"></i>{{ $overallStatus }}
                                @else
                                    <i class="fas fa-clock me-1"></i>{{ $overallStatus }}
                                @endif
                            </span>
                            @php
    $paymentMethodFull = match(strtolower($order->payment_method)) {
        'cod' => 'Cash on Delivery',
        'pickup' => 'Pickup',
        default => ucfirst($order->payment_method ?? 'Cash on Delivery'),
    };
@endphp

<h4 class="mb-0 fw-bold" style="color: #28a745;">
    Total: ₱{{ number_format($order->total_price, 2) }}
</h4>
<div class="mt-1">
    <span class="badge bg-light text-dark" style="border-radius: 10px;">
        <i class="fas fa-wallet me-1 text-secondary"></i>
        {{ $paymentMethodFull }}
    </span>
</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Content --}}
            <div class="card-body p-4">
                {{-- Status Summary --}}
                @if(!$hasDeliveredAll && !$isRefunded)
                <div class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 rounded" style="background-color: #fff3cd;">
                                <i class="fas fa-clock text-warning me-3" style="font-size: 1.2rem;"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $pendingCount }}</h6>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 rounded" style="background-color: #cff4fc;">
                                <i class="fas fa-truck text-info me-3" style="font-size: 1.2rem;"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $shippedCount }}</h6>
                                    <small class="text-muted">Shipped</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 rounded" style="background-color: #d1e7dd;">
                                <i class="fas fa-check-circle text-success me-3" style="font-size: 1.2rem;"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $deliveredCount }}</h6>
                                    <small class="text-muted">Delivered</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                    {{-- Footer with button --}}
    <div class="card-footer border-0 bg-light d-flex justify-content-end p-3">
        <a href="{{ route('orders.show', $order->id) }}" 
           class="btn btn-sm btn-outline-primary" 
           style="border-radius: 10px;">
            <i class="fas fa-eye me-1"></i> View Details
        </a>
        @if($order->status === 'Pending')
        <a href="{{ route('orders.generateQR', $order->id) }}" 
   class="btn btn-sm btn-outline-success"
   style="border-radius: 10px;">
    <i class="fas fa-qrcode me-1"></i> Generate QR
</a>
@endif
    </div>

                <form method="POST" action="{{ route('supplier.orders.status.bulk-update', $order->id) }}" 
                      id="orderForm{{ $order->id }}">
                    @csrf
                    @method('PUT')
                    
                    {{-- Products Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="border-0 fw-semibold" style="color: #495057;">
                                        <div class="d-flex align-items-center">
                                            @if(!$hasDeliveredAll && !$isRefunded) 
                                            <input type="checkbox" class="form-check-input me-2" 
                                                   id="selectAll{{ $order->id }}" 
                                                   onchange="toggleAllProducts({{ $order->id }})">
                                            @endif
                                            Product
                                        </div>
                                    </th>
                                    <th class="border-0 fw-semibold text-center" style="color: #495057;">Quantity</th>
                                    <th class="border-0 fw-semibold text-center" style="color: #495057;">Current Status</th>
                                    @if(!$hasDeliveredAll && !$isRefunded)
                                    <th class="border-0 fw-semibold text-center" style="color: #495057;">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                <tr>
                                    <td class="border-0 py-3">
                                        <div class="d-flex align-items-center">
                                            @if(!$hasDeliveredAll && !$isRefunded && $product->pivot->product_status !== 'Delivered')
                                            <input type="checkbox" class="form-check-input me-3 product-checkbox" 
                                                   name="selected_products[]" value="{{ $product->id }}"
                                                   data-order="{{ $order->id }}">
                                            @endif
                                            <div class="me-3">
                                                @if($product->images && $product->images->count() > 0)
                                                    <img src="{{ asset('storage/' . $product->images->first()->image) }}" 
                                                         alt="{{ $product->name }}"
                                                         class="rounded"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-fish text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $product->name }}</h6>
                                                <small class="text-muted">₱{{ number_format($product->price, 2) }} each</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="border-0 py-3 text-center">
                                        <span class="badge bg-light text-dark" style="border-radius: 15px; padding: 8px 12px;">
                                            {{ $product->pivot->quantity }}
                                        </span>
                                    </td>
                                    <td class="border-0 py-3 text-center">
                                        @php
                                            $statusBadge = match($product->pivot->product_status) {
                                                'Pending' => ['bg' => 'warning', 'icon' => 'fas fa-clock'],
                                                'Shipped' => ['bg' => 'info', 'icon' => 'fas fa-truck'],
                                                'Delivered' => ['bg' => 'success', 'icon' => 'fas fa-check-circle'],
                                                'Cancelled' => ['bg' => 'danger', 'icon' => 'fas fa-ban'],
                                                default => ['bg' => 'secondary', 'icon' => 'fas fa-question-circle']
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusBadge['bg'] }}" style="border-radius: 15px; padding: 8px 12px;">
                                            <i class="{{ $statusBadge['icon'] }} me-1"></i>{{ $product->pivot->product_status }}
                                        </span>
                                    </td>
                                    @if(!$hasDeliveredAll && !$isRefunded)
                                    <td class="border-0 py-3 text-center">
    @if($hasDeliveredAll && !$isRefunded)
        <span class="text-success">
            <i class="fas fa-check-circle me-1"></i>Complete
        </span>
    @elseif($isCancelled)
        <span class="text-danger fw-bold">Cancelled</span>
    @else
        <select name="individual_status[{{ $product->id }}]" 
                class="form-select form-select-sm" 
                style="border-radius: 10px; max-width: 120px; margin: 0 auto;"
                onchange="updateIndividualStatus({{ $order->id }}, {{ $product->id }}, this.value)">
            <option value="">Select...</option>
            <option value="Pending" {{ $product->pivot->product_status === 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Shipped" {{ $product->pivot->product_status === 'Shipped' ? 'selected' : '' }}>Shipped</option>
            <option value="Delivered" {{ $product->pivot->product_status === 'Delivered' ? 'selected' : '' }}>Delivered</option>
        </select>
    @endif
</td>
                                    @endif
                                </tr>
                                <input type="hidden" name="product_ids[]" value="{{ $product->id }}">
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                   {{-- Bulk Actions / Status Messages --}}
@if($hasDeliveredAll)
    <div class="alert alert-success border-0 mt-4" style="border-radius: 15px; background-color: #d4edda;">
    <div class="d-flex align-items-center">
        <i class="fas fa-trophy text-dark me-3" style="font-size: 1.5rem;"></i>
        <div>
            <h6 class="mb-1 fw-bold text-dark">Order Completed!</h6>
            <small class="text-muted">All products in this order have been successfully delivered.</small>
        </div>
    </div>
</div>
@elseif($isCancelled)
    <div class="alert alert-danger border-0 mt-4 text-white" style="border-radius: 15px; background-color: #dc3545;">
    <div class="d-flex align-items-center">
        <i class="fas fa-ban text-white me-3" style="font-size: 1.5rem;"></i>
        <div>
            <h6 class="mb-1 fw-bold text-white">Order Cancelled</h6>
            <small class="text-white-50">This order cannot be processed because it has been cancelled.</small>
        </div>
    </div>
</div>

@elseif($isRefunded)
    <div class="alert alert-secondary border-0 mt-4" style="border-radius: 15px;">
    <div class="d-flex align-items-center">
        <i class="fas fa-undo-alt text-dark me-3" style="font-size: 1.5rem;"></i>
        <div>
            <h6 class="mb-1 fw-bold text-dark">Refunded</h6>
            <small class="text-muted">This order has been refunded. Product updates are disabled for this transaction.</small>
        </div>
    </div>
</div>
@else
    <div class="card border-0 mt-4" style="background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 100%); border-radius: 15px;">
        <div class="card-body p-3">
            <h6 class="fw-bold mb-3" style="color: #0d6efd;">
                <i class="fas fa-tools me-2"></i>Bulk Actions
            </h6>
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Update Selected To:</label>
                    <select name="product_status" class="form-select" style="border-radius: 10px; color: #000000;">
                        <option value="">Choose status...</option>
                        <option value="Pending">📋 Pending</option>
                        <option value="Shipped">🚛 Shipped</option>
                        <option value="Delivered">✅ Delivered</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100" 
                            style="border-radius: 10px; background: linear-gradient(45deg, #0d6efd, #6610f2);">
                        <i class="fas fa-sync me-2"></i>Update Selected
                    </button>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-success w-100" 
                            style="border-radius: 10px;"
                            onclick="markAllDelivered({{ $order->id }})">
                        <i class="fas fa-check-double me-2"></i>Mark All Delivered
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
                </form>
@php
    $refundLabel = match($order->refund_status) {
        'Pending' => 'Pending Refund',
        'Approved' => 'Approved Refund',
        'Rejected' => 'Rejected Refund',
        default => '', // <- nothing for null or unknown values
    };

    $refundClass = match($order->refund_status) {
        'Pending' => 'bg-warning',
        'Approved' => 'bg-success',
        'Rejected' => 'bg-danger',
        default => '', // <- empty string, no badge shown
    };

    $refundIcon = match($order->refund_status) {
        'Pending' => 'fas fa-exclamation-circle',
        'Approved' => 'fas fa-check-circle',
        'Rejected' => 'fas fa-times-circle',
        default => '', // <- no icon for null
    };
@endphp

@if($refundLabel)
<div class="mt-3 d-flex align-items-center justify-content-between flex-wrap">
    <span class="badge {{ $refundClass }}" style="padding: 8px 12px; border-radius: 15px;">
        <i class="{{ $refundIcon }} me-1"></i>{{ $refundLabel }}
    </span>

    {{-- ✅ Refund action buttons (only show if pending) --}}
    @if($order->refund_status === 'Pending')
        <div class="d-flex gap-2 mt-2 mt-md-0">
            <form method="POST" action="{{ route('orders.refund.approve', ['order' => $order->id, 'status' => 'Approved']) }}">
                @csrf
                @method('PUT')
                <button type="submit" 
                        class="btn btn-sm btn-success"
                        style="border-radius: 10px;">
                    <i class="fas fa-check-circle me-1"></i>Approve Refund
                </button>
            </form>
            <form method="POST" 
      action="{{ route('orders.refund.decline', ['order' => $order->id, 'status' => 'Rejected']) }}">
    @csrf
    @method('PUT')

    <div class="mb-2">
        <label for="decline_reason" class="form-label fw-bold">Reason for Rejection:</label>
        <textarea name="decline_reason" id="decline_reason" class="form-control" rows="2" required></textarea>
    </div>

    <button type="submit" 
            class="btn btn-sm btn-danger"
            style="border-radius: 10px;">
        <i class="fas fa-times-circle me-1"></i>Reject Refund
    </button>
</form>
        </div>
    @endif
</div>
@endif

            </div>
        </div>
      
    @empty
        {{-- Empty State --}}
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-clipboard-list text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                </div>
                <h3 class="text-muted mb-3">No Product Orders Yet</h3>
                <p class="text-muted mb-4">You don't have any orders for your fish products yet. Keep your inventory fresh and customers will come!</p>
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg" 
                   style="border-radius: 25px; background: linear-gradient(45deg, #667eea, #764ba2); border: none;">
                    <i class="fas fa-plus me-2"></i>Add More Products
                </a>
            </div>
        </div>
    @endforelse
    
</div>

{{-- Custom CSS --}}
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(45deg, #5a6fd8, #6a42a0);
        transform: translateY(-1px);
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    @media (max-width: 768px) {
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
</style>

{{-- JavaScript for Enhanced Functionality --}}
<script>
    // Toggle all products checkbox
    function toggleAllProducts(orderId) {
        const selectAllCheckbox = document.getElementById('selectAll' + orderId);
        const productCheckboxes = document.querySelectorAll(`input[data-order="${orderId}"].product-checkbox`);
        
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }
    
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
    
    // Mark all products as delivered
    function markAllDelivered(orderId) {
        if (confirm('Are you sure you want to mark all products in this order as delivered?')) {
            const form = document.getElementById('orderForm' + orderId);
            const statusSelect = form.querySelector('select[name="product_status"]');
            statusSelect.value = 'Delivered';
            
            // Check all checkboxes
            const checkboxes = form.querySelectorAll('.product-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = true);
            
            form.submit();
        }
    }
    
    // Auto-check "Select All" when all individual checkboxes are checked
    document.addEventListener('DOMContentLoaded', function() {
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const orderId = this.getAttribute('data-order');
                const allCheckboxes = document.querySelectorAll(`input[data-order="${orderId}"].product-checkbox`);
                const checkedCheckboxes = document.querySelectorAll(`input[data-order="${orderId}"].product-checkbox:checked`);
                const selectAllCheckbox = document.getElementById('selectAll' + orderId);
                
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
                }
            });
        });
    });
</script>

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@endsection