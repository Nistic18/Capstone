@extends('layouts.app')
@section('title', $title)
<link rel="icon" type="image/png" href="{{ asset('img/avatar/dried-fish-logo.png') }}">
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<div class="container mt-4">
    <h3 class="fw-bold mb-4">{{ $title }}</h3>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($reviews->count() > 0)
        <div class="list-group">
            @foreach($reviews as $review)
                <div class="list-group-item mb-3 shadow-sm" style="border-radius: 15px;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold">Product: {{ $review->product->name ?? 'Unknown Product' }}</div>
                        <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                    </div>

                    {{-- Star Rating --}}
                    <div class="mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fas fa-star text-warning"></i>
                            @else
                                <i class="far fa-star text-warning"></i>
                            @endif
                        @endfor
                    </div>

                    {{-- Comment --}}
                    <p class="mb-2">{{ $review->comment ?: 'No comment provided.' }}</p>

                    {{-- Show buyer name only if supplier --}}
                    @if(auth()->user()->role === 'supplier')
                        <p><strong>Buyer:</strong> {{ $review->user->name ?? 'Anonymous' }}</p>
                    @endif

                    {{-- Order Reference --}}
                    @if($review->order)
                        <small class="text-muted">Order #{{ $review->order->id }}</small>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-star-half-alt text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
            <h5 class="text-muted mb-2">No Reviews Yet</h5>
            <p class="text-muted">
                {{ auth()->user()->role === 'buyer' ? 'Start reviewing your purchased products!' : 'No one has reviewed your products yet.' }}
            </p>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
@endsection
