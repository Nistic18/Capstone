@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card card-body">
        <h3 class="mb-4">🛒 Your Cart</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($cart->isEmpty())
            <p class="text-muted">Your cart is empty.</p>
        @else
            @php $total = 0; @endphp

            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $item)
                        @php
                            $maxStock = $item->product->stock;
                            $subtotal = $item->product->price * $item->quantity;
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>${{ number_format($item->product->price, 2) }}</td>
                            <td>
                                <form action="{{ route('cart.update', $item->product->id) }}" method="POST" class="d-flex">
                                    @csrf
                                    @method('PUT') {{-- ✅ Tell Laravel this is a PUT request --}}
                                    <input 
                                        type="number" 
                                        name="quantity" 
                                        value="{{ $item->quantity }}" 
                                        min="1" 
                                        max="{{ $maxStock }}" 
                                        class="form-control form-control-sm me-2"
                                        style="width: 80px"
                                    >
                                    <button type="submit" class="btn btn-sm btn-success">Update</button>
                                </form>
                                <small class="text-muted">Stock: {{ $item->product->quantity }}</small>
                            </td>
                            <td>${{ number_format($subtotal, 2) }}</td>
                            <td>
                                <form action="{{ route('cart.remove', $item->product->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Remove this item?')">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                <h5><strong>Total: ${{ number_format($total, 2) }}</strong></h5>
            </div>
            <form action="{{ route('cart.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg mt-3">Checkout</button>
            </form>
        @endif
    </div>
</div>
@endsection
