@extends('layouts.app')
{{-- Add Bootstrap 5 CSS --}}

@section('title', 'Cart')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

{{-- Add Bootstrap 5 JavaScript --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
@endpush
@section('content')
@php
    $userAddress = auth()->user()->address ?? null;
    $userPhone = auth()->user()->phone ?? null;
@endphp

<div class="container mt-4">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background: transparent; padding: 3%;">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none" style="color: #667eea;">
                    <i class="fas fa-home me-1"></i>Home
                </a>
            </li>
            <li class="breadcrumb-item active " aria-current="page">
                <i class="fas fa-shopping-cart me-1"></i>Your Cart
            </li>
        </ol>
    </nav>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle text-success me-3" style="font-size: 1.2rem;"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle text-danger me-3" style="font-size: 1.2rem;"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="row g-4">
        {{-- Cart Items Section --}}
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    @if($cart->isEmpty())
                        {{-- Empty Cart State --}}
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-shopping-cart text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                            </div>
                            <h3 class="text-muted mb-3">Your Cart is Empty</h3>
                            <p class="text-muted mb-4">Looks like you haven't added any fresh fish to your cart yet!</p>
                            <a href="{{ route('home') }}" class="btn btn-primary btn-lg" 
                               style="border-radius: 25px; background: linear-gradient(45deg, #667eea, #764ba2); border: none;">
                                <i class="fas fa-fish me-2"></i>Start Shopping
                            </a>
                        </div>
                    @else
                        {{-- Cart Items Header --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" id="selectAll" style="width: 20px; height: 20px; cursor: pointer;">
                                    <label class="form-check-label ms-2" for="selectAll" style="cursor: pointer;">
                                        Select All
                                    </label>
                                </div>
                                <h4 class="fw-bold mb-0" style="color: #2c3e50;">
                                    Cart Items ({{ $cart->count() }})
                                </h4>
                            </div>
                            <span class="badge" style="background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 15px; padding: 8px 15px; color: #fff;">
                                🐟 Fresh Selection
                            </span>
                        </div>

                        @php $total = 0; @endphp

                        {{-- Cart Items --}}
                        @foreach($cart as $item)
                            @php
                                $maxStock = $item->product->quantity;
                                $subtotal = $item->product->price * $item->quantity;
                                $total += $subtotal;
                            @endphp
                            
                            <div class="card border-0 mb-3 cart-item" style="background-color: #f8f9fa; border-radius: 15px;">
                                <div class="card-body p-3">
                                    <div class="row g-3 align-items-center">
                                        {{-- Checkbox --}}
                                        <div class="col-auto">
                                            <div class="form-check">
                                                <input class="form-check-input product-checkbox" 
                                                       type="checkbox" 
                                                       value="{{ $item->product->id }}"
                                                       data-price="{{ $item->product->price }}"
                                                       data-quantity="{{ $item->quantity }}"
                                                       data-subtotal="{{ $subtotal }}"
                                                       id="product-{{ $item->product->id }}"
                                                       style="width: 20px; height: 20px; cursor: pointer;">
                                            </div>
                                        </div>

                                        {{-- Product Image --}}
                                        <div class="col-md-2 col-3">
                                            <div class="position-relative">
@if($item->product->images && $item->product->images->count())
    <img src="{{ asset('storage/' . $item->product->images->first()->image) }}" 
         alt="{{ $item->product->name }}"
         class="img-fluid rounded"
         style="height: 80px; width: 80px; object-fit: cover;">
@else
    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
         style="height: 80px; width: 80px;">
        <i class="fas fa-fish text-muted"></i>
    </div>
@endif
                                            </div>
                                        </div>

                                        {{-- Product Info --}}
                                        <div class="col-md-3 col-8">
                                            <h6 class="fw-bold mb-1" style="color: #2c3e50;">{{ $item->product->name }}</h6>
                                            <p class="text-muted small mb-1">
                                                <i class="fas fa-tag me-1"></i>
                                                ₱{{ number_format($item->product->price, 2) }}
                                                @if($item->product->unit_type && $item->product->unit_value)
                                                    / {{ $item->product->unit_value }} 
                                                    {{ $item->product->unit_type }}{{ $item->product->unit_value > 1 ? 's' : '' }}
                                                @else
                                                    per piece
                                                @endif
                                            </p>
                                            
                                            {{-- Unit Information Badge --}}
                                            @if($item->product->unit_type && $item->product->unit_value)
                                                <span class="badge bg-info" style="font-size: 0.7rem;">
                                                    <i class="fas fa-box me-1"></i>
                                                    @switch($item->product->unit_type)
                                                        @case('pack')
                                                            {{ $item->product->unit_value }} pcs/pack
                                                            @break
                                                        @case('kilo')
                                                            {{ $item->product->unit_value }} kg
                                                            @break
                                                        @case('box')
                                                            {{ $item->product->unit_value }} kg/box
                                                            @break
                                                        @case('piece')
                                                            Per piece
                                                            @break
                                                    @endswitch
                                                </span>
                                            @endif
                                            
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-boxes me-1"></i>Stock: {{ $item->product->quantity }} available
                                            </small>
                                        </div>

                                        {{-- Quantity Controls --}}
                                        <div class="col-md-3 col-6">
                                            <label class="form-label small fw-semibold">Quantity</label>
                                            <form action="{{ route('cart.update', $item->product->id) }}" method="POST" class="d-flex align-items-center">
                                                @csrf
                                                @method('PUT')
                                                <div class="input-group" style="max-width: 130px;">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                            onclick="decreaseQuantity({{ $item->product->id }})"
                                                            style="border-radius: 10px 0 0 10px;">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" 
                                                           id="quantity-{{ $item->product->id }}"
                                                           name="quantity" 
                                                           value="{{ $item->quantity }}" 
                                                           min="1" 
                                                           max="{{ $maxStock }}" 
                                                           class="form-control form-control-sm text-center border-secondary"
                                                           style="border-left: none; border-right: none;"
                                                           onchange="this.form.submit()">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                            onclick="increaseQuantity({{ $item->product->id }}, {{ $maxStock }})"
                                                            style="border-radius: 0 10px 10px 0;">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        {{-- Subtotal & Actions --}}
                                        <div class="col-md-2 col-6 text-end">
                                            <div class="mb-2">
                                                <span class="fw-bold h6" style="color: #28a745;">
                                                    ₱{{ number_format($subtotal, 2) }}
                                                </span>
                                            </div>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    style="border-radius: 10px;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#removeFromCartModal"
                                                    data-product-name="{{ $item->product->name }}"
                                                    data-product-id="{{ $item->product->id }}"
                                                    title="Remove from cart">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- Order Summary Section --}}
        @if(!$cart->isEmpty())
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="border-radius: 20px; top: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4" style="color: #2c3e50;">
                        <i class="fas fa-receipt text-primary me-2"></i>Order Summary
                    </h5>

                    {{-- Selected Items Alert --}}
                    <div class="alert alert-info border-0 mb-3" style="background: rgba(0, 0, 0, 0.911); border-radius: 10px;">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <span id="selectedItemsCount">0</span> item(s) selected for checkout
                        </small>
                    </div>

                    {{-- User Information Display --}}
                    @if($userAddress || $userPhone)
                    <div class="mb-4 p-3 rounded" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                        <h6 class="fw-bold mb-3" style="color: #2c3e50;">
                            <i class="fas fa-user-circle me-2 text-primary"></i>Delivery Information
                        </h6>
                        
                        @if($userPhone)
                        <div class="mb-2">
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-phone me-2 text-success"></i>Phone Number
                            </small>
                            <span class="fw-semibold">{{ $userPhone }}</span>
                        </div>
                        @endif

                        @if($userAddress)
                        <div class="mb-2">
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-map-marker-alt me-2 text-danger"></i>Delivery Address
                            </small>
                            <span class="fw-semibold">{{ $userAddress }}</span>
                        </div>
                        @endif

                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary mt-2 w-100" style="border-radius: 8px;">
                            <i class="fas fa-edit me-1"></i>Update Information
                        </a>
                    </div>
                    @endif

                    {{-- Order Details --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Selected Items</span>
                            <span class="fw-semibold" id="selectedTotal">₱0.00</span>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="h6 fw-bold">Total Amount</span>
                            <span class="h5 fw-bold" style="color: #28a745;" id="grandTotal">₱0.00</span>
                        </div>
                    </div>

                    {{-- Delivery Information --}}
                    <div class="mb-4 p-3 rounded" style="background-color: #e8f4fd; border-left: 4px solid #17a2b8;">
                        <h6 class="fw-bold text-info mb-2">
                            <i class="fas fa-truck me-2"></i>Delivery Info
                        </h6>
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>Same-day delivery available<br>
                            <i class="fas fa-snowflake me-1"></i>Cold chain delivery<br>
                            <i class="fas fa-shield-alt me-1"></i>Freshness guaranteed
                        </small>
                    </div>

                    {{-- Checkout Button --}}
@if($userAddress && $userPhone)
    <div class="mb-3">
        <label for="payment_method" class="form-label fw-semibold">
            <i class="fas fa-hand-holding-usd me-2 text-success"></i>Choose Payment Method
        </label>
        <select name="payment_method" id="payment_method" class="form-select" required>
            <option value="COD">Cash on Delivery</option>
            <option value="Pickup">Pickup</option>
        </select>
    </div>

    <button type="button" class="btn btn-lg w-100 mb-3" 
            id="checkoutButton"
            style="border-radius: 15px; background: #ccc; border: none; color: #666; padding: 12px; cursor: not-allowed;"
            data-bs-toggle="modal" 
            data-bs-target="#checkoutConfirmationModal"
            disabled>
        <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
    </button>
@else
    <div class="alert alert-warning text-center mb-3" style="border-radius: 10px;">
        <i class="fas fa-exclamation-triangle me-1"></i>
        Please add your delivery address and phone number before checkout.
    </div>
    <button type="button" class="btn btn-lg w-100 mb-3" 
            style="border-radius: 15px; background: #ccc; border: none; color: #666; padding: 12px;" disabled>
        <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
    </button>
@endif
                    {{-- Continue Shopping --}}
                    <a href="{{ route('home') }}" class="btn btn-outline-primary w-100" 
                       style="border-radius: 15px; border-width: 2px;">
                        <i class="fas fa-plus me-2"></i>Add More Fish
                    </a>

                    {{-- Trust Badges --}}
                    <div class="mt-4 text-center">
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="p-2">
                                    <i class="fas fa-shield-alt text-success mb-1" style="font-size: 1.5rem;"></i>
                                    <small class="d-block text-muted">Secure</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2">
                                    <i class="fas fa-truck text-info mb-1" style="font-size: 1.5rem;"></i>
                                    <small class="d-block text-muted">Fast Delivery</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2">
                                    <i class="fas fa-fish text-primary mb-1" style="font-size: 1.5rem;"></i>
                                    <small class="d-block text-muted">Fresh</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Remove from Cart Modal --}}
<div class="modal fade" id="removeFromCartModal" tabindex="-1" aria-labelledby="removeFromCartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="removeFromCartModalLabel" style="color: #2c3e50;">
                    <i class="fas fa-shopping-cart text-danger me-2"></i>
                    Remove from Cart
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-3">Are you sure you want to remove <strong id="cartProductName"></strong> from your cart?</p>
                <div class="alert alert-warning border-0" style="background: rgba(255, 193, 7, 0.1); color: #856404;">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    This item will be removed from your shopping cart.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <form id="removeFromCartForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger" style="border-radius: 10px;">
                        <i class="fas fa-trash me-1"></i>Remove from Cart
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Checkout Confirmation Modal --}}
<div class="modal fade" id="checkoutConfirmationModal" tabindex="-1" aria-labelledby="checkoutConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold text-white" id="checkoutConfirmationModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Confirm Your Order
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 mb-4" style="background: rgba(23, 162, 184, 0.1); color: #0c5460; border-radius: 10px;">
                    <i class="fas fa-info-circle me-2"></i>
                    Please review your order details before confirming.
                </div>

                {{-- Order Items Summary --}}
                <div class="mb-4">
                    <h6 class="fw-bold mb-3" style="color: #2c3e50;">
                        <i class="fas fa-shopping-bag me-2 text-primary"></i>Order Items
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="modalOrderItems">
                                {{-- Items will be dynamically inserted here --}}
                            </tbody>
                            <tfoot style="background-color: #f8f9fa;">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total Amount:</td>
                                    <td class="text-end fw-bold" style="color: #28a745;" id="modalTotalAmount">₱0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Delivery Information --}}
                <div class="mb-4 p-3 rounded" style="background-color: #f8f9fa; border-left: 4px solid #28a745;">
                    <h6 class="fw-bold mb-3" style="color: #2c3e50;">
                        <i class="fas fa-truck me-2 text-success"></i>Delivery Information
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <small class="text-muted d-block">Phone Number:</small>
                            <span class="fw-semibold" id="confirmPhone">{{ $userPhone }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <small class="text-muted d-block">Payment Method:</small>
                            <span class="fw-semibold" id="confirmPaymentMethod">Cash on Delivery</span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Delivery Address:</small>
                            <span class="fw-semibold" id="confirmAddress">{{ $userAddress }}</span>
                        </div>
                    </div>
                </div>

                {{-- Terms and Conditions --}}
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirmTerms" required>
                    <label class="form-check-label" for="confirmTerms">
                        I confirm that all information is correct and I agree to the terms and conditions.
                    </label>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <form id="checkoutForm" action="{{ route('cart.checkout') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="payment_method" id="hiddenPaymentMethod" value="COD">
                    <input type="hidden" name="selected_products" id="selectedProductsInput">
                    <button type="submit" class="btn btn-success" style="border-radius: 10px;" id="confirmCheckoutBtn" disabled>
                        <i class="fas fa-check me-1"></i>Confirm & Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    .card:hover {
        transition: all 0.3s ease;
    }
    
    .input-group .btn {
        border: 1px solid #6c757d;
    }
    
    .sticky-top {
        position: sticky;
        top: 20px;
        z-index: 1020;
    }
    
    .cart-item {
        transition: all 0.3s ease;
    }
    
    .cart-item:has(.product-checkbox:checked) {
        background-color: #e7f3ff !important;
        border: 2px solid #667eea !important;
    }
    
    @media (max-width: 768px) {
        .sticky-top {
            position: relative;
            top: 0;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .col-md-2.col-3 {
            flex: 0 0 auto;
            width: 25%;
        }
        
        .col-md-4.col-9 {
            flex: 0 0 auto;
            width: 75%;
        }
    }
</style>

{{-- JavaScript for quantity controls and modals --}}
<script>
    let selectedProducts = [];
    let cartData = {};

    // Store cart data from server
    @foreach($cart as $item)
        cartData[{{ $item->product->id }}] = {
            name: "{{ $item->product->name }}",
            quantity: {{ $item->quantity }},
            price: {{ $item->product->price }},
            subtotal: {{ $item->product->price * $item->quantity }}
        };
    @endforeach

    function decreaseQuantity(productId) {
        const input = document.getElementById('quantity-' + productId);
        const currentValue = parseInt(input.value);
        if (currentValue > 1) {
            input.value = currentValue - 1;
            input.form.submit();
        }
    }
    
    function increaseQuantity(productId, maxStock) {
        const input = document.getElementById('quantity-' + productId);
        const currentValue = parseInt(input.value);
        if (currentValue < maxStock) {
            input.value = currentValue + 1;
            input.form.submit();
        }
    }
    
    // Auto-submit form when quantity changes
    document.querySelectorAll('input[name="quantity"]').forEach(input => {
        input.addEventListener('change', function() {
            setTimeout(() => {
                this.form.submit();
            }, 100);
        });
    });

    // Calculate selected total
    function updateSelectedTotal() {
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        let total = 0;
        selectedProducts = [];

        checkboxes.forEach(checkbox => {
            const subtotal = parseFloat(checkbox.dataset.subtotal);
            total += subtotal;
            selectedProducts.push(parseInt(checkbox.value));
        });

        document.getElementById('selectedItemsCount').textContent = checkboxes.length;
        document.getElementById('selectedTotal').textContent = '₱' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        document.getElementById('grandTotal').textContent = '₱' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

        // Enable/disable checkout button
        const checkoutButton = document.getElementById('checkoutButton');
        if (checkoutButton) {
            if (checkboxes.length === 0) {
                checkoutButton.disabled = true;
                checkoutButton.style.background = '#ccc';
                checkoutButton.style.color = '#666';
                checkoutButton.style.cursor = 'not-allowed';
            } else {
                checkoutButton.disabled = false;
                checkoutButton.style.background = 'linear-gradient(45deg, #28a745, #20c997)';
                checkoutButton.style.color = 'white';
                checkoutButton.style.cursor = 'pointer';
            }
        }
    }

    // Select All functionality
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                productCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedTotal();
            });
        }

        // Individual checkbox change
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectedTotal();
                
                // Update select all checkbox state
                const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(productCheckboxes).some(cb => cb.checked);
                
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                }
            });
        });

        // JavaScript to handle remove from cart modal
        const removeFromCartModal = document.getElementById('removeFromCartModal');
        
        if (removeFromCartModal) {
            removeFromCartModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const productName = button.getAttribute('data-product-name');
                const productId = button.getAttribute('data-product-id');
                
                document.getElementById('cartProductName').textContent = productName;
                
                const form = document.getElementById('removeFromCartForm');
                form.action = '{{ route("cart.remove", ":id") }}'.replace(':id', productId);
            });
        }

        // Handle checkout confirmation modal
        const checkoutConfirmationModal = document.getElementById('checkoutConfirmationModal');
        const confirmTermsCheckbox = document.getElementById('confirmTerms');
        const confirmCheckoutBtn = document.getElementById('confirmCheckoutBtn');
        const paymentMethodSelect = document.getElementById('payment_method');

        if (checkoutConfirmationModal) {
            checkoutConfirmationModal.addEventListener('show.bs.modal', function () {
                // Update payment method in modal
                const selectedPaymentMethod = paymentMethodSelect.value;
                document.getElementById('confirmPaymentMethod').textContent =
                    selectedPaymentMethod === 'COD' ? 'Cash on Delivery' : 'Pickup';
                document.getElementById('hiddenPaymentMethod').value = selectedPaymentMethod;

                // Populate modal with selected items
                const modalOrderItems = document.getElementById('modalOrderItems');
                modalOrderItems.innerHTML = '';

                let modalTotal = 0;
                selectedProducts.forEach(productId => {
                    const product = cartData[productId];
                    if (product) {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${product.name}</td>
                            <td class="text-center">${product.quantity}</td>
                            <td class="text-end">₱${product.price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                            <td class="text-end fw-semibold">₱${product.subtotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                        `;
                        modalOrderItems.appendChild(row);
                        modalTotal += product.subtotal;
                    }
                });

                document.getElementById('modalTotalAmount').textContent =
                    '₱' + modalTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                
                // Set selected products in hidden input
                document.getElementById('selectedProductsInput').value = JSON.stringify(selectedProducts);
                
                // Reset checkbox
                confirmTermsCheckbox.checked = false;
                confirmCheckoutBtn.disabled = true;
            });
        }
        
        // Enable/disable confirm button based on checkbox
        if (confirmTermsCheckbox) {
            confirmTermsCheckbox.addEventListener('change', function() {
                confirmCheckoutBtn.disabled = !this.checked;
            });
        }
        
        // Update payment method when changed
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', function() {
                document.getElementById('hiddenPaymentMethod').value = this.value;
            });
        }

        // Initial calculation
        updateSelectedTotal();
    });
</script>
{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection