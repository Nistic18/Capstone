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
        $productCount = $products->count();

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
            <form method="POST" action="{{ route('supplier.orders.status.bulk-update', $order->id) }}">
                @csrf
                @method('PUT')

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th class="border-0 fw-semibold">Product</th>
                                <th class="border-0 text-center fw-semibold">Quantity</th>
                                <th class="border-0 text-center fw-semibold">Unit Info</th>
                                <th class="border-0 text-center fw-semibold">Current Status</th>
                                @if(!$hasDeliveredAll && !$isRefunded && !$isCancelled)
                                    <th class="border-0 text-center fw-semibold">Action</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="border-0 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($product->images && $product->images->count() > 0)
                                                    <img src="{{ asset($product->images->first()->image) }}" 
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
                                        @if($product->unit_type && $product->unit_value)
                                            <span class="badge bg-info" style="border-radius: 10px; padding: 4px 8px; font-size: 0.7rem;">
                                                <i class="fas fa-box me-1"></i>
                                                @switch($product->unit_type)
                                                    @case('pack')
                                                        {{ $product->unit_value }} pc{{ $product->unit_value > 1 ? 's' : '' }}/pack
                                                        @break
                                                    @case('kilo')
                                                        {{ $product->unit_value }} kg
                                                        @break
               										@case('gram')
                    									{{ $product->unit_value }} g
                    									@break
                                                    @case('box')
                                                        {{ $product->unit_value }} kg/box
                                                        @break
                                                    @case('piece')
                                                        Per piece
                                                        @break
                                                @endswitch
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

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

                                    @if(!$hasDeliveredAll && !$isRefunded && !$isCancelled)
                                    <td class="border-0 text-center">
                                        @if($productCount === 1)
                                            {{-- Show buttons for single product --}}
                                            @if($product->pivot->product_status === 'Pending')
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="updateIndividualStatus({{ $order->id }}, {{ $product->id }}, 'Shipped')"
                                                        style="border-radius: 8px;">
                                                    <i class="fas fa-truck me-1"></i> Mark as Shipped
                                                </button>
                                                @elseif($product->pivot->product_status === 'Packed')
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="updateIndividualStatus({{ $order->id }}, {{ $product->id }}, 'Delivered')"
                                                        style="border-radius: 8px;">
                                                    <i class="fas fa-check-circle me-1"></i> Mark as Delivered
                                                </button>
                                            @elseif($product->pivot->product_status === 'Shipped')
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="updateIndividualStatus({{ $order->id }}, {{ $product->id }}, 'Delivered')"
                                                        style="border-radius: 8px;">
                                                    <i class="fas fa-check-circle me-1"></i> Mark as Delivered
                                                </button>
                                            @elseif($product->pivot->product_status === 'Delivered')
                                                <span class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i> Completed
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Bulk Action for Multiple Products --}}
                    @if(!$hasDeliveredAll && !$isRefunded && !$isCancelled && $productCount > 1)
                        @php
                            $hasPending = $products->contains(fn($p) => $p->pivot->product_status === 'Pending');
                            $hasShipped = $products->contains(fn($p) => $p->pivot->product_status === 'Shipped');
                        @endphp

                        @if($hasPending)
                            <input type="hidden" name="product_status" value="Shipped">
                            <button type="submit" class="btn btn-sm btn-info" style="border-radius: 10px;">
                                <i class="fas fa-truck me-1"></i> Mark All as Shipped
                            </button>
                        @elseif($hasShipped)
                            <input type="hidden" name="product_status" value="Delivered">
                            <button type="submit" class="btn btn-sm btn-success" style="border-radius: 10px;">
                                <i class="fas fa-check-circle me-1"></i> Mark All as Delivered
                            </button>
                        @endif
                    @endif
                </div>
            </form>

