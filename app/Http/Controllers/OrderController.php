<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Notifications\OrderStatusUpdated; // ✅ Added

class OrderController extends Controller
{
    public function index()
    {
        // Buyers can view their own orders
        if (auth()->user()->role === 'seller') {
            abort(403, 'Unauthorized access.');
        }

        $orders = Order::with('products') // eager load products
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function supplierOrders()
    {
        // Only sellers or admins can access supplier orders
        if (auth()->user()->role === 'buyer') {
            abort(403, 'Unauthorized access.');
        }

        $userId = auth()->id();

        $orders = Order::whereHas('products', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['products' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->latest()
            ->get();

        return view('orders.supplier', compact('orders'));
    }

    public function updateProductStatus(Request $request, $orderId, $productId)
    {
        if (auth()->user()->role === 'buyer') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'product_status' => 'required|string',
        ]);

        $userId = auth()->id();

        // Ensure the authenticated supplier owns this product
        $product = Product::where('id', $productId)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Update product status in pivot table
        DB::table('order_product')
            ->where('order_id', $orderId)
            ->where('product_id', $productId)
            ->update(['product_status' => $request->product_status]);

        // Update order status based on all product statuses
        $statuses = DB::table('order_product')
            ->where('order_id', $orderId)
            ->pluck('product_status');

        if ($statuses->every(fn($status) => $status === 'Delivered')) {
            Order::where('id', $orderId)->update(['status' => 'Delivered']);
        } else {
            Order::where('id', $orderId)->update(['status' => $request->product_status]);
        }

        // ✅ Notify buyer
        $order = Order::findOrFail($orderId);
        $order->user->notify(new OrderStatusUpdated($order));

        return back()->with('success', 'Product and order status updated.');
    }

    public function bulkUpdateProductStatus(Request $request, $orderId)
    {
        if (auth()->user()->role === 'buyer') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'product_status' => 'required|string',
            'product_ids' => 'required|array',
        ]);

        $userId = auth()->id();

        foreach ($request->product_ids as $productId) {
            $product = Product::where('id', $productId)
                ->where('user_id', $userId)
                ->first();

            if ($product) {
                DB::table('order_product')
                    ->where('order_id', $orderId)
                    ->where('product_id', $productId)
                    ->update(['product_status' => $request->product_status]);
            }
        }

        // Update order status
        $statuses = DB::table('order_product')
            ->where('order_id', $orderId)
            ->pluck('product_status');

        if ($statuses->every(fn($status) => $status === 'Delivered')) {
            Order::where('id', $orderId)->update(['status' => 'Delivered']);
        } else {
            Order::where('id', $orderId)->update(['status' => $request->product_status]);
        }

        // ✅ Notify buyer
        $order = Order::findOrFail($orderId);
        $order->user->notify(new OrderStatusUpdated($order));

        return back()->with('success', 'Statuses updated for selected products and order.');
    }

public function show(Order $order)
{
    // Allow buyers to view only their orders
    if (auth()->user()->role === 'buyer' && $order->user_id !== auth()->id()) {
        abort(403, 'Unauthorized access.');
    }

    // Allow sellers to view only if they own one of the products
    if (auth()->user()->role === 'seller') {
        $ownsProduct = $order->products()->where('user_id', auth()->id())->exists();
        if (! $ownsProduct) {
            abort(403, 'Unauthorized access.');
        }
    }
    $order->load('products.images', 'user'); // eager load relations
    return view('orders.show', compact('order'));
}
public function requestRefund(Request $request, $orderId)
{
    $order = Order::where('id', $orderId)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $request->validate([
        'refund_reason' => 'required|string|max:500',
    ]);

    // Only allow refund if delivered or paid
    if (!in_array($order->status, ['Delivered', 'paid'])) {
        return back()->with('error', 'Refunds can only be requested for delivered or paid orders.');
    }

    $order->update([
        'refund_status' => 'Pending',
        'refund_reason' => $request->refund_reason,
    ]);

    return back()->with('success', 'Refund request submitted successfully.');
}
public function approveRefund($orderId)
{
    $order = Order::findOrFail($orderId);

    
    if (!in_array(auth()->user()->role, ['supplier', 'reseller'])) {
        abort(403, 'Unauthorized access.');
    }

    $order->update([
        'refund_status' => 'Approved',
    ]);

    // Notify buyer
    $order->user->notify(new OrderStatusUpdated($order));

    return back()->with('success', 'Refund approved successfully.');
}

public function declineRefund(Request $request, $orderId)
{
    $order = Order::findOrFail($orderId);

    if (!in_array(auth()->user()->role, ['supplier', 'reseller'])) {
        abort(403, 'Unauthorized access.');
    }

    $request->validate([
        'decline_reason' => 'nullable|string|max:500',
    ]);

    $order->update([
        'refund_status' => 'Rejected',
        'refund_reason' => 'Seller/Reseller: ' . $request->decline_reason,
    ]);

    // Notify buyer
    $order->user->notify(new OrderStatusUpdated($order));

    return back()->with('success', 'Refund declined successfully.');
}
public function cancelOrder(Request $request, $orderId)
{
    $order = Order::where('id', $orderId)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    if (in_array($order->status, ['Delivered', 'Cancelled', 'Shipped'])) {
        return back()->with('error', 'This order cannot be cancelled.');
    }

    $request->validate([
        'cancel_reason' => 'required|string|max:500',
    ]);

    DB::transaction(function () use ($order, $request) {
        // Update the order status
        $order->update([
            'status' => 'Cancelled',
            'cancel_reason' => $request->cancel_reason,
        ]);

        // Update all products in the order to 'Cancelled' in pivot table
        DB::table('order_product')
            ->where('order_id', $order->id)
            ->update(['product_status' => 'Cancelled']);
    });

    // Notify all sellers involved in this order
    $order->products->each(function ($product) use ($order) {
        $product->user->notify(new OrderStatusUpdated($order));
    });

    return back()->with('success', 'Order and all products cancelled successfully.');
}



}
