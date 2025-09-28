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
    $deliveryFee = $totalQuantity >= 10 ? 100 : 50;

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

public function checkout()
{
    DB::beginTransaction();

    try {
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        // Calculate total quantity and delivery fee
        $totalQuantity = $cartItems->sum('quantity');
        $deliveryFee = $totalQuantity >= 10 ? 100 : 50;

        $totalProducts = 0;

        // Create order with delivery fee
        $order = Order::create([
            'user_id' => Auth::id(),
            'status' => 'Pending',
            'total_price' => 0, // temporary, will update after calculating total
            'delivery_fee' => $deliveryFee
        ]);

        foreach ($cartItems as $item) {
            $product = $item->product;

            if (!$product) {
                throw new \Exception("One of the products in your cart no longer exists.");
            }

            if ($item->quantity > $product->quantity) {
                throw new \Exception("Not enough stock for {$product->name}. Available: {$product->quantity}");
            }

            $totalProducts += $product->price * $item->quantity;

            $order->products()->attach($product->id, [
                'quantity' => $item->quantity,
                'product_status' => 'Pending'
            ]);

            if ($product->user) {
                $product->user->notify(new ProductCheckedOut($order, $product));
            }

            $product->decrement('quantity', $item->quantity);
        }

        // Update order total including delivery fee
        $order->update(['total_price' => $totalProducts + $deliveryFee]);

        // Clear cart
        Cart::where('user_id', Auth::id())->delete();

        DB::commit();

        return redirect()->route('orders.index')->with('success', 'Checkout successful!');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', $e->getMessage());
    }
}
}
