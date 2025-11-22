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

        {{-- Unit Type and Unit Value --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="unit_type">Unit Type <span class="text-danger">*</span></label>
                <select name="unit_type" id="unit_type" class="form-control" required>
                    <option value="">-- Select Unit Type --</option>
                    <option value="pack" {{ old('unit_type', $product->unit_type ?? '') == 'pack' ? 'selected' : '' }}>
                        Pack (pieces per pack)
                    </option>
                    <option value="kilo" {{ old('unit_type', $product->unit_type ?? '') == 'kilo' ? 'selected' : '' }}>
                        Kilo (kg)
                    </option>
                    <option value="box" {{ old('unit_type', $product->unit_type ?? '') == 'box' ? 'selected' : '' }}>
                        Box (kg per box)
                    </option>
                    <option value="piece" {{ old('unit_type', $product->unit_type ?? '') == 'piece' ? 'selected' : '' }}>
                        Piece
                    </option>
                </select>
                <small class="text-muted">Select how this product is sold</small>
            </div>

            <div class="col-md-6 mb-3">
                <label for="unit_value">Unit Value <span class="text-danger">*</span></label>
                <input type="number" name="unit_value" id="unit_value" class="form-control"
                       value="{{ old('unit_value', $product->unit_value ?? '') }}" 
                       step="0.01" min="0.01" required>
                <small class="text-muted" id="unit_value_hint">
                    @if(isset($product) && $product->unit_type)
                        @switch($product->unit_type)
                            @case('pack')
                                Number of pieces per pack
                                @break
                            @case('kilo')
                                Weight in kilograms
                                @break
                            @case('box')
                                Weight in kg per box
                                @break
                            @case('piece')
                                Enter 1 for single piece
                                @break
                        @endswitch
                    @else
                        Enter the quantity per unit
                    @endif
                </small>
            </div>
        </div>

        {{-- Show Quantity --}}
        <div class="mb-3">
            <label>Quantity in Stock</label>

            @if(isset($product))
                {{-- Editing: show quantity but readonly --}}
                <input type="number" name="quantity" class="form-control" 
                       value="{{ old('quantity', $product->quantity) }}" readonly>
                <small class="text-muted">To adjust inventory, use the Inventory Management page</small>
            @else
                {{-- Adding: editable quantity --}}
                <input type="number" name="quantity" class="form-control" 
                       value="{{ old('quantity', 0) }}" min="0" required>
                <small class="text-muted">Initial stock quantity</small>
            @endif
        </div>

        {{-- Low Stock Threshold --}}
        <div class="mb-3">
            <label for="low_stock_threshold">Low Stock Threshold (Optional)</label>
            <input type="number" name="low_stock_threshold" id="low_stock_threshold" class="form-control"
                   value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 10) }}" min="0">
            <small class="text-muted">You'll be notified when stock falls below this number (default: 10)</small>
        </div>

        {{-- Product Type --}}
        <div class="mb-3">
            <label for="product_type_id">Product Type <span class="text-danger">*</span></label>
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

        {{-- Product Category --}}
        <div class="mb-3">
            <label for="product_category_id">Product Category <span class="text-danger">*</span></label>
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
            <label>Product Images</label>
            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
            <small class="text-muted">You can upload multiple images (JPG, PNG, WEBP). Max 2MB per image.</small>

            {{-- Show existing images when editing --}}
            @if(isset($product) && $product->images->count())
                <div class="mt-3">
                    <label class="d-block mb-2">Current Images:</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($product->images as $image)
                            <div class="position-relative" style="width: 120px;">
                                <img src="{{ asset('storage/' . $image->image) }}" 
                                     class="img-thumbnail w-100" 
                                     style="height: 120px; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>
                {{ isset($product) ? 'Update Product' : 'Create Product' }}
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>
                Cancel
            </a>
        </div>
    </form>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

{{-- JavaScript for Dynamic Unit Value Hint --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitTypeSelect = document.getElementById('unit_type');
    const unitValueHint = document.getElementById('unit_value_hint');
    const unitValueInput = document.getElementById('unit_value');

    unitTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        
        switch(selectedType) {
            case 'pack':
                unitValueHint.textContent = 'Number of pieces per pack (e.g., 5 for 5 pieces per pack)';
                unitValueInput.placeholder = 'e.g., 5';
                break;
            case 'kilo':
                unitValueHint.textContent = 'Weight in kilograms (e.g., 2 for 2 kg)';
                unitValueInput.placeholder = 'e.g., 2.5';
                break;
            case 'box':
                unitValueHint.textContent = 'Weight in kg per box (e.g., 10 for 10 kg per box)';
                unitValueInput.placeholder = 'e.g., 10';
                break;
            case 'piece':
                unitValueHint.textContent = 'Enter 1 for single piece';
                unitValueInput.placeholder = '1';
                unitValueInput.value = '1';
                break;
            default:
                unitValueHint.textContent = 'Enter the quantity per unit';
                unitValueInput.placeholder = '';
        }
    });

    // Trigger on page load if editing
    if(unitTypeSelect.value) {
        unitTypeSelect.dispatchEvent(new Event('change'));
    }
});
</script>

<style>
.gap-2 {
    gap: 0.5rem;
}

.form-control:focus, .form-select:focus {
    border-color: #0bb364;
    box-shadow: 0 0 0 0.2rem rgba(11, 179, 100, 0.25);
}

.btn-primary {
    background: linear-gradient(45deg, #0bb364, #0bb364);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #099951, #099951);
    transform: translateY(-1px);
}

.text-danger {
    color: #dc3545;
}

.img-thumbnail {
    border: 2px solid #dee2e6;
    border-radius: 8px;
}

.alert-danger {
    border-left: 4px solid #dc3545;
}
</style>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection