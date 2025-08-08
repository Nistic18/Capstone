@extends('layouts.app')

@section('content')
<div class="card card-body mt-5">
    <h2>{{ isset($product) ? 'Edit Product' : 'Add Product' }}</h2>

<form action="{{ isset($product) ? route('supplierproduct.update', ['supplierproduct' => $product->id]) : route('supplierproduct.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($product)) @method('PUT') @endif


        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $product->name ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" class="form-control"
                   value="{{ old('price', $product->price ?? '') }}" step="0.01" required>
        </div>

        <div class="mb-3">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control"
                   value="{{ old('quantity', $product->quantity ?? '') }}" min="0" required>
        </div>

        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="image" class="form-control">
            @if(isset($product) && $product->image)
                <img src="{{ asset('storage/' . $product->image) }}"
                     width="100" class="mt-2 img-thumbnail">
            @endif
        </div>

        <button type="submit" class="btn btn-primary">
            {{ isset($product) ? 'Update Product' : 'Create Product' }}
        </button>
        <a href="{{ route('supplierproduct.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
