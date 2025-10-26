{{-- resources/views/orders/partials/order-list.blade.php --}}

@if ($filteredOrders->isEmpty())
    {{-- Empty State --}}
    <div class="text-center py-5">
        <i class="fas fa-shopping-bag text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
        <h5 class="text-muted mb-2">No orders found</h5>
        <p class="text-muted">You don't have any orders in this category yet.</p>
        <a href="{{ route('home') }}" class="btn btn-primary mt-3" style="border-radius: 4px;">
            <i class="fas fa-fish me-2"></i>Start Shopping
        </a>
    </div>
@else
    {{-- Orders List --}}
    <div class="orders-list">
        @foreach ($filteredOrders as $order)
            @php
                $statusConfig = match($order->status) {
                    'Shipped' => ['color' => 'info', 'icon' => 'fas fa-truck', 'text' => 'TO RECEIVE'],
                    'Delivered' => ['color' => 'success', 'icon' => 'fas fa-check-circle', 'text' => 'COMPLETED'],
                    'Cancelled' => ['color' => 'secondary', 'icon' => 'fas fa-times-circle', 'text' => 'CANCELLED'],
                    default => ['color' => 'warning', 'icon' => 'fas fa-box', 'text' => 'TO SHIP'],
                };
                $paymentMethodFull = match(strtolower($order->payment_method)) {
                    'cod' => 'Cash on Delivery',
                    'pickup' => 'Pickup',
                    default => ucfirst($order->payment_method ?? 'Cash on Delivery'),
                };
            @endphp

            <div class="card border mb-3 order-card" style="border-radius: 4px; border-color: #e5e5e5;">
                {{-- Order Header --}}
                <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-store text-muted me-2"></i>
                                <span class="fw-semibold">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                <span class="mx-2 text-muted">|</span>
                                <small class="text-muted">
                                    {{ $order->created_at->format('M d, Y h:i A') }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end mt-2 mt-md-0">
                            <span class="badge" style="background-color: var(--bs-{{ $statusConfig['color'] }}); font-size: 0.75rem; padding: 0.4rem 0.8rem;">
                                <i class="{{ $statusConfig['icon'] }} me-1"></i>{{ $statusConfig['text'] }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="card-body" style="padding: 1.5rem;">
                    @foreach ($order->products as $product)
                        <div class="row mb-3 pb-3 @if(!$loop->last) border-bottom @endif">
                            <div class="col-md-8">
                                <div class="d-flex">
                                    {{-- Product Image --}}
                                    <div class="me-3">
                                        @if($product->images && $product->images->count() > 0)
                                            <img src="{{ asset('storage/' . $product->images->first()->image) }}" 
                                                 alt="{{ $product->name }}"
                                                 class="border"
                                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                                        @else
                                            <div class="bg-light border d-flex align-items-center justify-content-center" 
                                                 style="width: 80px; height: 80px; border-radius: 4px;">
                                                <i class="fas fa-fish text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Product Info --}}
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">{{ $product->name }}</h6>
                                        <small class="text-muted d-block mb-2">x{{ $product->pivot->quantity }}</small>
                                        <small class="text-muted">{{ $paymentMethodFull }}</small>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Product Price --}}
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="mb-2">
                                </div>
                            </div>
                        </div>

                        {{-- Review Section (Only for Delivered Orders) --}}
                        @if($order->status === 'Delivered' && $loop->last)
                            @php
                                $review = $product->reviews()
                                    ->where('user_id', auth()->id())
                                    ->where('order_id', $order->id)
                                    ->first();
                            @endphp
                            
                            @if(!$review)
                                <div class="alert alert-light border mt-3" style="border-radius: 4px;">
                                    <form action="{{ route('reviews.store', ['order' => $order->id, 'product' => $product->id]) }}" method="POST">
                                        @csrf
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-3">
                                                <label class="form-label small text-muted mb-1">Rate this product</label>
                                                <select name="rating" class="form-select form-select-sm" required style="border-radius: 4px;">
                                                    @for ($i = 5; $i >= 1; $i--)
                                                        <option value="{{ $i }}">{{ str_repeat('⭐', $i) }} ({{ $i }})</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted mb-1">Your review (optional)</label>
                                                <input type="text" name="comment" class="form-control form-control-sm" 
                                                       placeholder="Share your thoughts..." style="border-radius: 4px;">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary btn-sm w-100" style="border-radius: 4px;">
                                                    <i class="fas fa-paper-plane me-1"></i>Submit Review
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endif
                    @endforeach

                    {{-- Order Total --}}
                    <div class="row mt-3 pt-3 border-top">
                        <div class="col-12 text-end">
                            <span class="text-muted me-2">Order Total:</span>
                            <span class="fw-bold" style="color: #764ba2; font-size: 1.3rem;">
                                ₱{{ number_format($order->total_price, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Order Actions --}}
                <div class="card-footer bg-white border-top" style="padding: 1rem 1.5rem;">
                    <div class="row g-2">
                        {{-- Refund Section --}}
                        @if($order->status === 'Delivered' && $order->refund_status === 'None')
                            <div class="col-md-6">
                                <form action="{{ route('orders.refund', $order->id) }}" method="POST">
                                    @csrf
                                    <div class="input-group">
                                        <input type="text" name="refund_reason" class="form-control form-control-sm" 
                                               placeholder="Reason for refund..." required style="border-radius: 4px 0 0 4px;">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius: 0 4px 4px 0;">
                                            Return/Refund
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @elseif($order->refund_status === 'Pending')
                            <div class="col-12">
                                <div class="alert alert-warning mb-0 py-2" style="border-radius: 4px; font-size: 0.9rem;">
                                    <i class="fas fa-hourglass-half me-2"></i>
                                    <strong>Refund Pending:</strong> {{ $order->refund_reason }}
                                </div>
                            </div>
                        @elseif($order->refund_status === 'Approved')
                            <div class="col-12">
                                <div class="alert alert-success mb-0 py-2" style="border-radius: 4px; font-size: 0.9rem;">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Refund Approved</strong>
                                </div>
                            </div>
                        @elseif($order->refund_status === 'Rejected')
                            <div class="col-12">
                                <div class="alert alert-danger mb-0 py-2" style="border-radius: 4px; font-size: 0.9rem;">
                                    <i class="fas fa-times-circle me-2"></i>
                                    <strong>Refund Rejected:</strong> {{ $order->refund_reason }}
                                </div>
                            </div>
                        @endif

                        {{-- Cancel Order --}}
                        @if($order->status !== 'Delivered' && $order->status !== 'Cancelled' && $order->status !== 'Shipped')
                            <div class="col-md-6">
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                                    @csrf
                                    <div class="input-group">
                                        <input type="text" name="cancel_reason" class="form-control form-control-sm" 
                                               placeholder="Reason for cancellation..." required style="border-radius: 4px 0 0 4px;">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" style="border-radius: 0 4px 4px 0;">
                                            Cancel Order
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @elseif($order->status === 'Cancelled')
                            <div class="col-12">
                                <div class="alert alert-secondary mb-0 py-2" style="border-radius: 4px; font-size: 0.9rem;">
                                    <i class="fas fa-times-circle me-2"></i>
                                    <strong>Order Cancelled:</strong> {{ $order->cancel_reason }}
                                </div>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="col-12 text-end mt-2">
                            @if($order->status === 'Delivered')
                                <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm" style="border-radius: 4px;">
                                    <i class="fas fa-redo me-1"></i>Buy Again
                                </a>
                            @endif
                            @php
    // Get the seller (user_id from the first product in the order)
    $sellerId = $order->products->first()->user_id ?? null;
@endphp

@if($sellerId)
    <a href="{{ route('chat.index', ['user' => $sellerId, 'order' => $order->id]) }}" 
       class="btn btn-outline-secondary btn-sm" 
       style="border-radius: 4px;">
        <i class="fas fa-comment me-1"></i>Contact Seller
    </a>
@endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif