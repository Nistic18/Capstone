@extends('layouts.app')
@section('title', 'Order Details')

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
                        $hasDeliveredAll = $products->every(fn($p) => $p->pivot->product_status === 'Delivered');
                        $pendingCount = $products->where('pivot.product_status', 'Pending')->count();
                        $shippedCount = $products->where('pivot.product_status', 'Shipped')->count();
                        $deliveredCount = $products->where('pivot.product_status', 'Delivered')->count();
                        $overallStatus = $hasDeliveredAll ? 'Completed' : ($pendingCount > 0 ? 'Processing' : 'In Transit');
                        $statusColor = match($overallStatus) {
                            'Completed' => 'success',
                            'In Transit' => 'info',
                            default => 'warning'
                        };
                    @endphp

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
                            <td colspan="3" class="text-end fw-bold">
                                Total: <span class="text-success">₱{{ number_format($order->total_price, 2) }}</span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection
