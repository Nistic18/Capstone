@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="container mt-4">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background: transparent; padding: 2%;">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none" style="color: #667eea;">
                    <i class="fas fa-home me-1"></i>Home
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-bell me-1"></i>Notifications
            </li>
        </ol>
    </nav>

    {{-- Page Header --}}
    {{-- <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #0bb364 100%); border-radius: 20px;">
        <div class="card-body text-center py-4">
            <div class="mb-3">
                <i class="fas fa-bell text-white" style="font-size: 2.5rem;"></i>
            </div>
            <h1 class="text-white fw-bold mb-2">🔔 Notifications</h1>
            <p class="text-white-50 mb-0">Stay updated with your latest activities and order updates</p>
        </div>
    </div> --}}

    @php
        $unreadCount = $notifications->where('read_at', null)->count();
        $totalCount = $notifications->count();
    @endphp

    {{-- Notification Actions --}}
    @if($totalCount > 0)
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-info text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold" style="color: #2c3e50;">Notification Summary</h6>
                                <small class="text-muted">
                                    @if($unreadCount > 0)
                                        <span class="badge bg-danger me-2" style="border-radius: 15px;">{{ $unreadCount }} Unread</span>
                                    @endif
                                    {{ $totalCount }} Total Notifications
                                </small>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-6 text-md-end mt-2 mt-md-0">
                        @if($unreadCount > 0)
                            <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary" style="border-radius: 15px;">
                                    <i class="fas fa-check-double me-2"></i>Mark All as Read
                                </button>
                            </form>
                        @endif
                    </div> --}}
                </div>
            </div>
        </div>
    @endif

    @forelse($notifications as $notification)
        @php
            $isUnread = !$notification->read_at;
            $notificationType = $notification->type ?? 'default';
            
            // Determine notification icon and color based on type or content
            $iconConfig = match(true) {
                str_contains($notification->data['message'] ?? '', 'order') => ['icon' => 'fas fa-shopping-bag', 'color' => '#28a745'],
                str_contains($notification->data['message'] ?? '', 'payment') => ['icon' => 'fas fa-credit-card', 'color' => '#ffc107'],
                str_contains($notification->data['message'] ?? '', 'delivered') => ['icon' => 'fas fa-truck', 'color' => '#17a2b8'],
                str_contains($notification->data['message'] ?? '', 'cancelled') => ['icon' => 'fas fa-times-circle', 'color' => '#dc3545'],
                default => ['icon' => 'fas fa-bell', 'color' => '#667eea'],
            };
        @endphp

        <div class="card border-0 shadow-sm mb-3" style="border-radius: 20px; {{ $isUnread ? 'border-left: 4px solid #667eea !important;' : '' }}">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-1 text-center mb-3 mb-md-0">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 50px; height: 50px; background-color: {{ $iconConfig['color'] }}15; border: 2px solid {{ $iconConfig['color'] }};">
                            <i class="{{ $iconConfig['icon'] }}" style="color: {{ $iconConfig['color'] }}; font-size: 1.2rem;"></i>
                        </div>
                    </div>
                    <div class="col-12 col-md-8 mb-3 mb-md-0">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-2 fw-bold" style="color: #2c3e50;">
                                    Order #{{ str_pad($notification->data['order_id'], 6, '0', STR_PAD_LEFT) }}
                                    has been updated to {{ $notification->data['status'] }}
                                    @if($isUnread)
                                        <span class="badge bg-primary ms-2" style="border-radius: 15px; font-size: 0.7rem;">NEW</span>
                                    @endif
                                </h6>
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                    @if($notification->read_at)
                                        <small class="text-success">
                                            <i class="fas fa-check me-1"></i>
                                            Read on {{ $notification->read_at->format('M d, Y') }}
                                        </small>
                                    @endif
                                </div>
                                @if(isset($notification->data['details']))
                                    <p class="mb-0 mt-2 text-muted small">{{ $notification->data['details'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 text-md-end">
                        <div class="d-flex flex-column gap-2">
                            @if(!$notification->read_at)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100" style="border-radius: 15px;">
                                        <i class="fas fa-check me-2"></i>Mark as Read
                                    </button>
                                </form>
                            @endif
                            @if(isset($notification->data['action_url']))
                                <a href="{{ $notification->data['action_url'] }}" class="btn btn-primary btn-sm w-100" 
                                   style="border-radius: 15px; background: linear-gradient(45deg, #667eea, #0bb364);">
                                    <i class="fas fa-external-link-alt me-2"></i>View Details
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        {{-- Empty Notifications State --}}
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-bell text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                </div>
                <h3 class="text-muted mb-3">No Notifications Yet</h3>
                <p class="text-muted mb-4">You're all caught up! New notifications will appear here when you have updates.</p>
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg" 
                   style="border-radius: 25px; background: linear-gradient(45deg, #667eea, #0bb364); border: none;">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
            </div>
        </div>
    @endforelse

    {{-- Notification Settings Card (Optional) --}}
    {{-- @if($totalCount > 0)
        <div class="card border-0 shadow-sm mt-4" style="border-radius: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="fw-bold mb-1" style="color: #2c3e50;">
                            <i class="fas fa-cog text-primary me-2"></i>Notification Settings
                        </h6>
                        <small class="text-muted">Manage how you receive notifications about orders, promotions, and updates.</small>
                    </div>
                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                        <a href="{{ route('profile.notifications') ?? '#' }}" class="btn btn-outline-primary" style="border-radius: 15px;">
                            <i class="fas fa-sliders-h me-2"></i>Manage Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif --}}

    {{-- Statistics Summary --}}
    @if($totalCount > 0)
        <div class="row g-4 mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 100%);">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: #2c3e50;">
                            <i class="fas fa-chart-line text-primary me-2"></i>Notification Activity
                        </h5>
                        <div class="row g-4 text-center">
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-bell text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h4 class="fw-bold mb-1" style="color: #667eea;">{{ $totalCount }}</h4>
                                    <small class="text-muted">Total Notifications</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-eye text-success mb-2" style="font-size: 2rem;"></i>
                                    <h4 class="fw-bold mb-1" style="color: #28a745;">{{ $totalCount - $unreadCount }}</h4>
                                    <small class="text-muted">Read Notifications</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-envelope text-warning mb-2" style="font-size: 2rem;"></i>
                                    <h4 class="fw-bold mb-1" style="color: #ffc107;">{{ $unreadCount }}</h4>
                                    <small class="text-muted">Unread Notifications</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Custom CSS --}}
<style>
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-outline-primary:hover {
        background: linear-gradient(45deg, #667eea, #0bb364);
        border-color: #667eea;
    }
    
    .badge {
        font-weight: 500;
    }
    
    /* Notification pulse animation for unread */
    .card:has(.badge:contains("NEW")) {
        animation: subtle-pulse 2s infinite;
    }
    
    @keyframes subtle-pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.1);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
        }
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
        
        .btn {
            font-size: 0.9rem;
        }
        
        .row.align-items-center > [class*="col-"] {
            margin-bottom: 1rem;
        }
        
        .row.align-items-center > [class*="col-"]:last-child {
            margin-bottom: 0;
        }
    }
</style>

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection