@extends('layouts.app')
@section('title', 'My Profile')
@section('content')
<div class="mt-5">
    {{-- Profile Header Section --}}
    <div class="card border-0 shadow-lg mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                     style="width: 120px; height: 120px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                    @if(auth()->user()->role === 'buyer')
                        <i class="fas fa-shopping-cart text-white" style="font-size: 3rem;"></i>
                    @elseif(auth()->user()->role === 'reseller')
                        <i class="fas fa-store text-white" style="font-size: 3rem;"></i>
                    @else
                        <i class="fas fa-fish text-white" style="font-size: 3rem;"></i>
                    @endif
                </div>
            </div>
            <h1 class="display-5 fw-bold text-white mb-3">{{ auth()->user()->name }}</h1>
            <p class="lead text-white-50 mb-3">
                <i class="fas fa-user-tag me-2"></i>
                {{ ucfirst(auth()->user()->role) }}
                <span class="mx-2">•</span>
                <i class="fas fa-calendar-alt me-2"></i>
                Member since {{ auth()->user()->created_at->format('F Y') }}
            </p>

            {{-- Role-specific header stats --}}
            @if(auth()->user()->role === 'buyer')
                <div class="d-inline-flex align-items-center px-4 py-2 rounded-pill" 
                     style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                    <i class="fas fa-shopping-bag text-white me-2"></i>
                    <span class="text-white">
                        <span class="h5 mb-0 me-2">{{ optional(auth()->user()->orders)->count() ?? 0 }}</span>
                        <small class="text-white-50">Total Orders</small>
                    </span>
                </div>
            @else
                {{-- Overall Average Rating for Resellers/Suppliers --}}
