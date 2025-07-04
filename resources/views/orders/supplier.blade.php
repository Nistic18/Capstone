@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Your Product Orders</h2>

    @forelse ($orders as $order)
        @php
            $products = $order->products;
            $hasDeliveredAll = $products->every(fn($p) => $p->pivot->product_status === 'Delivered');
        @endphp

        <div class="card my-3">
            <div class="card-header">
                Order #{{ $order->id }} • {{ $order->created_at->format('Y-m-d H:i') }}
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('supplier.orders.status.bulk-update', $order->id) }}">
                    @csrf
                    @method('PUT')
                    <table class="table table-bordered">
                        <thead>
                            <tr><th>Product</th><th>Qty</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->pivot->quantity }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $product->pivot->product_status }}</span>
                                    <input type="hidden" name="product_ids[]" value="{{ $product->id }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if (!$hasDeliveredAll)
                        <div class="d-flex justify-content-end mt-2">
                            <select name="product_status" class="form-select form-select-sm w-auto me-2">
                                <option value="Pending">Pending</option>
                                <option value="Shipped">Shipped</option>
                                <option value="Delivered">Delivered</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Update All</button>
                        </div>
                    @else
                        <div class="text-success text-end mt-2">All products delivered.</div>
                    @endif
                </form>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No orders for your products.</div>
    @endforelse
</div>
@endsection
