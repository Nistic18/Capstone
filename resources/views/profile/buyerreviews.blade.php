@extends('layouts.app')
@section('title', 'My Reviews')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<div class="container mt-4">
    <div class="d-flex align-items-center mb-4">
        <i class="fas fa-star text-warning me-2" style="font-size: 1.5rem;"></i>
        <h3 class="fw-bold mb-0">My Reviews</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($reviews->count() > 0)
        <div class="row">
            <div class="col-12">
                <p class="text-muted mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    You have written <strong>{{ $reviews->count() }}</strong> {{ Str::plural('review', $reviews->count()) }}
                </p>
            </div>
        </div>

        <div class="list-group">
            @foreach($reviews as $review)
                <div class="list-group-item mb-3 shadow-sm border-0" style="border-radius: 15px; background: #f8f9fa;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-semibold mb-1">{{ $review->product->name ?? 'Unknown Product' }}</h5>
                            @if($review->order)
                                <small class="text-muted">
                                    <i class="fas fa-receipt me-1"></i>Order #{{ $review->order->id }}
                                </small>
                            @endif
                        </div>
                        <small class="text-muted">
                            <i class="far fa-clock me-1"></i>{{ $review->created_at->diffForHumans() }}
                        </small>
                    </div>

                    {{-- Star Rating --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="badge bg-warning text-dark">{{ $review->rating }}/5</span>
                        </div>
                    </div>

                    {{-- Comment --}}
                    @if($review->comment)
                        <div class="bg-white p-3 rounded">
                            <p class="mb-0 text-dark">
                                <i class="fas fa-quote-left text-muted me-2" style="font-size: 0.8rem;"></i>
                                {{ $review->comment }}
                                <i class="fas fa-quote-right text-muted ms-2" style="font-size: 0.8rem;"></i>
                            </p>
                        </div>
                    @else
                        <div class="bg-white p-3 rounded">
                            <p class="mb-0 text-muted fst-italic">
                                <i class="fas fa-comment-slash me-2"></i>No comment provided
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-star-half-alt text-muted" style="font-size: 4rem; opacity: 0.2;"></i>
            </div>
            <h5 class="text-muted mb-2">No Reviews Yet</h5>
            <p class="text-muted mb-4">You haven't written any reviews for your purchases yet.</p>
            <a href="{{ route('orders.index') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-2"></i>View My Orders
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@endsection