@php
    $allReviews = auth()->user()->products ? auth()->user()->products->flatMap->reviews : collect();
    $avgRating = $allReviews->count() > 0 ? $allReviews->avg('rating') : 0;
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
            @endif
        </div>
    </div>

    {{-- Dashboard Cards for Resellers/Suppliers --}}
    @if(auth()->user()->role !== 'buyer')
        <div class="row mb-5">
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                    <div class="card-body text-center">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; background: linear-gradient(45deg, #667eea, #764ba2);">
                            <i class="fas fa-fish text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: #2c3e50;">{{ auth()->user()->products->count() }}</h3>
                        <p class="text-muted mb-0">Total Products</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                    <div class="card-body text-center">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; background: linear-gradient(45deg, #28a745, #20c997);">
                            <i class="fas fa-check-circle text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: #2c3e50;">{{ auth()->user()->products->where('status', 'available')->count() }}</h3>
                        <p class="text-muted mb-0">Available</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                    <div class="card-body text-center">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; background: linear-gradient(45deg, #ffc107, #fd7e14);">
                            <i class="fas fa-star text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: #2c3e50;">
                            {{ $allReviews->count() > 0 ? number_format($avgRating, 1) : '0.0' }}
                        </h3>
                        <p class="text-muted mb-0">Avg Rating</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                    <div class="card-body text-center">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; background: linear-gradient(45deg, #dc3545, #e91e63);">
                            <i class="fas fa-comments text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: #2c3e50;">{{ $allReviews->count() }}</h3>
                        <p class="text-muted mb-0">Total Reviews</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Content Tabs --}}
    <div class="card border-0 shadow-lg" style="border-radius: 20px;">
        <div class="card-header border-0" style="background: transparent; padding: 2rem 2rem 0 2rem;">
            <ul class="nav nav-tabs border-0" id="profileTabs" role="tablist">
                @if(auth()->user()->role === 'buyer')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                            <i class="fas fa-shopping-bag me-2"></i>My Orders
                        </button>
                    </li>
                @else
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">
                            <i class="fas fa-fish me-2"></i>My Products
                        </button>
                    </li>
                @endif
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab">
                        <i class="fas fa-newspaper me-2"></i>My Posts
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="profileTabContent">
                {{-- Orders Tab for Buyers --}}
                @if(auth()->user()->role === 'buyer')
                    <div class="tab-pane fade show active" id="orders" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h4 class="fw-bold mb-0" style="color: #2c3e50;">
                                <i class="fas fa-shopping-bag me-2" style="color: #667eea;"></i>
                                Order History
                            </h4>
                           <span class="badge bg-primary">{{ auth()->user()->orders ? auth()->user()->orders->count() : 0 }} orders</span>
                        </div>

                       @if(optional(auth()->user()->orders)->isEmpty() ?? true)
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-shopping-bag text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                                </div>
                                <h5 class="text-muted mb-3">No Orders Yet</h5>
                                <p class="text-muted">You haven't placed any orders yet. Start shopping to see your orders here!</p>
                                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill">
                                    <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                                </a>
                            </div>
                        @else
                            @php
                                $ordersPerPage = 8;
                                $currentOrdersPage = request()->get('orders_page', 1);
                                $allOrders = auth()->user()->orders->sortByDesc('created_at');
                                $totalOrdersPages = ceil($allOrders->count() / $ordersPerPage);
                                $ordersOffset = ($currentOrdersPage - 1) * $ordersPerPage;
                                $paginatedOrders = $allOrders->slice($ordersOffset, $ordersPerPage);
                            @endphp

                            <div class="row">
                                @foreach($paginatedOrders as $order)
                                    <div class="col-md-6 mb-4">
                                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div>
                                                        <h6 class="fw-bold mb-1" style="color: #2c3e50;">
                                                            Order #{{ $order->id }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            {{ $order->created_at->format('M d, Y') }}
                                                        </small>
                                                    </div>
                                                    <span class="badge rounded-pill
                                                        @if($order->status === 'completed') bg-success
                                                        @elseif($order->status === 'processing') bg-warning
                                                        @elseif($order->status === 'cancelled') bg-danger
                                                        @else bg-secondary
                                                        @endif">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <h5 class="fw-bold mb-0" style="color: #28a745;">
                                                        ₱{{ number_format($order->total_price, 2) }}
                                                    </h5>
                                                    <small class="text-muted">
                                                        {{ $order->products->count() }} {{ $order->products->count() == 1 ? 'item' : 'items' }}
                                                    </small>
                                                </div>

                                                {{-- Order Items Preview --}}
                                                <div class="order-items mb-3" style="max-height: 120px; overflow-y: auto;">
                                                    @foreach($order->products->take(3) as $product)
                                                        <div class="d-flex align-items-center mb-2 p-2 rounded" style="background: #f8f9fa;">
                                                            <div class="rounded me-3" style="width: 40px; height: 40px; overflow: hidden;">
                                                                @if($product->images && $product->images->count() > 0)
                                                                    <img src="{{ asset('storage/' . $product->images->first()->image) }}" 
                                                                         class="w-100 h-100" style="object-fit: cover;" 
                                                                         alt="{{ $product->name }}">
                                                                @else
                                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                                        <i class="fas fa-fish text-muted" style="font-size: 0.8rem;"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <small class="fw-bold d-block">{{ Str::limit($product->name, 25) }}</small>
                                                                <small class="text-muted">Qty: {{ $product->pivot->quantity }}</small>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    @if($order->products->count() > 3)
                                                        <small class="text-muted text-center d-block mt-2">
                                                            + {{ $order->products->count() - 3 }} more items
                                                        </small>
                                                    @endif
                                                </div>

                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('orders.index', $order) }}" 
                                                       class="btn btn-outline-primary btn-sm flex-fill rounded-pill">
                                                        <i class="fas fa-eye me-1"></i>View Details
                                                    </a>
                                                    @if($order->status === 'completed' && !$order->reviews()->where('user_id', auth()->id())->exists())
                                                        <button class="btn btn-outline-warning btn-sm rounded-pill" 
                                                                data-bs-toggle="modal" data-bs-target="#reviewModal{{ $order->id }}">
                                                            <i class="fas fa-star me-1"></i>Review
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Orders Pagination --}}
                            @if($totalOrdersPages > 1)
                                <div class="d-flex justify-content-center align-items-center mt-4 gap-3">
                                    <a href="?orders_page={{ max(1, $currentOrdersPage - 1) }}#orders" 
                                       class="btn btn-outline-primary rounded-pill {{ $currentOrdersPage <= 1 ? 'disabled' : '' }}">
                                        <i class="fas fa-chevron-left me-2"></i>Previous
                                    </a>
                                    <span class="text-muted">
                                        Page <strong>{{ $currentOrdersPage }}</strong> of <strong>{{ $totalOrdersPages }}</strong>
                                    </span>
                                    <a href="?orders_page={{ min($totalOrdersPages, $currentOrdersPage + 1) }}#orders" 
                                       class="btn btn-outline-primary rounded-pill {{ $currentOrdersPage >= $totalOrdersPages ? 'disabled' : '' }}">
                                        Next<i class="fas fa-chevron-right ms-2"></i>
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>

                {{-- Products Tab for Resellers/Suppliers --}}
                @else
                    <div class="tab-pane fade show active" id="products" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h4 class="fw-bold mb-1" style="color: #2c3e50;">
                                    <i class="fas fa-fish me-2" style="color: #667eea;"></i>
                                    My Products
                                </h4>
                                <p class="text-muted mb-0">
                                    @if(auth()->user()->products->count() > 0)
                                        {{ auth()->user()->products->count() }} {{ auth()->user()->products->count() == 1 ? 'product' : 'products' }} listed
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill">
                                <i class="fas fa-plus me-2"></i>Add Product
                            </a>
                        </div>

                        @if(auth()->user()->products->isEmpty())
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-fish text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                                </div>
                                <h5 class="text-muted mb-3">No Products Listed Yet</h5>
                                <p class="text-muted">Start selling by adding your first product!</p>
                                <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill">
                                    <i class="fas fa-plus me-2"></i>Add Your First Product
                                </a>
                            </div>
                        @else
                            @php
                                $productsPerPage = 9;
                                $currentProductsPage = request()->get('products_page', 1);
                                $allProducts = auth()->user()->products;
                                $totalProductsPages = ceil($allProducts->count() / $productsPerPage);
                                $productsOffset = ($currentProductsPage - 1) * $productsPerPage;
                                $paginatedProducts = $allProducts->slice($productsOffset, $productsPerPage);
                            @endphp

                            <div class="row">
                                @foreach($paginatedProducts as $product)
                                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                        <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden" 
                                             style="border-radius: 15px; transition: all 0.3s ease;">
                                            
                                            {{-- Product Image --}}
                                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}"
                                                         class="card-img-top w-100 h-100"
                                                         style="object-fit: cover;"
                                                         alt="{{ $product->name }}">
                                                @else
                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                        <i class="fas fa-fish text-muted" style="font-size: 2.5rem;"></i>
                                                    </div>
                                                @endif
                                                
                                                {{-- Status Badge --}}
                                                <span class="badge position-absolute top-0 end-0 m-3 rounded-pill
                                                    @if($product->status === 'available') bg-success
                                                    @else bg-secondary
                                                    @endif">
                                                    {{ ucfirst($product->status) }}
                                                </span>
                                            </div>

                                            <div class="card-body">
                                                <h6 class="card-title fw-bold mb-2">{{ $product->name }}</h6>
                                                <p class="text-muted small mb-2">{{ Str::limit($product->description, 60) }}</p>
                                                
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="h6 fw-bold text-success mb-0">
                                                        ₱{{ number_format($product->price, 2) }}
                                                    </span>
                                                    <small class="text-muted">Stock: {{ $product->quantity }}</small>
                                                </div>

                                                {{-- Rating --}}
                                                @if($product->reviews->count() > 0)
                                                    <div class="d-flex align-items-center mb-3">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star text-warning" style="font-size: 0.8rem;"></i>
                                                        @endfor
                                                        <small class="text-muted ms-2">
                                                            {{ number_format($product->averageRating(), 1) }} 
                                                            ({{ $product->reviews->count() }})
                                                        </small>
                                                    </div>
                                                @else
                                                    <div class="mb-3">
                                                        <small class="text-muted">No reviews yet</small>
                                                    </div>
                                                @endif

                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('products.show', $product) }}" 
                                                       class="btn btn-outline-primary btn-sm flex-fill">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                    <a href="{{ route('products.edit', $product) }}" 
                                                       class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Products Pagination --}}
                            @if($totalProductsPages > 1)
                                <div class="d-flex justify-content-center align-items-center mt-4 gap-3">
                                    <a href="?products_page={{ max(1, $currentProductsPage - 1) }}#products" 
                                       class="btn btn-outline-primary rounded-pill {{ $currentProductsPage <= 1 ? 'disabled' : '' }}">
                                        <i class="fas fa-chevron-left me-2"></i>Previous
                                    </a>
                                    <span class="text-muted">
                                        Page <strong>{{ $currentProductsPage }}</strong> of <strong>{{ $totalProductsPages }}</strong>
                                    </span>
                                    <a href="?products_page={{ min($totalProductsPages, $currentProductsPage + 1) }}#products" 
                                       class="btn btn-outline-primary rounded-pill {{ $currentProductsPage >= $totalProductsPages ? 'disabled' : '' }}">
                                        Next<i class="fas fa-chevron-right ms-2"></i>
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif

                {{-- Posts Tab (for all roles) --}}
                <div class="tab-pane fade" id="posts" role="tabpanel">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="fw-bold mb-1" style="color: #2c3e50;">
                                <i class="fas fa-newspaper me-2" style="color: #667eea;"></i>
                                My Posts
                            </h4>
                            <p class="text-muted mb-0">
                                {{ auth()->user()->posts->count() }} {{ auth()->user()->posts->count() == 1 ? 'post' : 'posts' }} shared
                            </p>
                        </div>
                        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#newPostModal">
                            <i class="fas fa-plus me-2"></i>New Post
                        </button>
                    </div>

                    @if(auth()->user()->posts->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-newspaper text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                            </div>
                            <h5 class="text-muted mb-3">No Posts Yet</h5>
                            <p class="text-muted">Share your thoughts, experiences, or updates with the community!</p>
                            <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#newPostModal">
                                <i class="fas fa-plus me-2"></i>Create Your First Post
                            </button>
                        </div>
                    @else
                        @php
                            $postsPerPage = 5;
                            $currentPostsPage = request()->get('posts_page', 1);
                            $allPosts = auth()->user()->posts->sortByDesc('created_at');
                            $totalPostsPages = ceil($allPosts->count() / $postsPerPage);
                            $postsOffset = ($currentPostsPage - 1) * $postsPerPage;
                            $paginatedPosts = $allPosts->slice($postsOffset, $postsPerPage);
                        @endphp

                        <div class="posts-container">
                            @foreach($paginatedPosts as $post)
                                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 45px; height: 45px; background: linear-gradient(45deg, #667eea, #764ba2);">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold mb-1">{{ auth()->user()->name }}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $post->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        @if($post->title)
                                            <h5 class="fw-bold mb-2">{{ $post->title }}</h5>
                                        @endif
                                        
                                        <p class="mb-3">{{ $post->content }}</p>

                                        @if($post->image)
                                            <div class="mb-3">
                                                <img src="{{ asset('storage/' . $post->image) }}" 
                                                     class="img-fluid rounded" 
                                                     style="max-height: 400px; width: 100%; object-fit: cover;" 
                                                     alt="Post image">
                                            </div>
                                        @endif

                                        {{-- Post Interactions --}}
                                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                            <div class="d-flex gap-3">
                                                <span class="text-muted small">
                                                    <i class="fas fa-heart me-1"></i>{{ $post->reactions->where('type', 'like')->count() }} likes
                                                </span>
                                                <span class="text-muted small">
                                                    <i class="fas fa-comment me-1"></i>{{ $post->comments->count() }} comments
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Posts Pagination --}}
                        @if($totalPostsPages > 1)
                            <div class="d-flex justify-content-center align-items-center mt-4 gap-3">
                                <a href="?posts_page={{ max(1, $currentPostsPage - 1) }}#posts" 
                                   class="btn btn-outline-primary rounded-pill {{ $currentPostsPage <= 1 ? 'disabled' : '' }}">
                                    <i class="fas fa-chevron-left me-2"></i>Previous
                                </a>
                                <span class="text-muted">
                                    Page <strong>{{ $currentPostsPage }}</strong> of <strong>{{ $totalPostsPages }}</strong>
                                </span>
                                <a href="?posts_page={{ min($totalPostsPages, $currentPostsPage + 1) }}#posts" 
                                   class="btn btn-outline-primary rounded-pill {{ $currentPostsPage >= $totalPostsPages ? 'disabled' : '' }}">
                                    Next<i class="fas fa-chevron-right ms-2"></i>
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
    .nav-tabs .nav-link {
        border: none;
        border-radius: 15px 15px 0 0;
        margin-right: 10px;
        color: #6c757d;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link.active {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        border: none;
    }
    
    .nav-tabs .nav-link:hover {
        border: none;
        background: #e9ecef;
        color: #495057;
    }
    
    .nav-tabs .nav-link.active:hover {
        background: linear-gradient(45deg, #5a6fd8, #6a4c93);
        color: white;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    
    .order-items::-webkit-scrollbar {
        width: 4px;
    }
    
    .order-items::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .order-items::-webkit-scrollbar-thumb {
        background: #667eea;
        border-radius: 10px;
    }

    .btn.disabled {
        pointer-events: none;
        opacity: 0.5;
    }
    
    @media (max-width: 768px) {
        .nav-tabs .nav-link {
            margin-right: 5px;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        
        .display-5 {
            font-size: 1.8rem;
        }
    }
</style>

{{-- Add Bootstrap JS if not already included --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Scroll to active tab on page load if there's a hash
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash) {
            const hash = window.location.hash.substring(1);
            const tabButton = document.getElementById(hash + '-tab');
            if (tabButton) {
                const tab = new bootstrap.Tab(tabButton);
                tab.show();
                setTimeout(() => {
                    document.getElementById(hash).scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        }
    });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush
@endsection