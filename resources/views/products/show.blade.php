@php
    use App\Models\Cart;
    $userId = auth()->check() ? auth()->id() : null;
    $inCart = $userId ? Cart::where('user_id', $userId)->where('product_id', $product->id)->exists() : false;
@endphp

@extends('layouts.app')
@section('title', 'Product')
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
<div class="container mt-4">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background: transparent; padding: 20px;">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none" style="color: #667eea;">
                    <i class="fas fa-home me-1"></i>Home
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none" style="color: #667eea;">
                    <i class="fas fa-fish me-1"></i>Products
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Product Image Section --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="position-relative">
@if($product->images && $product->images->count())
    {{-- Main Image --}}
    <img id="main-product-image"
         src="{{ asset('storage/' . $product->images->first()->image) }}" 
         alt="{{ $product->name }}" 
         class="card-img-top w-100 mb-3"
         style="height: 400px; object-fit: cover; border-radius: 10px;">

    {{-- Thumbnails --}}
    @if($product->images->count() > 1)
        <div class="row g-2">
            @foreach($product->images as $key => $image)
                <div class="col-3">
                    <img src="{{ asset('storage/' . $image->image) }}" 
                         alt="Thumbnail {{ $key+1 }}" 
                         class="img-fluid rounded shadow-sm thumbnail-image"
                         style="height: 80px; width: 100%; object-fit: cover; cursor: pointer;"
                         onclick="document.getElementById('main-product-image').src=this.src">
                </div>
            @endforeach
        </div>
    @endif
@else
    <div class="d-flex align-items-center justify-content-center" 
         style="height: 400px; background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-radius: 10px;">
        <div class="text-center">
            <i class="fas fa-fish text-muted mb-3" style="font-size: 4rem;"></i>
            <h5 class="text-muted">No Image Available</h5>
            <p class="text-muted mb-0">Product image coming soon</p>
        </div>
    </div>
