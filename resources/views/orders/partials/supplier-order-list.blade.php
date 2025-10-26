{{-- resources/views/orders/partials/supplier-order-list.blade.php --}}

@forelse ($filteredOrders as $order)
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
            'Cancelled' => 'danger',
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
                    <div class="d-flex align-items-center gap-3 mt-2">
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
                            @elseif($overallStatus === 'Cancelled')
                                <i class="fas fa-times-circle me-1"></i>{{ $overallStatus }}
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

        {{-- Order Body --}}
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

            {{-- Product Table --}}
            <form method="POST" action="{{ route('supplier.orders.status.bulk-update', $order->id) }}" id="orderForm{{ $order->id }}">
                @csrf
                @method('PUT')

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th class="border-0 fw-semibold">
                                    <div class="d-flex align-items-center">
                                        @if(!$hasDeliveredAll && !$isRefunded)
                                            <input type="checkbox" class="form-check-input me-2"
                                                id="selectAll{{ $order->id }}"
                                                onchange="toggleAllProducts({{ $order->id }})">
                                        @endif
                                        Product
                                    </div>
                                </th>
                                <th class="border-0 text-center fw-semibold">Quantity</th>
                                <th class="border-0 text-center fw-semibold">Current Status</th>
                                @if(!$hasDeliveredAll && !$isRefunded)
                                    <th class="border-0 text-center fw-semibold">Action</th>
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
                                                        alt="{{ $product->name }}" class="rounded"
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

                                    <td class="border-0 text-center">{{ $product->pivot->quantity }}</td>

                                    <td class="border-0 text-center">
                                        @php
                                            $badgeColor = match($product->pivot->product_status) {
                                                'Pending' => 'warning',
                                                'Shipped' => 'info',
                                                'Delivered' => 'success',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }}">
                                            {{ ucfirst($product->pivot->product_status) }}
                                        </span>
                                    </td>

                                    @if(!$hasDeliveredAll && !$isRefunded)
                                    <td class="border-0 text-center">
                                        <select name="product_statuses[{{ $product->id }}]" 
                                                class="form-select form-select-sm d-inline-block w-auto"
                                                onchange="updateIndividualStatus({{ $order->id }}, {{ $product->id }}, this.value)">
                                            <option value="">-- Update --</option>
                                            <option value="Pending" {{ $product->pivot->product_status === 'Pending' ? 'disabled' : '' }}>Mark as Shipped</option>
                                            <option value="Shipped" {{ $product->pivot->product_status === 'Shipped' ? 'disabled' : '' }}>Mark as Delivered</option>
                                            <option value="Delivered" {{ $product->pivot->product_status === 'Delivered' ? 'disabled' : '' }}>Mark as Completed</option>
                                        </select>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(!$hasDeliveredAll && !$isRefunded)
                    <div class="mt-3 text-end">
                        <select name="product_status" class="form-select d-inline-block w-auto me-2">
                            <option value="">-- Bulk Action --</option>
                            <option value="Shipped">Mark Selected as Shipped</option>
                            <option value="Delivered">Mark Selected as Delivered</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary" style="border-radius: 10px;">
                            <i class="fas fa-sync-alt me-1"></i> Update Selected
                        </button>
                    </div>
                @endif
            </form>

            {{-- Footer --}}
            <div class="card-footer border-0 bg-light d-flex justify-content-end gap-2 mt-4 p-3">
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
        </div>
    </div>

@empty
    <div class="text-center py-5">
        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
        <h6 class="text-muted">No orders found in this category.</h6>
    </div>
@endforelse
