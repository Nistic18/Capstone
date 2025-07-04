@extends('layouts.app')

@section('content')
<div class=" mt-5">
        <div class="card card-body">
            <h2 class="fw-bold mb-0 text-primary">Browse Products</h2>
        </div>
    @php use Illuminate\Support\Str; @endphp

    @if($products->isEmpty())
        <div class="alert alert-info text-center">No products available.</div>
    @else
    <div class="row">
        @foreach($products as $product)
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm position-relative">
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

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text text-muted small mb-2">{{ Str::limit($product->description, 100) }}</p>
                    <p class="fw-semibold text-primary fs-5 mb-3">${{ number_format($product->price, 2) }}</p>

                    @if($product->user)
                        <span class="badge bg-secondary mb-2 align-self-start">Supplier: {{ $product->user->name }}</span>
                    @endif

                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm w-100 mb-2 mt-auto">
                        View Details
                    </a>

                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label for="quantity-{{ $product->id }}" class="form-label small">Quantity</label>
                            <input type="number" id="quantity-{{ $product->id }}" name="quantity" value="1" min="1"
                                   class="form-control form-control-sm text-center mx-auto" style="max-width: 100px;">
                        </div>
                        <button type="submit" class="btn btn-outline-success btn-sm w-100 mb-2">
                            Add to Cart
                        </button>
                    </form>

                    <div class="text-center">
                        @if($product->status === 'sold')
                            <span class="badge bg-danger">Sold</span>
                        @else
                            <span class="badge bg-success">Available</span>
                        @endif
                    </div>
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
