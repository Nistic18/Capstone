@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <h2>Product Detail</h2>
</div>

<div class="card">
    <div class="card-body text-center">

        {{-- ✅ Show image if available --}}
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" 
                 alt="{{ $product->name }}" 
                 class="img-fluid mb-3" 
                 style="max-height: 300px;">
        @else
            <p class="text-muted">No image available.</p>
        @endif

        <h5 class="card-title">{{ $product->name }}</h5>
        <h6 class="card-subtitle mb-2 text-muted">${{ $product->price }}</h6>
        <p class="mb-1"><strong>Quantity:</strong> {{ $product->quantity }}</p>
        <p class="card-text">{{ $product->description }}</p>
        @if($product->user)
        <p class="text-muted"><strong>Reseller:</strong> {{ $product->user->name }}</p>
        @endif
        <a href="{{ route('home') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection
