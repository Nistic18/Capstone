@extends('layouts.app')
@section('title', 'Manage Products')
{{-- Add Bootstrap 5 CSS --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

{{-- Add Bootstrap 5 JavaScript --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
@endpush
@section('content')
<div class="mt-5">
    {{-- Header Section --}}
    <div class="card border-0 shadow-lg mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="fas fa-fish text-white" style="font-size: 3rem;"></i>
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">🐟 Product Management</h1>
            <p class="lead text-white-50 mb-4">Manage your fish market inventory and listings</p>
            
            {{-- Quick Stats --}}
            <div class="d-flex justify-content-center gap-4 flex-wrap">
                <div class="d-flex align-items-center px-3 py-2 rounded-pill" 
                     style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                    <i class="fas fa-boxes text-white me-2"></i>
                    <span class="text-white">{{ $products->total() }} Products</span>
                </div>
                @if($products->where('quantity', '>', 0)->count() > 0)
                <div class="d-flex align-items-center px-3 py-2 rounded-pill" 
                     style="background: rgba(40, 167, 69, 0.3); backdrop-filter: blur(10px);">
                    <i class="fas fa-check-circle text-white me-2"></i>
                    <span class="text-white">{{ $products->where('quantity', '>', 0)->count() }} In Stock</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Action Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color: #2c3e50;">
                <i class="fas fa-inventory me-2" style="color: #667eea;"></i>
                My Products
            </h2>
            <p class="text-muted mb-0">Manage your fish market listings</p>
        </div>
        
        <a class="btn btn-success px-4 py-2" 
           href="{{ route('products.create') }}" 
           style="border-radius: 15px; background: linear-gradient(45deg, #28a745, #20c997); border: none;">
            <i class="fas fa-plus me-2"></i>Add New Product
        </a>
    </div>

    {{-- Enhanced Search & Filter Section --}}
    <div class="card border-0 shadow-sm mb-5" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                {{-- Search Input with Icon --}}
                <div class="col-lg-5 col-md-6">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" name="search" class="form-control ps-5" 
                               style="border-radius: 25px; border: 2px solid #e9ecef;"
                               placeholder="Search your products..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Sort Dropdown --}}
                <div class="col-lg-4 col-md-6">
                    <select name="sort" class="form-select" 
                            style="border-radius: 25px; border: 2px solid #e9ecef;"
                            onchange="this.form.submit()">
                        <option value="">🔄 Sort by...</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>💰 Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>💸 Price: High to Low</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>🔤 Name: A to Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>🔤 Name: Z to A</option>
                    </select>
                </div>

                {{-- Search Button --}}
                <div class="col-lg-3 col-md-12">
                    <button type="submit" class="btn btn-primary w-100" 
                            style="border-radius: 25px; background: linear-gradient(45deg, #667eea, #764ba2); border: none;">
                        <i class="fas fa-search me-1"></i> Search & Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Results Count --}}
    @if(!$products->isEmpty())
    <div class="mb-4">
        <p class="text-muted mb-0">
            <i class="fas fa-fish me-2"></i>
            Found {{ $products->total() }} {{ $products->total() == 1 ? 'product' : 'products' }}
        </p>
    </div>
    @endif

    {{-- Products Display --}}
    @if($products->isEmpty())
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-fish text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
            <h3 class="text-muted mb-3">No Products Found</h3>
            <p class="text-muted">You haven't added any products yet or no products match your search criteria.</p>
            <a href="{{ route('products.create') }}" class="btn btn-primary" style="border-radius: 25px;">
                <i class="fas fa-plus me-2"></i>Add Your First Product
            </a>
        </div>
    @else
        <div class="row">
            @foreach($products as $product)
            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden" 
                     style="border-radius: 20px; transition: all 0.3s ease;"
                     onmouseover="this.style.transform='translateY(-5px)'; this.classList.add('shadow-lg')"
                     onmouseout="this.style.transform='translateY(0)'; this.classList.remove('shadow-lg')">
                    
                    {{-- Product Image with Status Badge - FIXED SECTION --}}
                    <div class="position-relative overflow-hidden" style="height: 220px; border-radius: 20px 20px 0 0;">
@if($product->images && $product->images->count())
    <img src="{{ asset('storage/' . $product->images->first()->image) }}"
         class="card-img-top w-100 h-100"
         style="object-fit: cover; transition: transform 0.3s ease; position: relative; z-index: 1; cursor: pointer;"
         alt="{{ $product->name }}"
         onmouseover="this.style.transform='scale(1.05)'"
         onmouseout="this.style.transform='scale(1)'"
         data-bs-toggle="modal" 
         data-bs-target="#imageModal-{{ $product->id }}">
@else
    <div class="w-100 h-100 d-flex align-items-center justify-content-center"
         style="background: linear-gradient(45deg, #f8f9fa, #e9ecef);">
        <div class="text-center">
            <i class="fas fa-fish text-muted mb-2" style="font-size: 3rem;"></i>
            <p class="text-muted mb-0">No Image</p>
        </div>
    </div>
@endif
                        
                        {{-- Stock Status Badge - FIXED --}}
                        <div class="position-absolute stock-badge-container" 
                             style="top: 12px; right: 12px; z-index: 1001;">
                            @if($product->quantity > 0)
                                <span class="badge stock-badge" 
                                      style="background: rgba(40, 167, 69, 0.95) !important; 
                                             border-radius: 20px; 
                                             padding: 8px 12px; 
                                             box-shadow: 0 4px 12px rgba(0,0,0,0.4); 
                                             border: 2px solid rgba(255,255,255,0.3); 
                                             font-weight: 600; 
                                             color: white !important;
                                             font-size: 0.75rem;
                                             white-space: nowrap;
                                             display: inline-block;">
                                    <i class="fas fa-check-circle me-1"></i>{{ $product->quantity }} in stock
                                </span>
                            @else
                                <span class="badge stock-badge" 
                                      style="background: rgba(220, 53, 69, 0.95) !important; 
                                             border-radius: 20px; 
                                             padding: 8px 12px; 
                                             box-shadow: 0 4px 12px rgba(0,0,0,0.4); 
                                             border: 2px solid rgba(255,255,255,0.3); 
                                             font-weight: 600; 
                                             color: white !important;
                                             font-size: 0.75rem;
                                             white-space: nowrap;
                                             display: inline-block;">
                                    <i class="fas fa-times-circle me-1"></i>Out of Stock
                                </span>
                            @endif
                        </div>

                        {{-- Quick Actions Overlay --}}
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0 quick-actions-overlay" 
                             style="background: rgba(0,0,0,0.7); transition: opacity 0.3s ease; z-index: 999;"
                             onmouseover="this.style.opacity='1'"
                             onmouseout="this.style.opacity='0'">
                            <div class="d-flex gap-2">
                                <a href="{{ route('products.edit', $product) }}" 
                                   class="btn btn-warning btn-sm" 
                                   style="border-radius: 10px;"
                                   data-bs-toggle="tooltip" 
                                   title="Edit Product">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-danger btn-sm" 
                                        style="border-radius: 10px;"
                                        onclick="confirmDelete('{{ $product->name }}', '{{ route('products.destroy', $product) }}')"
                                        data-bs-toggle="tooltip" 
                                        title="Delete Product">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Product Details --}}
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold mb-0" style="color: #2c3e50;">{{ $product->name }}</h5>
                            <span class="badge" style="background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 15px; color: #fff;">
                                🐟 Fresh
                            </span>
                        </div>
                        
                        <p class="card-text text-muted small mb-3" style="line-height: 1.5;">
                            {{ Str::limit($product->description, 80) }}
                        </p>

                        {{-- Price --}}
                        <div class="mb-3">
                            <span class="h4 fw-bold" style="color: #28a745;">
                                ${{ number_format($product->price, 2) }}
                            </span>
                            <small class="text-muted">/ piece</small>
                        </div>

                        {{-- Supplier Info --}}
                        @if(isset($product->user))
                            <div class="mb-3">
                                <div class="d-flex align-items-center p-2 rounded" 
                                     style="background-color: #f8f9fa; border-left: 3px solid #667eea;">
                                    <i class="fas fa-user-tie text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Supplier</small>
                                        <span class="fw-semibold" style="color: #667eea;">
                                            {{ $product->user->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Product Stats --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="text-center p-2 rounded" style="background: rgba(102, 126, 234, 0.1);">
                                    <i class="fas fa-eye text-primary mb-1"></i>
                                    <p class="small mb-0 text-muted">Views</p>
                                    <strong style="color: #2c3e50;">{{ rand(10, 500) }}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 rounded" style="background: rgba(40, 167, 69, 0.1);">
                                    <i class="fas fa-star text-warning mb-1"></i>
                                    <p class="small mb-0 text-muted">Rating</p>
                                    <strong style="color: #2c3e50;">{{ $product->averageRating() ? number_format($product->averageRating(), 1) : 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card Footer with Actions --}}
                    <div class="card-footer border-0 p-4 pt-0">
                        <div class="d-flex gap-2">
                            <a href="{{ route('products.edit', $product) }}" 
                               class="btn btn-outline-warning flex-fill" 
                               style="border-radius: 15px; border-width: 2px;">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            
                            <button type="button" 
                                    class="btn btn-outline-danger flex-fill" 
                                    style="border-radius: 15px; border-width: 2px;"
                                    onclick="confirmDelete('{{ $product->name }}', '{{ route('products.destroy', $product) }}')">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Image Modal --}}