@endif
                    
                    {{-- Stock Status Badge --}}
                    @if($product->quantity > 0)
                        <span class="badge position-absolute top-0 end-0 m-3" 
                              style="background: rgba(40, 167, 69, 0.9); border-radius: 20px; padding: 10px 15px; font-size: 0.9rem;">
                            <i class="fas fa-check-circle me-1"></i>{{ $product->quantity }} Available
                        </span>
                    @else
                        <span class="badge position-absolute top-0 end-0 m-3" 
                              style="background: rgba(220, 53, 69, 0.9); border-radius: 20px; padding: 10px 15px; font-size: 0.9rem;">
                            <i class="fas fa-times-circle me-1"></i>Out of Stock
                        </span>
                    @endif

                    {{-- Fresh Badge --}}
                    <span class="badge position-absolute top-0 start-0 m-3" 
                          style="background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 20px; padding: 10px 15px; font-size: 0.9rem;">
                        <i class="fas fa-fish me-1"></i>Fresh Fish
                    </span>
                </div>
            </div>

            {{-- Additional Product Images (if you have multiple images in future) --}}
            {{-- <div class="row g-2 mt-3">
                <div class="col-3">
                    <div class="card border-0 shadow-sm" style="border-radius: 10px; height: 80px; opacity: 0.7;">
                        <div class="card-body d-flex align-items-center justify-content-center p-2">
                            <i class="fas fa-images text-muted"></i>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card border-0 shadow-sm" style="border-radius: 10px; height: 80px; opacity: 0.7;">
                        <div class="card-body d-flex align-items-center justify-content-center p-2">
                            <i class="fas fa-images text-muted"></i>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card border-0 shadow-sm" style="border-radius: 10px; height: 80px; opacity: 0.7;">
                        <div class="card-body d-flex align-items-center justify-content-center p-2">
                            <i class="fas fa-images text-muted"></i>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card border-0 shadow-sm" style="border-radius: 10px; height: 80px; opacity: 0.7;">
                        <div class="card-body d-flex align-items-center justify-content-center p-2">
                            <i class="fas fa-plus text-muted"></i>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>

        {{-- Product Details Section --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-body p-4">
                    {{-- Product Title --}}
                    <div class="mb-4">
                        <h1 class="fw-bold mb-2" style="color: #2c3e50; line-height: 1.3;">{{ $product->name }}</h1>
                        <div class="d-flex align-items-center gap-3">
                            {{-- Rating --}}
                            @if($product->averageRating())
                                <div class="d-flex align-items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($product->averageRating()))
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-2 text-muted">
                                        {{ number_format($product->averageRating(), 1) }} 
                                        ({{ $product->reviews->count() }} {{ $product->reviews->count() == 1 ? 'review' : 'reviews' }})
                                    </span>
                                </div>
                            @else
                                <span class="text-muted">
                                    <i class="far fa-star text-muted me-1"></i>No reviews yet - Be the first!
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="display-5 fw-bold" style="color: #28a745;">₱{{ number_format($product->price, 2) }}</span>
                            <span class="h6 text-muted">per piece</span>
                        </div>
                        <small class="text-muted">💰 Competitive market price</small>
                    </div>

                    {{-- Product Info Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="card border-0" style="background-color: #f8f9fa; border-radius: 15px;">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-weight text-primary mb-2" style="font-size: 1.5rem;"></i>
                                    <h6 class="mb-1">Weight</h6>
                                    <small class="text-muted">Fresh & Premium</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0" style="background-color: #f8f9fa; border-radius: 15px;">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-shipping-fast text-success mb-2" style="font-size: 1.5rem;"></i>
                                    <h6 class="mb-1">Delivery</h6>
                                    <small class="text-muted">Same Day Available</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-info-circle text-primary me-2"></i>Product Description
                        </h6>
                        <div class="p-3 rounded" style="background-color: #f8f9fa; border-left: 4px solid #667eea;">
                            <p class="mb-0" style="line-height: 1.6;">{{ $product->description ?: 'Fresh, high-quality fish perfect for your next meal. Carefully selected and handled with care to ensure maximum freshness and taste.' }}</p>
                        </div>
                    </div>

                    {{-- Seller Information --}}
                    @if($product->user)
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-user-tie text-primary me-2"></i>Seller Information
                        </h6>
                        <div class="card border-0" style="background: linear-gradient(135deg, #667eea20, #764ba220); border-radius: 15px;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ $product->user->name }}</h6>
                                        <small class="text-muted">Trusted Fish Seller</small>
                                        <div class="mt-1">
                                            <a href="{{ route('profile.show', $product->user->id) }}" 
                                               class="btn btn-sm btn-outline-primary" style="border-radius: 20px;">
                                                <i class="fas fa-eye me-1"></i>View Profile
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Action Buttons --}}
                    {{-- Action Buttons --}}
<div class="mt-auto">
    @if($product->quantity > 0)
        @if(auth()->id() === $product->user_id)
            <div class="alert alert-warning d-flex align-items-center" style="border-radius: 15px;">
                <i class="fas fa-ban me-2"></i>
                <span>You cannot purchase your own product.</span>
            </div>
        @elseif($inCart)
            <div class="alert alert-warning d-flex align-items-center" style="border-radius: 15px;">
                <i class="fas fa-shopping-cart me-2"></i>
                <span>This item is already in your cart!</span>
            </div>
        @else
            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-3">
                @csrf
                <div class="row g-3">
                    <div class="col-4">
                        <label for="quantity" class="form-label fw-semibold">Quantity</label>
                        <input type="number" id="quantity" name="quantity" value="1" 
                               min="1" max="{{ $product->quantity }}"
                               class="form-control form-control-lg text-center" 
                               style="border-radius: 15px; border: 2px solid #e9ecef;">
                    </div>
                    <div class="col-8 d-flex align-items-end">
                        <button type="submit" class="btn btn-lg w-100" 
                                style="border-radius: 15px; background: linear-gradient(45deg, #28a745, #20c997); border: none; color: white;">
                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </form>
        @endif
    @else
        <div class="alert alert-danger d-flex align-items-center" style="border-radius: 15px;">
            <i class="fas fa-times-circle me-2"></i>
            <span>Currently out of stock. Check back later!</span>
        </div>
    @endif
</div>


                        {{-- Navigation Buttons --}}
                        <div class="row g-2 mt-3">
                            <div class="col-6">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100" 
                                   style="border-radius: 15px; border-width: 2px;">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Shop
                                </a>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-primary w-100" 
                                        style="border-radius: 15px; border-width: 2px;"
                                        onclick="navigator.share ? navigator.share({title: '{{ $product->name }}', url: window.location.href}) : copyToClipboard(window.location.href)">
                                    <i class="fas fa-share-alt me-2"></i>Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Information Tabs --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <ul class="nav nav-pills nav-fill mb-4" id="productTabs" role="tablist" style="background-color: #f8f9fa; border-radius: 15px; padding: 5px;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="pill" 
                                    data-bs-target="#details" type="button" role="tab" 
                                    style="border-radius: 10px; font-weight: 600;">
                                <i class="fas fa-info-circle me-2"></i>Details
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="pill" 
                                    data-bs-target="#reviews" type="button" role="tab"
                                    style="border-radius: 10px; font-weight: 600;">
                                <i class="fas fa-star me-2"></i>Reviews
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="shipping-tab" data-bs-toggle="pill" 
                                    data-bs-target="#shipping" type="button" role="tab"
                                    style="border-radius: 10px; font-weight: 600;">
                                <i class="fas fa-truck me-2"></i>Shipping
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="productTabsContent">
                        {{-- Details Tab --}}
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-primary mb-3">Product Specifications</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-semibold">Product Name:</td>
                                            <td>{{ $product->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Price:</td>
                                            <td class="text-success fw-bold">₱{{ number_format($product->price, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Stock:</td>
                                            <td>
                                                @if($product->quantity > 0)
                                                    <span class="badge bg-success">{{ $product->quantity }} available</span>
                                                @else
                                                    <span class="badge bg-danger">Out of stock</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Seller:</td>
                                            <td>{{ $product->user->name ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-primary mb-3">Quality Assurance</h6>
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                                        <div>
                                            <strong>Fresh Daily</strong>
                                            <p class="text-muted small mb-0">Caught and processed daily for maximum freshness</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="fas fa-thermometer-half text-info me-3 mt-1"></i>
                                        <div>
                                            <strong>Temperature Controlled</strong>
                                            <p class="text-muted small mb-0">Maintained at optimal temperature throughout</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-certificate text-warning me-3 mt-1"></i>
                                        <div>
                                            <strong>Quality Certified</strong>
                                            <p class="text-muted small mb-0">Meets all health and safety standards</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

{{-- Reviews Tab --}}
<div class="tab-pane fade" id="reviews" role="tabpanel">
    @if($product->reviews && $product->reviews->count() > 0)
        <div class="mb-4">
            <h6 class="fw-bold text-primary mb-3">
                Customer Reviews ({{ $product->reviews->count() }})
            </h6>

            {{-- Display reviews --}}
            @foreach($product->reviews as $review)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <strong>{{ $review->user->name ?? 'Anonymous' }}</strong>
                        <small class="text-muted ms-2">{{ $review->created_at->diffForHumans() }}</small>
                        <p class="mt-2">{{ $review->comment }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-comments text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
            <h5 class="text-muted mb-2">No Reviews Yet</h5>
            <p class="text-muted">Be the first to review this product!</p>
        </div>
    @endif
</div>

                        {{-- Shipping Tab --}}
                        <div class="tab-pane fade" id="shipping" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-primary mb-3">Shipping Information</h6>
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="fas fa-shipping-fast text-success me-3 mt-1"></i>
                                        <div>
                                            <strong>Same Day Delivery</strong>
                                            <p class="text-muted small mb-0">Order before 2 PM for same day delivery</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="fas fa-snowflake text-info me-3 mt-1"></i>
                                        <div>
                                            <strong>Cold Chain Delivery</strong>
                                            <p class="text-muted small mb-0">Temperature controlled transport</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-shield-alt text-primary me-3 mt-1"></i>
                                        <div>
                                            <strong>Safe Packaging</strong>
                                            <p class="text-muted small mb-0">Specially designed packaging for freshness</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-primary mb-3">Delivery Areas</h6>
                                    <p class="text-muted">We currently deliver to the following areas:</p>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                            Metro Manila - Free delivery
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-map-marker-alt text-warning me-2"></i>
                                            Cavite Province - ₱50 delivery fee
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-map-marker-alt text-info me-2"></i>
                                            Laguna Province - ₱50 delivery fee
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
    
    .nav-pills .nav-link {
        color: #6c757d;
        background: transparent;
        border: none;
    }
    
    .nav-pills .nav-link.active {
        background: linear-gradient(45deg, #667eea, #764ba2) !important;
        color: white !important;
    }
    
    .nav-pills .nav-link:hover {
        background: rgba(102, 126, 234, 0.1);
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .table td {
        padding: 0.5rem 0;
        border: none;
    }
    
    @media (max-width: 768px) {
        .display-5 {
            font-size: 2rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
    }
</style>

{{-- JavaScript for sharing functionality --}}
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Product link copied to clipboard!');
        });
    }
</script>

{{-- Add Bootstrap JS if not already included --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection