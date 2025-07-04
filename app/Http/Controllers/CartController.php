<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
public function checkout()
{
    $userId = auth()->id();
    $cartItems = Cart::with('product')->where('user_id', $userId)->get();

    if ($cartItems->isEmpty()) {
        return back()->with('error', 'Your cart is empty.');
    }

    DB::transaction(function () use ($cartItems, $userId) {
        $total = 0;

        foreach ($cartItems as $item) {
            if (!$item->product || $item->product->status === 'sold') {
                throw new \Exception("Product {$item->product->name} is not available.");
            }

            $total += $item->product->price * $item->quantity;
        }

        $order = Order::create([
            'user_id' => $userId,
            'total_price' => $total,
            'status' => 'Pending', // or "Processing"
        ]);

        foreach ($cartItems as $item) {
            $order->products()->attach($item->product->id, [
                'quantity' => $item->quantity,
                'product_status' => 'Pending'
            ]);

            // ✅ Mark product as sold
            $item->product->update(['status' => 'sold']);
        }

        // ✅ Clear the cart
        Cart::where('user_id', $userId)->delete();
    });

    return redirect()->route('cart.index')->with('success', 'Checkout complete! Thank you for your purchase.');
}
}