<div class="modal fade" id="imageModal-{{ $product->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-body p-0">
                @if($product->images && $product->images->count())
                    <div id="carousel-{{ $product->id }}" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($product->images as $key => $image)
                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image->image) }}" 
                                         class="d-block w-100" 
                                         style="max-height: 600px; object-fit: contain;">
                                </div>
                            @endforeach
                        </div>
                        @if($product->images->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel-{{ $product->id }}" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carousel-{{ $product->id }}" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
            @endforeach
        </div>

        {{-- Enhanced Pagination --}}
        <div class="d-flex justify-content-center mt-5">
            <nav aria-label="Product pagination">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    @endif
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="deleteModalLabel" style="color: #2c3e50;">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Confirm Product Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-3">Are you sure you want to delete <strong id="productName"></strong>?</p>
                <div class="alert alert-warning border-0" style="background: rgba(255, 193, 7, 0.1) color:white;">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    This action cannot be undone and will permanently remove the product from your inventory.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="border-radius: 10px;">
                        <i class="fas fa-trash me-1"></i>Delete Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS - UPDATED WITH FIXES --}}
<style>
    
    .card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-success {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-success:hover {
        background: linear-gradient(45deg, #218838, #1ea085);
        transform: translateY(-1px);
    }
    
    .btn-outline-warning {
        border-color: #ffc107;
        color: #ffc107;
        transition: all 0.3s ease;
    }
    
    .btn-outline-warning:hover {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
        transform: translateY(-1px);
    }
    
    .btn-outline-danger {
        border-color: #dc3545;
        color: #dc3545;
        transition: all 0.3s ease;
    }
    
    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
        transform: translateY(-1px);
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: linear-gradient(45deg, #5a6fd8, #6a42a0);
        transform: translateY(-1px);
    }
    
    .pagination .page-link {
        border-radius: 10px;
        margin: 0 2px;
        border: 2px solid #e9ecef;
        color: #667eea;
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border-color: #667eea;
    }
    
    .alert {
        border: none;
    }
    
    /* FIXED: Stock Badge Positioning */
    .stock-badge-container {
        pointer-events: none;
    }
    
    .stock-badge {
        pointer-events: auto;
        position: relative !important;
        z-index: 1001 !important;
    }
    
    .card-img-top {
        position: relative;
        z-index: 1;
    }
    
    .quick-actions-overlay {
        pointer-events: none;
    }
    
    .quick-actions-overlay > div {
        pointer-events: auto;
    }
    
    /* Ensure badges stay visible on hover */
    .card:hover .stock-badge-container {
        z-index: 1002 !important;
    }
    
    @media (max-width: 768px) {
        .display-4 {
            font-size: 2rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .d-flex.gap-3 {
            flex-direction: column;
        }
        
        .flex-fill {
            width: 100%;
        }
        
        .stock-badge {
            font-size: 0.65rem !important;
            padding: 6px 10px !important;
        }
    }
</style>

{{-- JavaScript --}}
<script>
function confirmDelete(productName, deleteUrl) {
    document.getElementById('productName').textContent = productName;
    document.getElementById('deleteForm').action = deleteUrl;
    
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection