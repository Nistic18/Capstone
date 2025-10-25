@extends('layouts.app')

@section('title', 'Order Delivered')

@section('content')
<div class="container text-center mt-5">
    <div class="card shadow-sm mx-auto" style="max-width: 400px; border-radius: 20px;">
        <div class="card-body p-4">
            <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
            <h4 class="fw-bold mb-3">Order Delivered!</h4>
            <p class="text-muted">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} has been successfully marked as <strong>Delivered</strong>.</p>
        </div>
    </div>

    <a href="{{ route('orders.index') }}" class="btn btn-success mt-4" style="border-radius: 10px;">
        <i class="fas fa-home me-1"></i> Back to My Orders
    </a>
</div>
@endsection
