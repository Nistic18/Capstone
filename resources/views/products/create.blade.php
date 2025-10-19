@extends('layouts.app')
@section('title', 'Product')
@section('content')
<div class="card card-body mt-5">
    <h2>{{ isset($product) ? 'Edit Product' : 'Add Product' }}</h2>

    <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}"
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

        {{-- <div class="mb-3">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control"
                   value="{{ old('quantity', $product->quantity ?? '') }}" min="0" required>
        </div> --}}

        {{-- MULTIPLE IMAGES --}}
        <div class="mb-3">
            <label>Images</label>
            <input type="file" name="images[]" class="form-control" multiple>

            {{-- Show existing images when editing --}}
            @if(isset($product) && $product->images->count())
                <div class="d-flex flex-wrap mt-2 gap-2">
                    @foreach($product->images as $image)
                        <div class="position-relative" style="width: 100px;">
                            <img src="{{ asset('storage/' . $image->image) }}" 
                                 class="img-thumbnail w-100" style="height: 100px; object-fit: cover;">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">
            {{ isset($product) ? 'Update Product' : 'Create Product' }}
        </button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
