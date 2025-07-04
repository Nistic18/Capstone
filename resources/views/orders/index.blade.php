@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">My Orders</h2>

    @if ($orders->isEmpty())
        <div class="alert alert-info">You have no orders yet.</div>
    @else
        @foreach ($orders as $order)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <span class="float-end">Date: {{ $order->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="card-body">
                    <p><strong>Total Price:</strong> ${{ number_format($order->total_price, 2) }}</p>
                    <p><strong>Status:</strong> {{ $order->status ?? 'Pending' }}</p>

                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->pivot->quantity }}</td>
                                    <td>${{ number_format($product->price, 2) }}</td>
                                    <td>${{ number_format($product->price * $product->pivot->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif
    @php
    $statusColor = match($order->status) {
        'paid' => 'success',
        'shipped' => 'info',
        'cancelled' => 'danger',
        default => 'secondary',
    };
    @endphp

    <span class="badge bg-{{ $statusColor }}">
    {{ ucfirst($order->status) }}
    </span>
</div>
@endsection
