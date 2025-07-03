@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Products</h2>
        <a class="btn btn-primary" href="{{ route('products.create') }}">+ Add Product</a>
    </div>

    @if($products->isEmpty())
        <div class="alert alert-info">No products found.</div>
    @else
    <div class="row">
        @foreach($products as $product)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}"
                         class="card-img-top"
                         style="height: 200px; object-fit: cover;"
                         alt="{{ $product->name }}">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                         style="height: 200px;">
                        <span class="text-muted">No Image</span>
                    </div>
                @endif

                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                    <p class="card-text fw-bold">${{ number_format($product->price, 2) }}</p>
                        @if(isset($product->user))
                        <span class="badge bg-secondary text-white">
                        Supplier: {{ $product->user->name }}
                        </span>
                        @endif
                </div>
                <div class="card-footer bg-white border-top-0">
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection