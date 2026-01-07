@extends('layouts.app')

@section('title', 'Order QR Code')
<link rel="icon" type="image/png" href="{{ asset('img/avatar/dried-fish-logo.png') }}">
@section('content')
<div class="container text-center mt-5">
    <h3 class="mb-3">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h3>
    <p class="text-muted mb-4">Scan this QR code to confirm delivery.</p>
    
    <div class="card shadow-sm mx-auto" style="max-width: 400px; border-radius: 20px;">
        <div class="card-body">
            {{-- ✅ Replace the <img> line with this --}}
            <div class="qr-container">{!! $qrCode !!}</div>

            <p class="text-secondary small mb-0 mt-3">Scan or open: <br>
                <a href="{{ $qrUrl }}" target="_blank">{{ $qrUrl }}</a>
            </p>
        </div>
    </div>

    <a href="{{ route('supplier.orders') }}" class="btn btn-outline-primary mt-4" style="border-radius: 10px;">
        <i class="fas fa-arrow-left me-1"></i> Back to Orders
    </a>
</div>
@endsection
