@extends('layouts.app')
@section('title', 'Order Details')
<link rel="icon" type="image/png" href="{{ asset('img/avatar/dried-fish-logo.png') }}">
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
                <a href="{{ route('products.index') }}" class="text-decoration-none" style="color: #667eea;">
                    <i class="fas fa-clipboard-list me-1"></i>Orders
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-receipt me-1"></i>Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
            </li>
        </ol>
    </nav>

    {{-- Cancellation Alert --}}
    @if($order->status === 'Cancelled' && $order->cancel_reason)
        <div class="alert alert-danger mb-4" style="border-radius: 15px; border-left: 5px solid #dc3545;">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="alert-heading fw-bold mb-2">
                        <i class="fas fa-ban me-2"></i>Order Cancelled
                    </h6>
                    <p class="mb-0">
                        <strong>Reason:</strong> {{ $order->cancel_reason }}
                    </p>
                    <small class="text-muted d-block mt-1">
                        <i class="fas fa-info-circle me-1"></i>
                        This order was cancelled and all items have been returned to inventory.
                    </small>
                </div>
            </div>
        </div>
    @endif

    {{-- Refund Status Alerts --}}
    @if($order->refund_status === 'Pending')
        <div class="alert alert-warning mb-4" style="border-radius: 15px; border-left: 5px solid #ffc107;">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="alert-heading fw-bold mb-2">
                        <i class="fas fa-clock me-2"></i>Refund Request Pending
                    </h6>
                    @if($order->refund_reason)
                        <p class="mb-0">
                            <strong>Request Reason:</strong> {{ $order->refund_reason }}
                        </p>
                    @endif
                    <small class="text-muted d-block mt-1">
                        <i class="fas fa-info-circle me-1"></i>
                        Refund request is currently being reviewed.
                    </small>
                </div>
            </div>
        </div>
    @elseif($order->refund_status === 'Approved')
        <div class="alert alert-success mb-4" style="border-radius: 15px; border-left: 5px solid #28a745;">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="alert-heading fw-bold mb-2">
                        <i class="fas fa-thumbs-up me-2"></i>Refund Approved
                    </h6>
                    @if($order->refund_reason)
                        <p class="mb-1">
                            <strong>Request:</strong> {{ $order->refund_reason }}
                        </p>
                    @endif
                    <small class="text-muted d-block mt-1">
                        <i class="fas fa-info-circle me-1"></i>
                        Refund has been approved.
                    </small>
                </div>
            </div>
        </div>
    @elseif($order->refund_status === 'Rejected')
        <div class="alert alert-danger mb-4" style="border-radius: 15px; border-left: 5px solid #dc3545;">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="alert-heading fw-bold mb-2">
                        <i class="fas fa-ban me-2"></i>Refund Request Rejected
                    </h6>
                    @if($order->refund_reason)
                        <p class="mb-1">
                            <strong>Request:</strong> {{ $order->refund_reason }}
                        </p>
                    @endif
                    @if($order->decline_reason)
                        <p class="mb-0 mt-2">
                            <strong>Seller's Response:</strong> {{ $order->decline_reason }}
                        </p>
                    @else
                        <p class="mb-0 mt-2">
                            <em class="text-muted">The seller did not provide a specific reason for rejection.</em>
                        </p>
                    @endif
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        If you have concerns, please contact the seller directly.
                    </small>
                </div>
            </div>
        </div>
    @endif

    {{-- Order Card --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; overflow: hidden;">
        {{-- Header --}}
        <div class="card-header border-0" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 1.5rem;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-1 fw-bold" style="color: #2c3e50;">
                        <i class="fas fa-receipt text-primary me-2"></i>
                        Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                    </h5>
                    <small class="text-muted d-block">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $order->created_at->format('M d, Y h:i A') }}
                    </small>
                    <small class="text-muted d-block">
                        <i class="fas fa-user me-1"></i>
                        Ordered by: {{ $order->user->name }}
                    </small>
                    <small class="text-muted d-block">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        Address: {{ $order->user->address ?? 'N/A' }}
                    </small>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    @php
                        $products = $order->products;
                        $isCancelled = $order->status === 'Cancelled';
                        $isRefunded = $order->refund_status === 'Approved';
                        
                        if ($isCancelled) {
                            $overallStatus = 'Cancelled';
                            $statusColor = 'danger';
                        } elseif ($isRefunded) {
                            $overallStatus = 'Refunded';
                            $statusColor = 'secondary';
                        } else {
                            $hasDeliveredAll = $products->every(fn($p) => $p->pivot->product_status === 'Delivered');
                            $pendingCount = $products->where('pivot.product_status', 'Pending')->count();
                            $overallStatus = $hasDeliveredAll ? 'Completed' : ($pendingCount > 0 ? 'Processing' : 'In Transit');
                            $statusColor = match($overallStatus) {
                                'Completed' => 'success',
                                'In Transit' => 'info',
                                default => 'warning'
                            };
                        }
                    @endphp

                    <span class="badge mb-2"
                          style="background: var(--bs-{{ $statusColor }}); border-radius: 15px; padding: 8px 15px; font-size: 0.9rem;">
                        @if($overallStatus === 'Completed')
                            <i class="fas fa-check-circle me-1"></i>{{ $overallStatus }}
                        @elseif($overallStatus === 'In Transit')
                            <i class="fas fa-truck me-1"></i>{{ $overallStatus }}
                        @elseif($overallStatus === 'Cancelled')
                            <i class="fas fa-times-circle me-1"></i>{{ $overallStatus }}
                        @elseif($overallStatus === 'Refunded')
                            <i class="fas fa-undo me-1"></i>{{ $overallStatus }}
                        @else
                            <i class="fas fa-clock me-1"></i>{{ $overallStatus }}
                        @endif
                    </span>
                    <h4 class="mb-0 fw-bold" style="color: #28a745;">
                        ₱{{ number_format($order->total_price, 2) }}
                    </h4>
                </div>
            </div>
        </div>

        {{-- Products --}}
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-box me-2 text-primary"></i>Products in this Order
            </h6>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="border-0">Product</th>
                            <th class="border-0 text-center">Quantity</th>
                            <th class="border-0 text-center">Unit Price</th>
                            <th class="border-0 text-center">Subtotal</th>
                            <th class="border-0 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->products as $product)
                        <tr>
                            <td class="py-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($product->images && $product->images->count() > 0)
                                            <img src="{{ asset($product->images->first()->image) }}"
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
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">{{ $product->pivot->quantity }}</td>
                            <td class="text-center">₱{{ number_format($product->price, 2) }}</td>
                            <td class="text-center">₱{{ number_format($product->price * $product->pivot->quantity, 2) }}</td>
                            <td class="text-center">
                                @php
                                    $statusBadge = match($product->pivot->product_status) {
                                        'Pending' => ['bg' => 'warning', 'icon' => 'fas fa-clock'],
                                        'Shipped' => ['bg' => 'info', 'icon' => 'fas fa-truck'],
                                        'Delivered' => ['bg' => 'success', 'icon' => 'fas fa-check-circle'],
                                        'Cancelled' => ['bg' => 'danger', 'icon' => 'fas fa-times-circle'],
                                        default => ['bg' => 'secondary', 'icon' => 'fas fa-question-circle']
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusBadge['bg'] }}" style="border-radius: 15px; padding: 8px 12px;">
                                    <i class="{{ $statusBadge['icon'] }} me-1"></i>{{ $product->pivot->product_status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background-color: #f8f9fa;">
                        <tr>
                            <td colspan="5" class="text-end fw-bold py-3">
                                <span class="me-2">Total:</span>
                                <span class="text-success fs-5">₱{{ number_format($order->total_price, 2) }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="card-footer border-0 bg-light p-3">
            <div class="d-flex justify-content-end gap-2">
                @if($order->user_id)
                    <a href="{{ route('chat.index', ['user' => $order->user_id, 'order' => $order->id]) }}" 
                       class="btn btn-sm btn-outline-primary" 
                       style="border-radius: 10px;">
                        <i class="fas fa-comment me-1"></i>Contact {{ auth()->user()->role === 'buyer' ? 'Seller' : 'Buyer' }}
                    </a>
                @endif
                
                <a href="{{ route('supplier.orders') }}" 
                   class="btn btn-sm btn-outline-secondary" 
                   style="border-radius: 10px;">
                    <i class="fas fa-arrow-left me-1"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection