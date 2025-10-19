<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Store a review
    public function store(Request $request, $orderId, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // Check if the logged-in user owns the order
        $order = auth()->user()->orders()->findOrFail($orderId);

        \App\Models\Product::findOrFail($productId); // Ensure product exists

        Review::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $productId,
                'order_id' => $orderId,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return back()->with('success', 'Review submitted!');
    }

    // Show user reviews
    public function index()
{
    if(auth()->user()->role !== 'buyer') {
        // Supplier: show reviews received on their products
        $reviews = Review::whereIn('product_id', auth()->user()->products->pluck('id'))
                         ->with(['product', 'user', 'order'])
                         ->latest()
                         ->get();
    } else {
        // Buyer: show reviews they wrote
        $reviews = Review::where('user_id', auth()->id())
                         ->with(['product', 'order'])
                         ->latest()
                         ->get();
    }

    return view('profile.reviews', compact('reviews'));
}
}