{{-- Refund Actions --}}
@if ($order->refund_status === 'Pending')
    <div class="mt-3">
        {{-- Display Refund Reason --}}
        @if($order->refund_reason)
            <div class="alert alert-warning mb-3" style="border-radius: 10px;">
                <h6 class="mb-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>Refund Request
                </h6>
                <p class="mb-0">
                    <strong>Buyer's Reason:</strong> {{ $order->refund_reason }}
                </p>
            </div>
        @endif

        <div id="refundButtons_{{ $order->id }}">
            <form action="{{ route('orders.refund.approve', $order->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this refund request?')">
                    <i class="fas fa-check"></i> Accept Refund
                </button>
            </form>

            <button type="button" class="btn btn-danger btn-sm" onclick="toggleDeclineForm_{{ $order->id }}()">
                <i class="fas fa-times"></i> Reject Refund
            </button>
        </div>

        {{-- Inline Decline Form (Hidden by default) --}}
        <div id="declineForm_{{ $order->id }}" style="display: none; margin-top: 15px; padding: 15px; background-color: #fff3cd; border-radius: 10px; border: 2px solid #ffc107; position: relative; z-index: 1;">
            <form action="{{ route('orders.refund.decline', $order->id) }}" method="POST" id="declineReasonForm_{{ $order->id }}" onsubmit="console.log('Form submitting for order {{ $order->id }}'); console.log('Decline reason value:', document.getElementById('decline_reason_{{ $order->id }}').value); return true;">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="decline_reason_{{ $order->id }}" class="form-label fw-semibold">
                        <i class="fas fa-exclamation-circle text-warning me-1"></i>
                        Reason for rejection (optional)
                    </label>
                    <textarea 
                        class="form-control" 
                        id="decline_reason_{{ $order->id }}" 
                        name="decline_reason" 
                        rows="3" 
                        placeholder="Explain why you're rejecting this refund request..."
                        onchange="console.log('Textarea changed:', this.value)"></textarea>
                    <small class="text-muted">This message will be visible to the buyer.</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-times-circle me-1"></i> Confirm Rejection
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleDeclineForm_{{ $order->id }}()">
                        <i class="fas fa-undo me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>

        <script>
        (function() {
            // Create a closure to avoid conflicts
            var formVisible_{{ $order->id }} = false;
            
            window.toggleDeclineForm_{{ $order->id }} = function() {
                console.log('Toggle decline form for order {{ $order->id }}');
                
                // IMPORTANT: Find the active tab pane FIRST
                var activeTabPane = document.querySelector('.tab-pane.active');
                if (!activeTabPane) {
                    console.error('No active tab pane found!');
                    return;
                }
                
                console.log('Active tab pane:', activeTabPane.id);
                
                // Find the form and buttons WITHIN the active tab pane
                var formElement = activeTabPane.querySelector('#declineForm_{{ $order->id }}');
                var buttonElement = activeTabPane.querySelector('#refundButtons_{{ $order->id }}');
                
                console.log('Form element in active tab:', formElement);
                console.log('Button element in active tab:', buttonElement);
                
                if (!formElement || !buttonElement) {
                    console.error('Elements not found in active tab pane!');
                    return;
                }
                
                if (formVisible_{{ $order->id }}) {
                    // Hide form, show buttons
                    formElement.style.cssText = 'display: none; margin-top: 15px; padding: 15px; background-color: #fff3cd; border-radius: 10px; border: 2px solid #ffc107; position: relative; z-index: 1;';
                    buttonElement.style.display = 'block';
                    formVisible_{{ $order->id }} = false;
                    console.log('Form hidden');
                } else {
                    // Show form, hide buttons
                    formElement.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; margin-top: 15px; padding: 15px; background-color: #fff3cd; border-radius: 10px; border: 2px solid #ffc107; position: relative; z-index: 1000; min-height: 200px; width: 100%;';
                    buttonElement.style.display = 'none';
                    formVisible_{{ $order->id }} = true;
                    console.log('Form shown in active tab');
                    
                    // Force a reflow
                    formElement.offsetHeight;
                    
                    // Double-check visibility after a tiny delay
                    setTimeout(function() {
                        console.log('After timeout - Form offsetHeight:', formElement.offsetHeight);
                        console.log('After timeout - Form offsetWidth:', formElement.offsetWidth);
                        
                        if (formElement.offsetHeight > 0) {
                            console.log('✅ Form is now visible!');
                        } else {
                            console.error('❌ Form still not visible');
                        }
                    }, 100);
                }
            };
        })();
        </script>
    </div>
@elseif ($order->refund_status === 'Approved')
    <div class="mt-3">
        <div class="alert alert-success mb-0" style="border-radius: 10px;">
            <h6 class="mb-2">
                <i class="fas fa-check-circle me-2"></i>Refund Approved
            </h6>
            @if($order->refund_reason)
                <p class="mb-0"><small><strong>Buyer's Reason:</strong> {{ $order->refund_reason }}</small></p>
            @endif
        </div>
    </div>
@elseif ($order->refund_status === 'Rejected')
    <div class="mt-3">
        <div class="alert alert-danger mb-0" style="border-radius: 10px;">
            <h6 class="mb-2">
                <i class="fas fa-times-circle me-2"></i>Refund Rejected
            </h6>
            @if($order->refund_reason)
                <p class="mb-1"><small><strong>Buyer's Reason:</strong> {{ $order->refund_reason }}</small></p>
            @endif
            @if($order->decline_reason)
                <p class="mb-0"><small><strong>Your Response:</strong> {{ $order->decline_reason }}</small></p>
            @else
                <p class="mb-0"><small class="text-muted">No reason provided by seller.</small></p>
            @endif
        </div>
    </div>
@endif
        </div> {{-- End of card-body --}}

        {{-- Footer --}}
        <div class="card-footer border-0 bg-light d-flex justify-content-end gap-2 p-3">
            {{-- Contact Buyer Button --}}
            @if($order->user_id)
                <a href="{{ route('chat.index', ['user' => $order->user_id, 'order' => $order->id]) }}" 
                   class="btn btn-sm btn-outline-primary" 
                   style="border-radius: 10px;">
                    <i class="fas fa-comment me-1"></i>Contact Buyer
                </a>
            @endif

            <a href="{{ route('orders.show', $order->id) }}" 
               class="btn btn-sm btn-outline-primary" 
               style="border-radius: 10px;">
                <i class="fas fa-eye me-1"></i> View Details
            </a>

            @if($order->status === 'Pending' || $order->status === 'Packed')
                <a href="{{ route('orders.generateQR', $order->id) }}" 
                   class="btn btn-sm btn-outline-success"
                   style="border-radius: 10px;">
                    <i class="fas fa-qrcode me-1"></i> Generate QR
                </a>
            @endif
        </div>
        
    </div> {{-- End of card --}}
@empty
    <div class="text-center py-5">
        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
        <h6 class="text-muted">No orders found in this category.</h6>
    </div>
@endforelse