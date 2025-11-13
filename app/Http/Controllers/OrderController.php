<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderStatusUpdated;
use App\Notifications\OrderDeliveredNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
class OrderController extends Controller
{
   public function index()
{
    if (auth()->user()->role === 'seller') {
        abort(403, 'Unauthorized access.');
    }

    $userId = Auth::id();

    // Query database SEPARATELY for each tab - most reliable method
    $orders = Order::with('products.images')
        ->where('user_id', $userId)
        ->orderByDesc('created_at')
        ->get();

    $allOrders = $orders;
    
    $toShipOrders = Order::with('products.images')
        ->where('user_id', $userId)
        ->where('status', 'Pending')
        ->orderByDesc('created_at')
        ->get();
    
    $toReceiveOrders = Order::with('products.images')
        ->where('user_id', $userId)
        ->where('status', 'Shipped')
        ->orderByDesc('created_at')
        ->get();
    
    $completedOrders = Order::with('products.images')
        ->where('user_id', $userId)
        ->where('status', 'Delivered')
        ->where('refund_status', 'None')
        ->orderByDesc('created_at')
        ->get();
    
    $cancelledOrders = Order::with('products.images')
        ->where('user_id', $userId)
        ->where('status', 'Cancelled')
        ->orderByDesc('created_at')
        ->get();
    
    $refundOrders = Order::with('products.images')
        ->where('user_id', $userId)
        ->whereIn('refund_status', ['Pending', 'Approved', 'Rejected'])
        ->orderByDesc('created_at')
        ->get();

    // Debug logs
    // \Log::info('=== ORDER COUNTS ===');
    // \Log::info('All: ' . $allOrders->count());
    // \Log::info('To Ship: ' . $toShipOrders->count());
    // \Log::info('To Receive: ' . $toReceiveOrders->count());
    // \Log::info('Completed: ' . $completedOrders->count());
    // \Log::info('Cancelled: ' . $cancelledOrders->count());
    // \Log::info('Refund: ' . $refundOrders->count());

    return view('orders.index', compact(
        'orders',
        'allOrders',
        'toShipOrders',
        'toReceiveOrders',
        'completedOrders',
        'cancelledOrders',
        'refundOrders'
    ));
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
    ]);

    $userId = auth()->id();

    // Get all products in this order that belong to the supplier
    $products = Product::whereIn('id', function ($query) use ($orderId, $userId) {
        $query->select('product_id')
              ->from('order_product')
              ->where('order_id', $orderId);
    })
    ->where('user_id', $userId)
    ->get();

    foreach ($products as $product) {
        DB::table('order_product')
            ->where('order_id', $orderId)
            ->where('product_id', $product->id)
            ->update(['product_status' => $request->product_status]);
    }

    // Update overall order status
    $statuses = DB::table('order_product')
        ->where('order_id', $orderId)
        ->pluck('product_status');

    if ($statuses->every(fn($status) => $status === 'Delivered')) {
        Order::where('id', $orderId)->update(['status' => 'Delivered']);
    } else {
        Order::where('id', $orderId)->update(['status' => $request->product_status]);
    }

    // Notify buyer
    $order = Order::findOrFail($orderId);
    $order->user->notify(new OrderStatusUpdated($order));

    return back()->with('success', 'All products updated successfully.');
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

    // Update refund_status AND order status
    $order->update([
        'refund_status' => 'Approved',
        'status' => 'Refunded',
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
        'status' => 'Declined Refund',
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
        // AND revert the quantities back to inventory
        foreach ($order->products as $product) {
            $orderQuantity = $product->pivot->quantity;
            $oldQuantity = $product->quantity;
            $newQuantity = $oldQuantity + $orderQuantity;

            // Update product quantity
            $product->update(['quantity' => $newQuantity]);

            // Log the inventory restoration
            $product->inventoryLogs()->create([
                'user_id' => $product->user_id, // The seller/supplier who owns the product
                'type' => 'in',
                'quantity' => $orderQuantity,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'reason' => 'order_cancelled',
                'notes' => "Order #{$order->id} cancelled by buyer. Reason: {$request->cancel_reason}",
            ]);
        }

        // Update pivot table status
        DB::table('order_product')
            ->where('order_id', $order->id)
            ->update(['product_status' => 'Cancelled']);
    });

    // Notify all sellers involved in this order
    $order->products->each(function ($product) use ($order) {
        $product->user->notify(new OrderStatusUpdated($order));
    });

    return back()->with('success', 'Order cancelled successfully.');
}

public function seller()
{
    return $this->belongsTo(User::class, 'seller_id');
}

public function generateQR($orderId)
{
    $order = Order::findOrFail($orderId);

    // ✅ Create a temporary signed URL (valid for 24 hours)
    $qrUrl = URL::temporarySignedRoute(
        'orders.qrDeliver', // route name
        now()->addHours(24), // expiration
        ['order' => $order->id]
    );

    // ✅ Use SVG format (no Imagick/GD needed)
    $qrCode = QrCode::format('svg')->size(250)->generate($qrUrl);

    return view('orders.qr', compact('order', 'qrCode', 'qrUrl'));
}


public function qrDeliver(Request $request, $orderId)
{
    if (! $request->hasValidSignature()) {
        abort(403, 'Invalid or expired QR code.');
    }

    $order = Order::with('products')->findOrFail($orderId);

    if (Auth::id() !== $order->user_id) {
        Log::warning("Unauthorized QR scan attempt for order {$order->id} by user " . Auth::id());
        abort(403, 'You are not authorized to confirm this delivery.');
    }

    // ✅ Update all product_status to Delivered
    DB::table('order_product')
        ->where('order_id', $orderId)
        ->update(['product_status' => 'Delivered']);

    // ✅ Update order status to Delivered
    $order->status = 'Delivered';
    $order->save();

    // ✅ Notify buyer (or other logic if needed)
    $order->user->notify(new OrderDeliveredNotification($order));

    Log::info("Order {$order->id} marked as delivered via QR scan by buyer " . Auth::id());

    return view('orders.qr-confirm', compact('order'));
}
}
