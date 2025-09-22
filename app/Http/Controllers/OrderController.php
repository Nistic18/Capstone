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
}
