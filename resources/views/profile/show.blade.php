@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
            <h3>{{ $user->name }}'s Profile</h3>
            <p class="text-muted">Joined: {{ $user->created_at->format('M Y') }}</p>

            {{-- Overall Average Rating --}}
            @php
                $allReviews = $user->products->flatMap->reviews; 
                $avgRating = $allReviews->avg('rating');
            @endphp

            @if($allReviews->count() > 0)
                <h5 class="mt-2">
                    ⭐ {{ number_format($avgRating, 1) }} / 5.0
                    <small class="text-muted">({{ $allReviews->count() }} reviews)</small>
                </h5>
            @else
                <h6 class="mt-2 text-muted">No reviews yet</h6>
            @endif
        </div>
    </div>

    <h4 class="mb-3">Products Posted</h4>

    @if($user->products->isEmpty())
        <div class="alert alert-info">This user has not posted any products yet.</div>
    @else
        <div class="row">
            @foreach($user->products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">${{ number_format($product->price, 2) }}</p>
                            <p><strong>Quantity:</strong> {{ $product->quantity }}</p>
                            <p>
                                <strong>Rating:</strong> 
                                @if($product->averageRating())
                                    {{ number_format($product->averageRating(), 1) }} ★ ({{ $product->reviews->count() }} reviews)
                                @else
                                    No reviews yet
                                @endif
                            </p>
                        </div>
                        <div class="card-footer">
                            <h6>Reviews:</h6>
                            @forelse($product->reviews as $review)
                                <div class="border rounded p-2 mb-2">
                                    <strong>{{ $review->user->name }}</strong>  
                                    <span class="text-warning">
                                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                    </span><br>
                                    <small>{{ $review->comment }}</small>
                                </div>
                            @empty
                                <p class="text-muted">No reviews yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
