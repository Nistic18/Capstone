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

        return view('cart.index', compact('cart'));
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

public function checkout()
{
    DB::beginTransaction();

    try {
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        $total = 0;

        $order = Order::create([
            'user_id' => Auth::id(),
            'status' => 'Pending',
            'total_price' => 0 // will update later
        ]);

        foreach ($cartItems as $item) {
            $product = $item->product;

            if (!$product) {
                throw new \Exception("One of the products in your cart no longer exists.");
            }

            // Validate stock
            if ($item->quantity > $product->quantity) {
                throw new \Exception("Not enough stock for {$product->name}. Available: {$product->quantity}");
            }

            // Calculate total
            $total += $product->price * $item->quantity;

            // Attach product to order with quantity and pending status
            $order->products()->attach($product->id, [
                'quantity' => $item->quantity,
                'product_status' => 'Pending'
            ]);
            // 🔔 Notify the seller (reseller) about checkout
            if ($product->user) {
                $product->user->notify(new ProductCheckedOut($order, $product));
            }
            // Deduct quantity from product stock
            $product->decrement('quantity', $item->quantity);
        }

        // Update total price in order
        $order->update(['total_price' => $total]);

        // Clear user's cart
        Cart::where('user_id', Auth::id())->delete();

        DB::commit();

        return redirect()->route('orders.index')->with('success', 'Checkout successful!');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', $e->getMessage());
    }
}
}
