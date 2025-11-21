@extends('layouts.app')
@section('title', 'Profile')
@section('content')
<div class="mt-5">
    {{-- Profile Header Section --}}
    <div class="card border-0 shadow-lg mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                     style="width: 120px; height: 120px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                    <i class="fas fa-user text-white" style="font-size: 3rem;"></i>
                </div>
            </div>
            <h1 class="display-5 fw-bold text-white mb-3">{{ $user->name }}</h1>
            <p class="lead text-white-50 mb-3">
                <i class="fas fa-calendar-alt me-2"></i>
                Member since {{ $user->created_at->format('F Y') }}
            </p>
            {{-- Chat Here Button --}}
<div class="text-center mt-4">
    <a href="{{ route('chat.index', ['user' => $user->id]) }}" 
       class="btn btn-primary btn-lg" 
       style="border-radius: 25px; background: linear-gradient(45deg, #667eea, #764ba2);">
        <i class="fas fa-comments me-2"></i> Chat Here
    </a>
</div>
            {{-- Overall Average Rating --}}
            @php
                $allReviews = $user->products->flatMap->reviews; 
                $avgRating = $allReviews->avg('rating');
            @endphp

            @if($allReviews->count() > 0)
                <div class="d-inline-flex align-items-center px-4 py-2 rounded-pill" 
                     style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                    <div class="me-3">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avgRating))
                                <i class="fas fa-star text-warning"></i>
                            @else
                                <i class="far fa-star text-white-50"></i>
                            @endif
                        @endfor
                    </div>
                    <div class="text-white">
                        <span class="h5 mb-0 me-2">{{ number_format($avgRating, 1) }}</span>
                        <small class="text-white-50">({{ $allReviews->count() }} {{ $allReviews->count() == 1 ? 'review' : 'reviews' }})</small>
                    </div>
                </div>
            @else
                <div class="d-inline-flex align-items-center px-4 py-2 rounded-pill" 
                     style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                    <i class="far fa-star text-white-50 me-2"></i>
                    <span class="text-white-50">No reviews yet</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Products Section Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #2c3e50;">
                <i class="fas fa-fish me-2" style="color: #667eea;"></i>
                Products by {{ $user->name }}
            </h2>
            <p class="text-muted mb-0">
                @if($user->products->count() > 0)
                    {{ $user->products->count() }} {{ $user->products->count() == 1 ? 'product' : 'products' }} available
                @endif
            </p>
        </div>
    </div>

    @if($user->products->isEmpty())
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-fish text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
            <h3 class="text-muted mb-3">No Products Posted Yet</h3>
            <p class="text-muted">This seller hasn't listed any fish products yet. Check back later!</p>
        </div>
    @else
        <div class="row">
            @foreach($user->products as $product)
            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden" 
                     style="border-radius: 20px; transition: all 0.3s ease; cursor: pointer;"
                     onmouseover="this.style.transform='translateY(-5px)'; this.classList.add('shadow-lg')"
                     onmouseout="this.style.transform='translateY(0)'; this.classList.remove('shadow-lg')">
                    
                    {{-- Product Image with Overlay --}}
                    <div class="position-relative overflow-hidden" style="height: 220px; border-radius: 20px 20px 0 0;">
                        @if($product->images && $product->images->count() > 0)
                            <img src="{{ asset('storage/' . $product->images->first()->image) }}"
                                 class="card-img-top w-100 h-100"
                                 style="object-fit: cover; transition: transform 0.3s ease;"
                                 alt="{{ $product->name }}"
                                 onmouseover="this.style.transform='scale(1.05)'"
                                 onmouseout="this.style.transform='scale(1)'">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center"
                                 style="background: linear-gradient(45deg, #f8f9fa, #e9ecef);">
                                <div class="text-center">
                                    <i class="fas fa-fish text-muted mb-2" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted mb-0">No Image</p>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Stock Status Badge --}}
                        @if($product->quantity > 0)
                            <span class="badge position-absolute top-0 end-0 m-3" 
                                  style="background: rgba(40, 167, 69, 0.9); border-radius: 20px; padding: 8px 12px;">
                                <i class="fas fa-check-circle me-1"></i>{{ $product->quantity }} in stock
                            </span>
                        @else
                            <span class="badge position-absolute top-0 end-0 m-3" 
                                  style="background: rgba(220, 53, 69, 0.9); border-radius: 20px; padding: 8px 12px;">
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
                        
                        {{-- Price --}}
                        <div class="mb-3">
                            <span class="h4 fw-bold" style="color: #28a745;">
                                ₱{{ number_format($product->price, 2) }}
                            </span>
                            <small class="text-muted">/ piece</small>
                        </div>

                        {{-- Product Rating --}}
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

                        {{-- View Product Button --}}
                        <div class="mt-auto">
                            <a href="{{ route('products.show', $product) }}" 
                               class="btn btn-outline-primary w-100 mb-3" 
                               style="border-radius: 15px; border-width: 2px;">
                                <i class="fas fa-eye me-2"></i>View Product Details
                            </a>
                        </div>
                    </div>

                    {{-- Reviews Section --}}
                    @if($product->reviews->count() > 0)
                    <div class="card-footer border-0" style="background: #f8f9fa; border-radius: 0 0 20px 20px;">
                        <h6 class="fw-bold mb-3" style="color: #2c3e50;">
                            <i class="fas fa-comments me-2" style="color: #667eea;"></i>
                            Recent Reviews
                        </h6>
                        
                        <div class="reviews-container" style="max-height: 200px; overflow-y: auto;">
                            @foreach($product->reviews->take(3) as $review)
                                <div class="review-item p-3 mb-2 rounded-3" 
                                     style="background: white; border: 1px solid #e9ecef;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 32px; height: 32px; background: linear-gradient(45deg, #667eea, #764ba2);">
                                                <i class="fas fa-user text-white" style="font-size: 0.7rem;"></i>
                                            </div>
                                            <div>
                                                <strong class="small" style="color: #2c3e50;">{{ $review->user->name }}</strong>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fas fa-star text-warning" style="font-size: 0.8rem;"></i>
                                                @else
                                                    <i class="far fa-star text-warning" style="font-size: 0.8rem;"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="mb-0 small text-muted" style="line-height: 1.4;">
                                        "{{ Str::limit($review->comment, 80) }}"
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($product->reviews->count() > 3)
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-ellipsis-h me-1"></i>
                                    {{ $product->reviews->count() - 3 }} more reviews
                                </small>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Custom CSS --}}
<style>
    .card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    
    .btn-outline-primary {
        border-color: #667eea;
        color: #667eea;
    }
    
    .btn-outline-primary:hover {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border-color: #667eea;
        transform: translateY(-1px);
    }
    
    .reviews-container::-webkit-scrollbar {
        width: 4px;
    }
    
    .reviews-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .reviews-container::-webkit-scrollbar-thumb {
        background: #667eea;
        border-radius: 10px;
    }
    
    .reviews-container::-webkit-scrollbar-thumb:hover {
        background: #5a6fd8;
    }
    
    .review-item {
        transition: all 0.2s ease;
    }
    
    .review-item:hover {
        transform: translateX(5px);
        border-color: #667eea !important;
    }
    
    @media (max-width: 768px) {
        .display-5 {
            font-size: 1.8rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .reviews-container {
            max-height: 150px;
        }
    }
    
    /* Glassmorphism effect for profile header elements */
    .rounded-circle {
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
body, 
h1, h2, h3, h4, h5, h6, 
p, span, a, div, input, select, button, label {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
}
</style>

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection