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



@section('content')
<div class="mt-5">
    {{-- Enhanced Search & Filter Section with Collapsible Design --}}
    <div class="card border-0 shadow-sm mb-5" style="border-radius: 24px; overflow: hidden;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('home') }}" id="filterForm">
                {{-- Search Bar - Always Visible --}}
                <div class="mb-3">
                    <div class="position-relative">
                        <i class="position-absolute text-muted" style="left: 20px; top: 50%; transform: translateY(-50%); z-index: 10;"></i>
                        <input type="text" 
                               name="search" 
                               class="form-control form-control-lg ps-5 pe-5 shadow-sm" 
                               placeholder="Search for fish species, type, or category..."
                               value="{{ request('search') }}"
                               style="border-radius: 16px; border: 2px solid #e9ecef; height: 56px; font-size: 1rem;">
                        @if(request('search'))
                            <button type="button" 
                                    class="btn btn-link position-absolute text-muted" 
                                    style="right: 10px; top: 50%; transform: translateY(-50%); padding: 0; width: 30px; height: 30px;"
                                    onclick="document.querySelector('input[name=search]').value=''; document.getElementById('filterForm').submit();">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Toggle Filter Button --}}
                <div class="d-flex gap-2 mb-3">
                    <button type="button" 
                            class="btn btn-outline-primary flex-grow-1" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#advancedFilters" 
                            aria-expanded="false" 
                            aria-controls="advancedFilters"
                            id="filterToggleBtn"
                            style="border-radius: 12px; border: 2px solid #0bb364; color: #0bb364; font-weight: 600; height: 48px;">
                        <i class="fas fa-sliders-h me-2"></i>
                        <span id="filterBtnText">Show Advanced Filters</span>
                        <i class="fas fa-chevron-down ms-2" id="filterChevron"></i>
                    </button>
                    <button type="submit" 
                            class="btn btn-primary px-4" 
                            style="border-radius: 12px; background: linear-gradient(135deg, #0bb364 0%, #088a50 100%); border: none; font-weight: 600; height: 48px; min-width: 120px;">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>

                {{-- Collapsible Advanced Filters --}}
                <div class="collapse" id="advancedFilters">
                    <div class="card border-0 bg-light" style="border-radius: 16px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="mb-0 fw-bold" style="color: #0bb364;">
                                    <i class="fas fa-filter me-2"></i>Advanced Filters
                                </h6>
                               <button type="button" 
                                    class="btn btn-sm btn-light" 
                                    id="closeAdvancedFilters"
                                    style="border-radius: 8px;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            {{-- Filters Grid --}}
                            <div class="row g-3 mb-3">
                                {{-- Category Filter --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label small fw-semibold mb-2" style="color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.75rem;">
                                        <i class="fas fa-layer-group me-1" style="color: #0bb364;"></i>Category
                                    </label>
                                    <select name="product_category_id" 
                                            class="form-select shadow-sm" 
                                            style="border-radius: 12px; border: 2px solid #e9ecef; height: 48px; font-size: 0.95rem;">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ request('product_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Type Filter --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label small fw-semibold mb-2" style="color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.75rem;">
                                        <i class="fas fa-fish me-1" style="color: #0bb364;"></i>Fish Type
                                    </label>
                                    <select name="product_type_id" 
                                            class="form-select shadow-sm" 
                                            style="border-radius: 12px; border: 2px solid #e9ecef; height: 48px; font-size: 0.95rem;">
                                        <option value="">All Types</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" 
                                                    {{ request('product_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Unit Type Filter --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label small fw-semibold mb-2" style="color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.75rem;">
                                        <i class="fas fa-weight-hanging me-1" style="color: #0bb364;"></i>Unit Type
                                    </label>
                                    <select name="unit_type" 
                                            class="form-select shadow-sm" 
                                            style="border-radius: 12px; border: 2px solid #e9ecef; height: 48px; font-size: 0.95rem;">
                                        <option value="">All Units</option>
                                        <option value="piece" {{ request('unit_type') == 'piece' ? 'selected' : '' }}>Pieces</option>
                                        <option value="kilo" {{ request('unit_type') == 'kilo' ? 'selected' : '' }}>Kilogram</option>
                                        <option value="pack" {{ request('unit_type') == 'pack' ? 'selected' : '' }}>Pack</option>
                                        <option value="box" {{ request('unit_type') == 'box' ? 'selected' : '' }}>Box</option>
                                    </select>
                                </div>

                                {{-- Sort Dropdown --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label small fw-semibold mb-2" style="color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.75rem;">
                                        <i class="fas fa-sort-amount-down me-1" style="color: #0bb364;"></i>Sort By
                                    </label>
                                    <select name="sort" 
                                            class="form-select shadow-sm" 
                                            style="border-radius: 12px; border: 2px solid #e9ecef; height: 48px; font-size: 0.95rem;">
                                        <option value="">Default Order</option>
                                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Action Buttons Inside Collapsible --}}
                            <div class="d-flex gap-2">
                                <a href="{{ route('home') }}" 
                                   class="btn btn-light px-4" 
                                   style="border-radius: 12px; border: 2px solid #e9ecef; font-weight: 600; color: #6c757d; height: 48px; line-height: 36px;">
                                    <i class="fas fa-redo-alt me-2"></i>Clear All Filters
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Active Filters Chips - Always Visible When Filters Applied --}}
                @if(request()->hasAny(['search', 'product_category_id', 'product_type_id', 'unit_type', 'sort']))
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="small fw-bold" style="color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.7rem;">
                            <i class="fas fa-tag me-1"></i>Active Filters ({{ collect([request('search'), request('product_category_id'), request('product_type_id'), request('unit_type'), request('sort')])->filter()->count() }})
                        </span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if(request('search'))
                            <span class="badge d-inline-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 8px 14px; font-size: 0.85rem; font-weight: 500;">
                                <i class="fas fa-search me-2" style="font-size: 0.75rem;"></i>
                                "{{ Str::limit(request('search'), 20) }}"
                                <a href="{{ route('home', array_merge(request()->except('search'))) }}" 
                                   class="text-white ms-2 text-decoration-none d-inline-flex align-items-center justify-content-center" 
                                   style="width: 18px; height: 18px; background: rgba(255,255,255,0.2); border-radius: 50%; font-size: 0.7rem;">×</a>
                            </span>
                        @endif

                        @if(request('product_category_id'))
                            @php
                                $category = $categories->find(request('product_category_id'));
                            @endphp
                            @if($category)
                            <span class="badge d-inline-flex align-items-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 20px; padding: 8px 14px; font-size: 0.85rem; font-weight: 500;">
                                <i class="fas fa-layer-group me-2" style="font-size: 0.75rem;"></i>
                                {{ $category->name }}
                                <a href="{{ route('home', array_merge(request()->except('product_category_id'))) }}" 
                                   class="text-white ms-2 text-decoration-none d-inline-flex align-items-center justify-content-center" 
                                   style="width: 18px; height: 18px; background: rgba(255,255,255,0.2); border-radius: 50%; font-size: 0.7rem;">×</a>
                            </span>
                            @endif
                        @endif

                        @if(request('product_type_id'))
                            @php
                                $type = $types->find(request('product_type_id'));
                            @endphp
                            @if($type)
                            <span class="badge d-inline-flex align-items-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 20px; padding: 8px 14px; font-size: 0.85rem; font-weight: 500;">
                                <i class="fas fa-fish me-2" style="font-size: 0.75rem;"></i>
                                {{ $type->name }}
                                <a href="{{ route('home', array_merge(request()->except('product_type_id'))) }}" 
                                   class="text-white ms-2 text-decoration-none d-inline-flex align-items-center justify-content-center" 
                                   style="width: 18px; height: 18px; background: rgba(255,255,255,0.2); border-radius: 50%; font-size: 0.7rem;">×</a>
                            </span>
                            @endif
                        @endif

                        @if(request('unit_type'))
                            <span class="badge d-inline-flex align-items-center" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 20px; padding: 8px 14px; font-size: 0.85rem; font-weight: 500;">
                                <i class="fas fa-weight-hanging me-2" style="font-size: 0.75rem;"></i>
                                {{ ucfirst(request('unit_type')) }}
                                <a href="{{ route('home', array_merge(request()->except('unit_type'))) }}" 
                                   class="text-white ms-2 text-decoration-none d-inline-flex align-items-center justify-content-center" 
                                   style="width: 18px; height: 18px; background: rgba(255,255,255,0.2); border-radius: 50%; font-size: 0.7rem;">×</a>
                            </span>
                        @endif

                        @if(request('sort'))
                            <span class="badge d-inline-flex align-items-center" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #495057 !important; border-radius: 20px; padding: 8px 14px; font-size: 0.85rem; font-weight: 500;">
                                <i class="fas fa-sort-amount-down me-2" style="font-size: 0.75rem;"></i>
                                @switch(request('sort'))
                                    @case('price_asc') Price: Low to High @break
                                    @case('price_desc') Price: High to Low @break
                                    @case('name_asc') Name: A to Z @break
                                    @case('name_desc') Name: Z to A @break
                                @endswitch
                                <a href="{{ route('home', array_merge(request()->except('sort'))) }}" 
                                   class="ms-2 text-decoration-none d-inline-flex align-items-center justify-content-center" 
                                   style="width: 18px; height: 18px; background: rgba(0,0,0,0.1); border-radius: 50%; font-size: 0.7rem; color: #495057;">×</a>
                            </span>
                        @endif
                    </div>
                </div>
                @endif
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
                        @if($product->productCategory)
                            <span class="badge" style="background: linear-gradient(45deg, #0bb364, #0bb364); border-radius: 15px; color: #fff;">
                                {{ $product->productCategory->name }}
                            </span>
                        @else
                            <span class="badge" style="background: linear-gradient(45deg, #0bb364, #0bb364); border-radius: 15px; color: #fff;">
                                Fresh
                            </span>
                        @endif
                    </div>
                    
                    <p class="card-text text-muted small mb-3" style="line-height: 1.5;">
                        {{ Str::limit($product->description, 80) }}
                    </p>

                    {{-- Price with Unit Information --}}
                    <div class="mb-3">
                        <span class="h4 fw-bold" style="color: #28a745;">
                            ₱{{ number_format($product->price, 2) }}
                        </span>
                        @if($product->unit_type && $product->unit_value)
                            <small class="text-muted">
                                / {{ $product->unit_value }} 
                                {{ $product->unit_type }}{{ $product->unit_value > 1 ? 's' : '' }}
                            </small>
                        @else
                            <small class="text-muted">/ piece</small>
                        @endif
                    </div>

                    {{-- Unit Information Badge --}}
                    @if($product->unit_type && $product->unit_value)
                        <div class="mb-3">
                            <span class="badge bg-info" style="border-radius: 12px; padding: 6px 12px; font-size: 0.75rem;">
                                <i class="fas fa-box me-1"></i>
                                @switch($product->unit_type)
                                    @case('pack')
                                        {{ $product->unit_value }} piece{{ $product->unit_value > 1 ? 's' : '' }} per pack
                                        @break
                                    @case('kilo')
                                        {{ $product->unit_value }} kg
                                        @break
                                    @case('box')
                                        {{ $product->unit_value }} kg per box
                                        @break
                                    @case('piece')
                                        Sold per piece
                                        @break
                                @endswitch
                            </span>
                        </div>
                    @endif

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
                                 style="background-color: #f8f9fa; border-left: 3px solid #0bb364;">
                                <i class="fas fa-user-circle text-primary me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Seller</small>
                                    <a href="{{ route('profile.show', $product->user->id) }}" 
                                       class="fw-semibold text-decoration-none" style="color: #0bb364;">
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
@push('styles')
{{-- Custom CSS --}}
<style>
/* Hide next/prev buttons by default */
.custom-carousel .carousel-control-prev,
.custom-carousel .carousel-control-next {
    opacity: 0;
    transition: opacity 0.4s ease, transform 0.4s ease;
    transform: translateX(0);
}

.custom-carousel:hover .carousel-control-prev {
    opacity: 0.5;
    transform: translateX(10px);
}

.custom-carousel:hover .carousel-control-next {
    opacity: 0.5;
    transform: translateX(-10px);
}

.custom-carousel .carousel-control-prev:hover,
.custom-carousel .carousel-control-next:hover {
    opacity: 0.9;
    transform: translateX(0);
}

.card:hover {
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

/* Enhanced Filter Form Styles */
.form-select:focus,
.form-control:focus {
    border-color: #0bb364 !important;
    box-shadow: 0 0 0 3px rgba(11, 179, 100, 0.1) !important;
    outline: none;
}

.form-select,
.form-control {
    transition: all 0.3s ease;
}

.form-select:hover,
.form-control:hover {
    border-color: #0bb364;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(11, 179, 100, 0.3) !important;
    background: linear-gradient(135deg, #0bb364 0%, #088a50 100%) !important;
}

.btn-light:hover {
    background-color: #f8f9fa;
    border-color: #0bb364;
    color: #0bb364;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.btn-outline-primary {
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: linear-gradient(135deg, #0bb364 0%, #088a50 100%);
    border-color: #0bb364;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(11, 179, 100, 0.3);
}

.badge a:hover {
    background: rgba(255,255,255,0.4) !important;
}

.pagination .page-link {
    border-radius: 10px;
    margin: 0 2px;
    border: 2px solid #e9ecef;
    color: #0bb364;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(45deg, #0bb364, #0bb364);
    border-color: #0bb364;
}

.alert {
    border: none;
}

/* Collapsible animation */
.collapse {
    transition: height 0.35s ease;
}

.collapsing {
    transition: height 0.35s ease;
}

#advancedFilters .card {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Filter toggle button animation */
#filterChevron {
    transition: transform 0.3s ease;
}

#filterToggleBtn[aria-expanded="true"] #filterChevron {
    transform: rotate(180deg);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .display-4 {
        font-size: 2rem;
    }
    
    .card-body {
        padding: 1rem;
    }

    .form-label {
        font-size: 0.7rem !important;
    }
    
    .form-select,
    .form-control {
        height: 44px !important;
        font-size: 0.9rem !important;
    }
    
    .btn {
        font-size: 0.9rem;
    }
    
    #filterBtnText {
        font-size: 0.9rem;
    }
}

/* Font Family */
body, 
h1, h2, h3, h4, h5, h6, 
p, span, a, div, input, select, button, label {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- JavaScript for Filter Toggle --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const advancedFilters = document.getElementById('advancedFilters');
    const filterBtnText = document.getElementById('filterBtnText');
    const filterChevron = document.getElementById('filterChevron');
    const closeBtn = document.getElementById('closeAdvancedFilters');

    // Initialize Collapse instance
    const collapseInstance = new bootstrap.Collapse(advancedFilters, {toggle: false});

    // Toggle chevron & button text when collapse shows/hides
    advancedFilters.addEventListener('shown.bs.collapse', () => {
        filterBtnText.textContent = 'Hide Advanced Filters';
        filterChevron.classList.replace('fa-chevron-down', 'fa-chevron-up');
    });

    advancedFilters.addEventListener('hidden.bs.collapse', () => {
        filterBtnText.textContent = 'Show Advanced Filters';
        filterChevron.classList.replace('fa-chevron-up', 'fa-chevron-down');
    });

    // Close button inside collapse
    closeBtn.addEventListener('click', () => {
        collapseInstance.hide();
    });
});
</script>
@endpush
@endsection