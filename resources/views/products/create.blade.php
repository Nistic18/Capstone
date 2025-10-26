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

        
{{-- ✅ Show Quantity only when adding a new product --}}
{{-- Show Quantity --}}
<div class="mb-3">
    <label>Quantity</label>

    @if(isset($product))
        {{-- Editing: show quantity but readonly --}}
        <input type="number" name="quantity" class="form-control" 
               value="{{ old('quantity', $product->quantity) }}" readonly>
    @else
        {{-- Adding: editable quantity --}}
        <input type="number" name="quantity" class="form-control" 
               value="{{ old('quantity', 0) }}" min="0" required>
    @endif
</div>

        {{-- ✅ Product Type --}}
        <div class="mb-3">
            <label for="product_type_id">Product Type</label>
            <select name="product_type_id" id="product_type_id" class="form-control" required>
                <option value="">-- Select Product Type --</option>
                @foreach($productTypes as $type)
                    <option value="{{ $type->id }}"
                        {{ old('product_type_id', $product->product_type_id ?? '') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ✅ Product Category --}}
        <div class="mb-3">
            <label for="product_category_id">Product Category</label>
            <select name="product_category_id" id="product_category_id" class="form-control" required>
                <option value="">-- Select Product Category --</option>
                @foreach($productCategories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('product_category_id', $product->product_category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

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
    @if($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
</div>
@endsection
