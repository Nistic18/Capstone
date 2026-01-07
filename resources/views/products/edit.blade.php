@extends('layouts.app')
@section('title', isset($product) ? 'Edit Product' : 'Create Product')
<link rel="icon" type="image/png" href="{{ asset('img/avatar/dried-fish-logo.png') }}">
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<div class="mt-5">
    {{-- Header --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1" style="color: #2c3e50;">
            <i class="fas fa-{{ isset($product) ? 'edit' : 'plus-circle' }} me-2" style="color: #015f4b;"></i>
            {{ isset($product) ? 'Edit Product' : 'Create New Product' }}
        </h2>
        <p class="text-muted mb-0">Fill in the product details below</p>
    </div>

    {{-- Form Card --}}
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <form method="POST" 
                  action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" 
                  enctype="multipart/form-data">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif

                <div class="row g-4">
                    {{-- Product Name --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-fish text-primary me-1"></i>Product Name
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $product->name ?? '') }}"
                               placeholder="e.g., Fresh Tilapia"
                               required
                               style="border-radius: 15px;">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Price --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-tag text-success me-1"></i>Price (₱)
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               step="0.01" 
                               name="price" 
                               class="form-control @error('price') is-invalid @enderror" 
                               value="{{ old('price', $product->price ?? '') }}"
                               placeholder="0.00"
                               required
                               style="border-radius: 15px;">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Unit Type --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-box text-info me-1"></i>Unit Type
                            <span class="text-danger">*</span>
                        </label>
                        <select name="unit_type" 
                                id="unit_type"
                                class="form-select @error('unit_type') is-invalid @enderror" 
                                required
                                style="border-radius: 15px;"
                                onchange="updateUnitPlaceholder()">
                            <option value="">Select unit type...</option>
                            <option value="pack" {{ old('unit_type', $product->unit_type ?? '') == 'pack' ? 'selected' : '' }}>
                                📦 Per Pack (pieces)
                            </option>
                            <option value="kilo" {{ old('unit_type', $product->unit_type ?? '') == 'kilo' ? 'selected' : '' }}>
                                ⚖️ Per Kilo (kg)
                            </option>
                            <option value="box" {{ old('unit_type', $product->unit_type ?? '') == 'box' ? 'selected' : '' }}>
                                📦 Per Box (kg)
                            </option>
                            <option value="piece" {{ old('unit_type', $product->unit_type ?? '') == 'piece' ? 'selected' : '' }}>
                                🐟 Per Piece
                            </option>
                        </select>
                        @error('unit_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Unit Value --}}
                    <div class="col-md-6" id="unit_value_container">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-balance-scale text-warning me-1"></i>
                            <span id="unit_value_label">Unit Value</span>
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               step="0.01" 
                               name="unit_value" 
                               id="unit_value"
                               class="form-control @error('unit_value') is-invalid @enderror" 
                               value="{{ old('unit_value', $product->unit_value ?? '') }}"
                               placeholder="Enter value"
                               required
                               style="border-radius: 15px;">
                        <small class="text-muted" id="unit_value_hint">
                            Enter the quantity/weight for this unit
                        </small>
                        @error('unit_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Quantity --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-boxes text-primary me-1"></i>Stock Quantity
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               name="quantity" 
                               class="form-control @error('quantity') is-invalid @enderror" 
                               value="{{ old('quantity', $product->quantity ?? 0) }}"
                               min="0"
                               required
                               style="border-radius: 15px;">
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Low Stock Threshold --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>Low Stock Alert
                        </label>
                        <input type="number" 
                               name="low_stock_threshold" 
                               class="form-control @error('low_stock_threshold') is-invalid @enderror" 
                               value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 10) }}"
                               min="0"
                               placeholder="10"
                               style="border-radius: 15px;">
                        <small class="text-muted">Alert when stock falls below this number</small>
                        @error('low_stock_threshold')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-folder text-warning me-1"></i>Category
                            <span class="text-danger">*</span>
                        </label>
                        <select name="product_category_id" 
                                class="form-select @error('product_category_id') is-invalid @enderror" 
                                required
                                style="border-radius: 15px;">
                            <option value="">Select category...</option>
                            @foreach($productCategories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ old('product_category_id', $product->product_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Type --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-tag text-info me-1"></i>Product Type
                            <span class="text-danger">*</span>
                        </label>
                        <select name="product_type_id" 
                                class="form-select @error('product_type_id') is-invalid @enderror" 
                                required
                                style="border-radius: 15px;">
                            <option value="">Select type...</option>
                            @foreach($productTypes as $type)
                                <option value="{{ $type->id }}" 
                                        {{ old('product_type_id', $product->product_type_id ?? '') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-align-left text-secondary me-1"></i>Description
                        </label>
                        <textarea name="description" 
                                  rows="4" 
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Describe your product..."
                                  style="border-radius: 15px;">{{ old('description', $product->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Product Images --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-images text-primary me-1"></i>Product Images
                        </label>
                        <input type="file" 
                               name="images[]" 
                               class="form-control @error('images.*') is-invalid @enderror" 
                               accept="image/jpeg,image/jpg,image/png,image/webp"
                               multiple
                               style="border-radius: 15px;">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Upload multiple images (JPG, PNG, WEBP, max 2MB each)
                        </small>
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- Existing Images --}}
@if(isset($product) && $product->images->count() > 0)
    <div class="mt-3">
        <p class="fw-semibold mb-2">
            <i class="fas fa-image me-1"></i>Current Images:
        </p>
        <div class="row g-3">
            @foreach($product->images as $image)
                <div class="col-md-3 col-sm-4 col-6">
                    <div class="position-relative">
                        <img src="{{ asset($image->image) }}" 
                             alt="Product Image"
                             class="img-thumbnail w-100" 
                             style="height: 120px; object-fit: cover; border-radius: 10px;">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

                    {{-- Unit Summary Display --}}
                    <div class="col-12">
                        <div class="alert alert-info border-0 d-none" 
                             id="unit_summary"
                             style="background: rgba(13, 202, 240, 0.1); border-radius: 15px;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Product Unit:</strong> <span id="unit_summary_text">-</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-12">
                        <hr class="my-4">
                        <div class="d-flex gap-3 justify-content-end">
                            <a href="{{ route('products.index') }}" 
                               class="btn btn-outline-secondary px-4" 
                               style="border-radius: 15px;">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" 
                                    class="btn btn-success px-4" 
                                    style="border-radius: 15px; background: linear-gradient(45deg, #28a745, #20c997); border: none;">
                                <i class="fas fa-{{ isset($product) ? 'save' : 'plus-circle' }} me-2"></i>
                                {{ isset($product) ? 'Update Product' : 'Create Product' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Custom Styles --}}
<style>
    .form-control:focus, .form-select:focus {
        border-color: #015f4b;
        box-shadow: 0 0 0 0.2rem rgba(1, 95, 75, 0.25);
    }
    
    .btn-success {
        transition: all 0.3s ease;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }
    
    .btn-outline-secondary:hover {
        transform: translateY(-2px);
    }
    
    .form-label {
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .card {
        transition: all 0.3s ease;
    }
</style>

{{-- JavaScript for Dynamic Unit Display --}}
<script>
function updateUnitPlaceholder() {
    const unitType = document.getElementById('unit_type').value;
    const unitValueInput = document.getElementById('unit_value');
    const unitValueLabel = document.getElementById('unit_value_label');
    const unitValueHint = document.getElementById('unit_value_hint');
    const unitValueContainer = document.getElementById('unit_value_container');
    const unitSummary = document.getElementById('unit_summary');
    const unitSummaryText = document.getElementById('unit_summary_text');
    
    if (unitType === 'piece') {
        unitValueContainer.classList.add('d-none');
        unitValueInput.value = '1';
        unitValueInput.removeAttribute('required');
        unitSummary.classList.remove('d-none');
        unitSummaryText.textContent = 'Sold per piece';
    } else {
        unitValueContainer.classList.remove('d-none');
        unitValueInput.setAttribute('required', 'required');
        
        switch(unitType) {
            case 'pack':
                unitValueLabel.textContent = 'Pieces per Pack';
                unitValueInput.placeholder = 'e.g., 12';
                unitValueHint.textContent = 'How many pieces are in one pack?';
                if (unitValueInput.value) {
                    unitSummary.classList.remove('d-none');
                    unitSummaryText.textContent = unitValueInput.value + ' pieces per pack';
                }
                break;
            case 'kilo':
                unitValueLabel.textContent = 'Weight (kg)';
                unitValueInput.placeholder = 'e.g., 2.5';
                unitValueHint.textContent = 'Weight in kilograms';
                if (unitValueInput.value) {
                    unitSummary.classList.remove('d-none');
                    unitSummaryText.textContent = unitValueInput.value + ' kg';
                }
                break;
            case 'box':
                unitValueLabel.textContent = 'Weight per Box (kg)';
                unitValueInput.placeholder = 'e.g., 10';
                unitValueHint.textContent = 'How many kilograms in one box?';
                if (unitValueInput.value) {
                    unitSummary.classList.remove('d-none');
                    unitSummaryText.textContent = unitValueInput.value + ' kg per box';
                }
                break;
            default:
                unitSummary.classList.add('d-none');
        }
    }
}

// Update summary when value changes
document.addEventListener('DOMContentLoaded', function() {
    updateUnitPlaceholder();
    
    const unitValueInput = document.getElementById('unit_value');
    unitValueInput.addEventListener('input', updateUnitPlaceholder);
});
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush
@endsection