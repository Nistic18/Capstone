<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Notifications\ProductCheckedOut;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        // Calculate total quantity
        $totalQuantity = $cart->sum('quantity');

        // Determine delivery fee
        $deliveryFee = $totalQuantity >= 10 ? 0 : 0;

        // Calculate total price
        $total = $cart->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        return view('cart.index', compact('cart', 'total', 'deliveryFee'));
    }

    public function add(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = auth()->id();
        $quantity = $request->input('quantity');

        $cartItem = Cart::where('user_id', $userId)
                        ->where('product_id', $productId)
                        ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }
        return back()->with('success', 'Product added to cart!');
    }

    public function remove($id)
    {
        Cart::where('user_id', Auth::id())
            ->where('product_id', $id)
            ->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Find the cart item for this user and product
        $cartItem = Cart::where('user_id', Auth::id())
                        ->where('product_id', $productId)
                        ->with('product')
                        ->first();

        if (!$cartItem) {
            return back()->with('error', 'Product not found in cart.');
        }

        // Make sure quantity does not exceed stock
        $maxStock = $cartItem->product->quantity;
        if ($request->quantity > $maxStock) {
            return back()->with('error', "Only $maxStock items available in stock.");
        }

        // Update quantity
        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Quantity updated successfully!');
    }

    public function checkout(Request $request)
    {
        DB::beginTransaction();

        try {
            // Decode selected product IDs from hidden input
            $selectedProducts = json_decode($request->input('selected_products'), true);

            if (empty($selectedProducts)) {
                return redirect()->back()->with('error', 'Please select at least one product to checkout.');
            }

            // Fetch only selected items from the user's cart
            $cartItems = Cart::where('user_id', Auth::id())
                            ->whereIn('product_id', $selectedProducts)
                            ->with('product.user') // Load supplier relationship
                            ->get();

            if ($cartItems->isEmpty()) {
                return redirect()->back()->with('error', 'No valid selected products found in your cart.');
            }

            // Group cart items by supplier (product owner)
            $itemsBySupplier = $cartItems->groupBy(function($item) {
                return $item->product->user_id;
            });

            // Determine order status based on payment method
            $paymentMethod = $request->payment_method;
            $orderStatus = ($paymentMethod === 'Pickup') ? 'Packed' : 'Pending';

            $createdOrders = [];

            // Create separate order for each supplier
            foreach ($itemsBySupplier as $supplierId => $supplierItems) {
                $totalQuantity = $supplierItems->sum('quantity');
                $deliveryFee = $totalQuantity >= 10 ? 0 : 0;
                $totalProducts = 0;

                // Create order for this supplier's products
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'status' => $orderStatus,
                    'total_price' => 0, // temporary
                    'delivery_fee' => $deliveryFee,
                    'payment_method' => $paymentMethod
                ]);

                foreach ($supplierItems as $item) {
                    $product = $item->product;

                    if (!$product) {
                        throw new \Exception("One of the selected products no longer exists.");
                    }

                    if ($item->quantity > $product->quantity) {
                        throw new \Exception("Not enough stock for {$product->name}. Available: {$product->quantity}");
                    }

                    // Add to order with appropriate product status
                    $order->products()->attach($product->id, [
                        'quantity' => $item->quantity,
                        'product_status' => $orderStatus
                    ]);

                    // Notify seller
                    if ($product->user) {
                        $product->user->notify(new ProductCheckedOut($order, $product));
                    }

                    // Update totals & stock
                    $totalProducts += $product->price * $item->quantity;
                    $product->decrement('quantity', $item->quantity);
                }

                // Finalize order total
                $order->update(['total_price' => $totalProducts + $deliveryFee]);
                
                $createdOrders[] = $order->id;
            }

            // Remove only selected items from cart
            Cart::where('user_id', Auth::id())
                ->whereIn('product_id', $selectedProducts)
                ->delete();

            DB::commit();

            $orderCount = count($createdOrders);
            $message = $orderCount > 1 
                ? "Checkout successful! {$orderCount} orders created (one per supplier)." 
                : "Checkout successful!";

            return redirect()->route('orders.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}