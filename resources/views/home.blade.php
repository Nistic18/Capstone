@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Browse Products</h2>

    @php use Illuminate\Support\Str; @endphp

    @if($products->isEmpty())
        <div class="alert alert-info">No products available.</div>
    @else
    <div class="row">
        @foreach($products as $product)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100"> {{-- ✅ Added shadow & border --}}
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

                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                    <p class="fw-bold text-primary">${{ number_format($product->price, 2) }}</p>
                </div>
                @if($product->user)
                <div class="px-3 pb-2">
                    <span class="badge bg-secondary text-white">Supplier: {{ $product->user->name }}</span>
                </div>
                @endif
                <div class="card-footer bg-transparent border-0 text-center">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary w-100">
                        View Details
                    </a>
                
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mt-2">
                    @csrf
                    <div class="mb-2">
                        <label for="quantity-{{ $product->id }}" class="form-label">Quantity</label>
                        <input type="number" id="quantity-{{ $product->id }}" name="quantity" value="1" min="1" class="form-control form-control-sm text-center" style="max-width: 100px; margin: 0 auto;">
                    </div>
                        <button type="submit" class="btn btn-sm btn-outline-success w-100">
                            Add to Cart
                        </button>
                </form>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
