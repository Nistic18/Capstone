@php
    use Illuminate\Support\Str;
    use App\Models\Cart;

    $userId = auth()->check() ? auth()->id() : null;
@endphp

@extends('layouts.app')

@section('content')
<div class="mt-5">
    <div class="card card-body">
        <h2 class="fw-bold mb-0 text-primary">Browse Products</h2>
    </div>

    @if($products->isEmpty())
        <div class="alert alert-info text-center">No products available.</div>
    @else
    <div class="row">
        @foreach($products as $product)
        @php
            // Check if already in cart
            $inCart = $userId ? Cart::where('user_id', $userId)
                               ->where('product_id', $product->id)
                               ->exists() : false;
        @endphp

        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm position-relative">
                {{-- Product Image --}}
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}"
                         class="card-img-top rounded-top"
                         style="height: 200px; object-fit: cover;"
                         alt="{{ $product->name }}">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center rounded-top"
                         style="height: 200px;">
                        <span class="text-muted">No Image</span>
                    </div>
                @endif

                {{-- Product Details --}}
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text text-muted small mb-2">{{ Str::limit($product->description, 100) }}</p>
                    <p class="fw-semibold text-primary fs-5 mb-3">${{ number_format($product->price, 2) }}</p>

                    @if($product->user)
                        <span class="badge bg-secondary mb-2 align-self-start">
                            Supplier: {{ $product->user->name }}
                        </span>
                    @endif

                    {{-- View Details always visible --}}
                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm w-100 mb-2 mt-auto">
                        View Details
                    </a>

                    {{-- Add to Cart / Already in Cart / Availability --}}
                    @if($product->quantity > 0)
                        @if($inCart)
                            <span class="badge bg-warning text-dark w-100 py-2">Already in Cart</span>
                        @else
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <label for="quantity-{{ $product->id }}" class="form-label small">Quantity</label>
                                    <input type="number" id="quantity-{{ $product->id }}" name="quantity" value="1" min="1"
                                           max="{{ $product->quantity }}"
                                           class="form-control form-control-sm text-center mx-auto" style="max-width: 100px;">
                                </div>
                                <button type="submit" class="btn btn-outline-success btn-sm w-100 mb-2">
                                    Add to Cart
                                </button>
                            </form>
                        @endif

                        {{-- Availability badge shown when stock > 0 --}}
                        <div class="mt-2">
                            <span class="badge bg-success w-100 py-2">Available</span>
                        </div>
                    @else
                        {{-- Out of Stock Badge --}}
                        <span class="badge bg-danger w-100 py-2">Out of Stock</span>
                    @endif

                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
