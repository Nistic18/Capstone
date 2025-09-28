@extends('layouts.app')
@section('title', 'My Order')
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
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-shopping-bag me-1"></i>My Orders
            </li>
        </ol>
    </nav>

    {{-- Page Header --}}
    {{-- <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
        <div class="card-body text-center py-4">
            <div class="mb-3">
                <i class="fas fa-shopping-bag text-white" style="font-size: 2.5rem;"></i>
            </div>
            <h1 class="text-white fw-bold mb-2">📦 My Fish Orders</h1>
            <p class="text-white-50 mb-0">Track your fresh fish deliveries and order history</p>
        </div>
    </div> --}}

    @if ($orders->isEmpty())
        {{-- Empty Orders State --}}
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-bag text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                </div>
                <h3 class="text-muted mb-3">No Orders Yet</h3>
                <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping for fresh fish!</p>
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg" 
                   style="border-radius: 25px; background: linear-gradient(45deg, #667eea, #764ba2); border: none;">
                    <i class="fas fa-fish me-2"></i>Start Shopping
                </a>
            </div>
        </div>
    @else
        {{-- Orders List --}}
        <div class="row g-4">
            @foreach ($orders as $order)
                @php
                    $statusConfig = match($order->status) {
                        'Shipped' => ['color' => 'info', 'icon' => 'fas fa-truck', 'text' => 'Out for Delivery'],
                        'Delivered' => ['color' => 'primary', 'icon' => 'fas fa-check-circle', 'text' => 'Delivered'],
                        'Cancelled' => ['color' => 'danger', 'icon' => 'fas fa-times-circle', 'text' => 'Cancelled'],
                        default => ['color' => 'warning', 'icon' => 'fas fa-clock', 'text' => 'Processing'],
                    };
                @endphp

                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                        {{-- Order Header --}}
                        <div class="card-header border-0" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 1.5rem;">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-1 fw-bold" style="color: #2c3e50;">
                                        <i class="fas fa-receipt text-primary me-2"></i>
                                        Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ $order->created_at->format('M d, Y') }} at {{ $order->created_at->format('h:i A') }}
                                    </small>
                                </div>
                                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                    <div class="d-flex flex-column align-items-md-end">
                                        <span class="badge mb-2" 
                                              style="background: var(--bs-{{ $statusConfig['color'] }}); border-radius: 15px; padding: 8px 15px; font-size: 0.9rem;">
                                            <i class="{{ $statusConfig['icon'] }} me-1"></i>{{ $statusConfig['text'] }}
                                        </span>
                                        <h4 class="mb-0 fw-bold" style="color: #28a745;">
                                            Total: ₱{{ number_format($order->total_price, 2) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Order Items --}}
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3" style="color: #2c3e50;">
                                <i class="fas fa-fish text-primary me-2"></i>
                                Order Items ({{ $order->products->count() }} {{ $order->products->count() == 1 ? 'item' : 'items' }})
                            </h6>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th class="border-0 fw-semibold" style="color: #495057;">Product</th>
                                            <th class="border-0 fw-semibold text-center" style="color: #495057;">Quantity</th>
                                            <th class="border-0 fw-semibold text-end" style="color: #495057;">Price</th>
                                            <th class="border-0 fw-semibold text-end" style="color: #495057;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->products as $product)
                                            <tr>
                                                <td class="border-0 py-3">
                                                    <div class="d-flex align-items-center">
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
                                                            <small class="text-muted">Fresh Fish</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="border-0 py-3 text-center">
                                                    <span class="badge bg-light text-dark" style="border-radius: 15px; padding: 8px 12px;">
                                                        {{ $product->pivot->quantity }}
                                                    </span>
                                                </td>
                                                <td class="border-0 py-3 text-end fw-semibold">
                                                    ₱{{ number_format($product->price, 2) }}
                                                </td>
                                                <td class="border-0 py-3 text-end fw-bold" style="color: #28a745;">
                                                    ₱{{ number_format($product->price * $product->pivot->quantity, 2) }}
                                                </td>
                                            </tr>

                                            {{-- Review Section --}}
                                            @if($order->status === 'Delivered')
                                                @php
                                                    $review = $product->reviews()
                                                        ->where('user_id', auth()->id())
                                                        ->where('order_id', $order->id)
                                                        ->first();
                                                @endphp
                                                <tr>
                                                    <td colspan="4" class="border-0 pt-0">
                                                        @if(!$review)
                                                            {{-- Review Form --}}
                                                            <div class="card border-0" style="background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 100%); border-radius: 15px;">
                                                                <div class="card-body p-3">
                                                                    <h6 class="fw-bold mb-3" style="color: #0d6efd;">
                                                                        <i class="fas fa-star me-2"></i>Rate {{ $product->name }}
                                                                    </h6>
                                                                    <form action="{{ route('reviews.store', ['order' => $order->id, 'product' => $product->id]) }}" method="POST">
                                                                        @csrf
                                                                        <div class="row g-3 align-items-end">
                                                                            <div class="col-md-3">
                                                                                <label for="rating-{{ $product->id }}" class="form-label small fw-semibold">Rating</label>
                                                                                <select name="rating" id="rating-{{ $product->id }}" class="form-select" style="border-radius: 10px;">
                                                                                    @for ($i = 5; $i >= 1; $i--)
                                                                                        <option value="{{ $i }}">{{ str_repeat('★', $i) }} {{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                                                                    @endfor
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label for="comment-{{ $product->id }}" class="form-label small fw-semibold">Comment (Optional)</label>
                                                                                <input type="text" name="comment" id="comment-{{ $product->id }}" 
                                                                                       class="form-control" placeholder="Share your experience..."
                                                                                       style="border-radius: 10px;">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <button type="submit" class="btn btn-primary w-100" 
                                                                                        style="border-radius: 10px; background: linear-gradient(45deg, #0d6efd, #6610f2);">
                                                                                    <i class="fas fa-paper-plane me-1"></i>Submit
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @else
                                                            {{-- Existing Review --}}
                                                            <div class="card border-0" style="background: linear-gradient(135deg, #d4edda 0%, #f0f9f0 100%); border-radius: 15px;">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex align-items-start">
                                                                        <div class="me-3">
                                                                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" 
                                                                                 style="width: 40px; height: 40px;">
                                                                                <i class="fas fa-check text-white"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-grow-1">
                                                                            <h6 class="fw-bold mb-1" style="color: #198754;">
                                                                                <i class="fas fa-star text-warning me-1"></i>Your Review
                                                                            </h6>
                                                                            <div class="mb-2">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    @if($i <= $review->rating)
                                                                                        <i class="fas fa-star text-warning"></i>
                                                                                    @else
                                                                                        <i class="far fa-star text-warning"></i>
                                                                                    @endif
                                                                                @endfor
                                                                                <span class="ms-2 fw-semibold">{{ $review->rating }}/5</span>
                                                                            </div>
                                                                            @if($review->comment)
                                                                                <p class="mb-0 text-muted fst-italic">"{{ $review->comment }}"</p>
                                                                            @endif
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-calendar-alt me-1"></i>
                                                                                Reviewed on {{ $review->created_at->format('M d, Y') }}
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- Order Actions --}}
<div class="row g-2 mt-3">
    @if($order->status === 'Delivered' && $order->refund_status === 'None')
        <div class="col-md-6">
            <form action="{{ route('orders.refund', $order->id) }}" method="POST">
                @csrf
                <div class="mb-2">
                    <textarea name="refund_reason" class="form-control" placeholder="Why are you asking for a refund?" required style="border-radius: 10px;"></textarea>
                </div>
                <button type="submit" class="btn btn-outline-danger w-100" style="border-radius: 15px;">
                    <i class="fas fa-undo me-2"></i>Request Refund
                </button>
            </form>
        </div>
    @elseif($order->refund_status === 'Pending')
        <div class="col-12">
            <div class="alert alert-warning border-0" style="border-radius: 15px;">
                <i class="fas fa-hourglass-half me-2"></i>
                Refund request pending. Reason: <strong>{{ $order->refund_reason }}</strong>
            </div>
        </div>
    @elseif($order->refund_status === 'Approved')
        <div class="col-12">
            <div class="alert alert-success border-0" style="border-radius: 15px;">
                <i class="fas fa-check-circle me-2"></i>
                Refund approved.
            </div>
        </div>
    @elseif($order->refund_status === 'Rejected')
        <div class="col-12">
            <div class="alert alert-danger border-0" style="border-radius: 15px;">
                <i class="fas fa-times-circle me-2"></i>
                Refund request rejected. Reason: <strong>{{ $order->refund_reason }}</strong>
            </div>
        </div>
    @endif
</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Order Summary Stats --}}
        @php
            $totalOrders = $orders->count();
            $totalSpent = $orders->sum('total_price');
            $deliveredOrders = $orders->where('status', 'Delivered')->count();
        @endphp
        
        <div class="row g-4 mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: #2c3e50;">
                            <i class="fas fa-chart-bar text-primary me-2"></i>Order Statistics
                        </h5>
                        <div class="row g-4 text-center">
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-shopping-bag text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h4 class="fw-bold mb-1" style="color: #667eea;">{{ $totalOrders }}</h4>
                                    <small class="text-muted">Total Orders</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-dollar-sign text-success mb-2" style="font-size: 2rem;"></i>
                                    <h4 class="fw-bold mb-1" style="color: #28a745;">${{ number_format($totalSpent, 2) }}</h4>
                                    <small class="text-muted">Total Spent</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-check-circle text-info mb-2" style="font-size: 2rem;"></i>
                                    <h4 class="fw-bold mb-1" style="color: #17a2b8;">{{ $deliveredOrders }}</h4>
                                    <small class="text-muted">Delivered Orders</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
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
    
    .btn-outline-primary:hover {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border-color: #667eea;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .badge {
        font-weight: 500;
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

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection