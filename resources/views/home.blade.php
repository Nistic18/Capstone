@php
    use Illuminate\Support\Str;
    use App\Models\Cart;

    $userId = auth()->check() ? auth()->id() : null;
@endphp

@extends('layouts.app')
@section('title', 'Fish Market')

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
    {{-- Hero Section --}}
    {{-- <div class="card border-0 shadow-lg mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="fas fa-fish text-white" style="font-size: 3rem;"></i>
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">🐠 FishMarket</h1>
            <p class="lead text-white-50 mb-0">Discover the finest selection of fresh fish from trusted sellers</p>
        </div>
    </div> --}}

    {{-- Enhanced Search & Filter Section --}}
    <div class="card border-0 shadow-sm mb-5" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('home') }}" class="row g-3">
                {{-- Search Input with Icon --}}
                <div class="col-lg-4 col-md-6">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" name="search" class="form-control ps-5" 
                               style="border-radius: 25px; border: 2px solid #e9ecef;"
                               placeholder="Search for fish species..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Price Range --}}
                <div class="col-lg-2 col-md-3">
                    <div class="position-relative">
                        <i class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="number" name="min_price" class="form-control ps-5" 
                               style="border-radius: 25px; border: 2px solid #e9ecef;"
                               placeholder="Min ₱"
                               value="{{ request('min_price') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3">
                    <input type="number" name="max_price" class="form-control" 
                           style="border-radius: 25px; border: 2px solid #e9ecef;"
                           placeholder="Max ₱"
                           value="{{ request('max_price') }}">
                </div>

                {{-- Sort Dropdown --}}
                <div class="col-lg-3 col-md-6">
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

                <div class="col-lg-1 col-md-12">
                    <button type="submit" class="btn btn-primary w-100" 
                            style="border-radius: 25px; background: linear-gradient(45deg, #667eea, #764ba2); border: none;">
                        <i class="fas fa-search me-1"></i> Filter
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
            Found {{ $products->total() }} fresh fish {{ $products->total() == 1 ? 'listing' : 'listings' }}
        </p>
    </div>
    @endif

    @if($products->isEmpty())
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-fish text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
            <h3 class="text-muted mb-3">No Fish Found</h3>
            <p class="text-muted">Try adjusting your search criteria or check back later for new listings!</p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="border-radius: 25px;">
                <i class="fas fa-refresh me-2"></i>View All Fish
            </a>
        </div>
    @else
    <div class="row">
        @foreach($products as $product)
        @php
            // Check if already in cart
            $inCart = $userId ? Cart::where('user_id', $userId)
                               ->where('product_id', $product->id)
                               ->exists() : false;
        @endphp

        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden" 
                 style="border-radius: 20px; transition: all 0.3s ease; cursor: pointer;"
                 onmouseover="this.style.transform='translateY(-5px)'; this.classList.add('shadow-lg')"
                 onmouseout="this.style.transform='translateY(0)'; this.classList.remove('shadow-lg')">
                
                {{-- Product Image with Overlay --}}
                <div class="position-relative overflow-hidden" style="height: 250px; border-radius: 20px 20px 0 0;">
@if($product->images->count() > 0)
    <div id="carousel-{{ $product->id }}" class="carousel slide custom-carousel" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($product->images as $key => $image)
                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                    <img src="{{ asset('storage/' . $image->image) }}" 
                         class="d-block w-100" 
                         style="height:250px; object-fit:cover;" 
                         alt="{{ $product->name }}">
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
@else
    <div class="w-100 h-100 d-flex align-items-center justify-content-center"
         style="background: linear-gradient(45deg, #f8f9fa, #e9ecef); height:250px;">
        <div class="text-center">
            <i class="fas fa-fish text-muted mb-2" style="font-size: 3rem;"></i>
            <p class="text-muted mb-0">No Image</p>
        </div>
    </div>
