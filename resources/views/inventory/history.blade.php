@extends('layouts.app')
@section('title', 'Inventory History - ' . $product->name)

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
                <i class="fas fa-history me-2" style="color: #667eea;"></i>
                Inventory History
            </h2>
            <p class="text-muted mb-0">Track all stock movements for {{ $product->name }}</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Inventory
        </a>
    </div>

    {{-- Product Info Card --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-2">
                    @if($product->images && $product->images->count())
                        <img src="{{ asset('storage/' . $product->images->first()->image) }}" 
                             alt="{{ $product->name }}"
                             class="rounded w-100"
                             style="object-fit: cover; max-height: 120px;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                             style="height: 120px;">
                            <i class="fas fa-fish text-muted fa-3x"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 class="fw-bold mb-2">{{ $product->name }}</h4>
                    <p class="text-muted mb-2">{{ Str::limit($product->description, 100) }}</p>
                    <span class="badge bg-primary px-3 py-2">SKU: {{ $product->id }}</span>
                </div>
                <div class="col-md-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 rounded" style="background: rgba(102, 126, 234, 0.1);">
                                <p class="small text-muted mb-1">Current Stock</p>
                                <h4 class="fw-bold mb-0" style="color: #667eea;">{{ $product->quantity }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 rounded" style="background: rgba(40, 167, 69, 0.1);">
                                <p class="small text-muted mb-1">Price</p>
                                <h4 class="fw-bold mb-0 text-success">₱{{ number_format($product->price, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- History Timeline --}}
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header border-0 bg-white pt-4 px-4">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-list-alt me-2"></i>
                Stock Movement History
            </h5>
        </div>
        <div class="card-body p-4">
            @forelse($logs as $log)
            <div class="d-flex mb-4 pb-4 border-bottom position-relative">
                {{-- Timeline Line --}}
                @if(!$loop->last)
                <div class="position-absolute" 
                     style="left: 19px; top: 50px; width: 2px; height: 100%; background: #e9ecef;"></div>
                @endif

                {{-- Icon --}}
                <div class="me-3 flex-shrink-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 40px; height: 40px; background: {{ $log->type === 'in' ? 'rgba(40, 167, 69, 0.1)' : 'rgba(220, 53, 69, 0.1)' }};">
                        <i class="fas fa-{{ $log->type === 'in' ? 'arrow-up' : 'arrow-down' }}" 
                           style="color: {{ $log->type === 'in' ? '#28a745' : '#dc3545' }};"></i>
                    </div>
                </div>

                {{-- Content --}}
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-1">
                                {{ $log->getTypeLabel() }}
                                <span class="badge bg-{{ $log->getTypeBadgeClass() }} ms-2">
                                    {{ $log->type === 'in' ? '+' : '-' }}{{ $log->quantity }} units
                                </span>
                            </h6>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-user me-1"></i>{{ $log->user->name }}
                                <span class="mx-2">•</span>
                                <i class="fas fa-clock me-1"></i>{{ $log->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Stock Level</small>
                            <span class="badge bg-secondary">
                                {{ $log->old_quantity }} → {{ $log->new_quantity }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-auto">
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-tag me-1"></i>
                                {{ ucfirst($log->reason) }}
                            </span>
                        </div>
                    </div>

                    @if($log->notes)
                    <div class="alert alert-light border-0 mb-0 mt-2" style="background-color: #f8f9fa;">
                        <i class="fas fa-sticky-note me-2 text-muted"></i>
                        <small>{{ $log->notes }}</small>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-history text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                <p class="text-muted mt-3">No inventory history found</p>
            </div>
            @endforelse

            {{-- Pagination --}}
            @if($logs->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Stock In</p>
                            <h3 class="fw-bold mb-0">
                                {{ $logs->where('type', 'in')->sum('quantity') }} units
                            </h3>
                        </div>
                        <i class="fas fa-arrow-up fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Stock Out</p>
                            <h3 class="fw-bold mb-0">
                                {{ $logs->where('type', 'out')->sum('quantity') }} units
                            </h3>
                        </div>
                        <i class="fas fa-arrow-down fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #0bb364 100%);">
                <div class="card-body text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 opacity-75">Total Movements</p>
                            <h3 class="fw-bold mb-0">{{ $logs->count() }}</h3>
                        </div>
                        <i class="fas fa-exchange-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@endsection