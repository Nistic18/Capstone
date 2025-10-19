@extends('layouts.app')
@section('title', 'Inventory Management')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<div class="container mt-5">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #2c3e50;">
                <i class="fas fa-boxes me-2" style="color: #667eea;"></i>
                Inventory Management
            </h2>
            <p class="text-muted mb-0">Track and manage your product stock levels</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Products</p>
                            <h3 class="fw-bold mb-0">{{ $stats['total_products'] }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Out of Stock</p>
                            <h3 class="fw-bold mb-0">{{ $stats['out_of_stock'] }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Low Stock</p>
                            <h3 class="fw-bold mb-0">{{ $stats['low_stock'] }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-battery-quarter fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Value</p>
                            <h3 class="fw-bold mb-0">₱{{ number_format($stats['total_value'], 2) }}</h3>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('inventory.index') }}" class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" name="search" class="form-control ps-5" 
                               style="border-radius: 25px;"
                               placeholder="Search products..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <select name="stock_status" class="form-select" style="border-radius: 25px;" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <select name="sort" class="form-select" style="border-radius: 25px;" onchange="this.form.submit()">
                        <option value="">Sort by...</option>
                        <option value="quantity_asc" {{ request('sort') == 'quantity_asc' ? 'selected' : '' }}>Quantity: Low to High</option>
                        <option value="quantity_desc" {{ request('sort') == 'quantity_desc' ? 'selected' : '' }}>Quantity: High to Low</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius: 25px;">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="border-0 px-4 py-3">Product</th>
                            <th class="border-0 py-3">Price</th>
                            <th class="border-0 py-3">Current Stock</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3">Low Stock Alert</th>
                            <th class="border-0 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    @if($product->images && $product->images->count())
                                        <img src="{{ asset('storage/' . $product->images->first()->image) }}" 
                                             alt="{{ $product->name }}"
                                             class="rounded me-3"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-fish text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <small class="text-muted">ID: {{ $product->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="fw-semibold text-success">₱{{ number_format($product->price, 2) }}</span>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-secondary px-3 py-2" style="font-size: 0.9rem;">
                                    {{ $product->quantity }} units
                                </span>
                            </td>
                            <td class="py-3">
                                @if($product->isOutOfStock())
                                    <span class="badge bg-danger px-3 py-2">
                                        <i class="fas fa-times-circle me-1"></i>Out of Stock
                                    </span>
                                @elseif($product->isLowStock())
                                    <span class="badge bg-warning px-3 py-2">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
                                    </span>
                                @else
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>In Stock
                                    </span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="text-muted">≤ {{ $product->low_stock_threshold }} units</span>
                            </td>
                            <td class="py-3 text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            onclick="openAdjustModal({{ $product->id }}, '{{ $product->name }}', {{ $product->quantity }}, {{ $product->low_stock_threshold }})"
                                            style="border-radius: 8px 0 0 8px;">
                                        <i class="fas fa-edit me-1"></i>Adjust
                                    </button>
                                    <a href="{{ route('inventory.history', $product) }}" 
                                       class="btn btn-sm btn-outline-secondary"
                                       style="border-radius: 0 8px 8px 0;">
                                        <i class="fas fa-history"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-box-open text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3">No products found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $products->appends(request()->query())->links() }}
    </div>
    @endif
</div>

{{-- Adjust Stock Modal --}}
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-boxes me-2 text-primary"></i>
                    Adjust Inventory
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="adjustForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product</label>
                        <input type="text" id="modalProductName" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Stock</label>
                        <input type="text" id="modalCurrentStock" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Action</label>
                        <select name="action" class="form-select" required>
                            <option value="add">Add Stock</option>
                            <option value="remove">Remove Stock</option>
                            <option value="set">Set Stock</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason</label>
                        <select name="reason" class="form-select" required>
                            <option value="restock">Restock</option>
                            <option value="sale">Sale</option>
                            <option value="adjustment">Adjustment</option>
                            <option value="damaged">Damaged</option>
                            <option value="returned">Returned</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Low Stock Threshold</label>
                        <input type="number" id="modalThreshold" name="low_stock_threshold" class="form-control" min="0">
                        <small class="text-muted">You'll be alerted when stock reaches this level</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openAdjustModal(productId, productName, currentStock, threshold) {
    document.getElementById('modalProductName').value = productName;
    document.getElementById('modalCurrentStock').value = currentStock + ' units';
    document.getElementById('modalThreshold').value = threshold;
    document.getElementById('adjustForm').action = '/inventory/' + productId + '/adjust';
    
    new bootstrap.Modal(document.getElementById('adjustModal')).show();
}
</script>
@endpush

@endsection