@endif
                    
                </div>
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
                            ₱{{ number_format($product->price, 2) }}
                        </span>
                        <small class="text-muted">/ piece</small>
                    </div>

                    {{-- Rating --}}
                    <div class="mb-3">
                        @if($product->averageRating())
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($product->averageRating()))
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                </div>
                                <small class="text-muted">
                                    {{ number_format($product->averageRating(), 1) }} 
                                    ({{ $product->reviews->count() }} {{ $product->reviews->count() == 1 ? 'review' : 'reviews' }})
                                </small>
                            </div>
                        @else
                            <small class="text-muted">
                                <i class="far fa-star text-muted me-1"></i>No reviews yet
                            </small>
                        @endif
                    </div>

                    {{-- Seller Info --}}
                    @if($product->user)
                        <div class="mb-3">
                            <div class="d-flex align-items-center p-2 rounded" 
                                 style="background-color: #f8f9fa; border-left: 3px solid #667eea;">
                                <i class="fas fa-user-circle text-primary me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Seller</small>
                                    <a href="{{ route('profile.show', $product->user->id) }}" 
                                       class="fw-semibold text-decoration-none" style="color: #667eea;">
                                        {{ $product->user->name }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="mt-auto">
                        {{-- View Details Button --}}
                        <a href="{{ route('products.show', $product) }}" 
                           class="btn btn-outline-primary w-100 mb-2" 
                           style="border-radius: 15px; border-width: 2px;">
                            <i class="fas fa-eye me-2"></i>View Details
                        </a>

                        {{-- Add to Cart / Cart Status --}}
                        @if($product->quantity > 0)
                            @if($product->user_id == $userId)
                                <div class="alert alert-info text-center mb-0 py-2" style="border-radius: 15px;">
                                    <i class="fas fa-ban me-2"></i>You cannot order your own product
                                </div>
                            @elseif($inCart)
                                <div class="alert alert-warning text-center mb-0 py-2" style="border-radius: 15px;">
                                    <i class="fas fa-shopping-cart me-2"></i>Already in Cart
                                </div>
                            @else
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <div class="row g-2 mb-2">
                                        <div class="col-4">
                                            <label for="quantity-{{ $product->id }}" class="form-label small">Quantity</label>
                                            <input type="number" id="quantity-{{ $product->id }}" 
                                                   name="quantity" value="1" min="1" max="{{ $product->quantity }}"
                                                   class="form-control form-control-sm text-center" 
                                                   style="border-radius: 10px;">
                                        </div>
                                        <div class="col-8 d-flex align-items-end">
                                            <button type="submit" class="btn btn-success w-100" 
                                                    style="border-radius: 15px; background: linear-gradient(45deg, #28a745, #20c997);">
                                                <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        @else
                            <div class="alert alert-danger text-center mb-0 py-2" style="border-radius: 15px;">
                                <i class="fas fa-times-circle me-2"></i>Currently Unavailable
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

{{-- Custom CSS --}}
<style>
/* Hide next/prev buttons by default */
.custom-carousel .carousel-control-prev,
.custom-carousel .carousel-control-next {
    opacity: 0;
    transition: opacity 0.4s ease, transform 0.4s ease;
    transform: translateX(0); /* default reset */
}

/* On hover, fade in with slight slide */
.custom-carousel:hover .carousel-control-prev {
    opacity: 0.5;
    transform: translateX(10px); /* slide in from left */
}

.custom-carousel:hover .carousel-control-next {
    opacity: 0.5;
    transform: translateX(-10px); /* slide in from right */
}

/* When hovering directly on the button, make it brighter */
.custom-carousel .carousel-control-prev:hover,
.custom-carousel .carousel-control-next:hover {
    opacity: 0.9;
    transform: translateX(0); /* reset to center */
}

    .card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
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
    
    @media (max-width: 768px) {
        .display-4 {
            font-size: 2rem;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
</style>

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection