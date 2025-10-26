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
    // Show user reviews - BUYER ONLY
    public function index()
{
    $user = auth()->user();

    if ($user->role === 'buyer') {
        // Buyer: show reviews they have written
        $reviews = \App\Models\Review::where('user_id', $user->id)
                    ->with(['product', 'order'])
                    ->latest()
                    ->get();
        $title = 'My Reviews';
    } elseif ($user->role === 'supplier') {
        // Supplier: show reviews for their products
        $reviews = \App\Models\Review::whereHas('product', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->with(['product', 'order', 'user'])
                    ->latest()
                    ->get();
        $title = 'Product Reviews';
    } else {
        abort(403, 'Unauthorized access.');
    }

    return view('profile.reviews', compact('reviews', 'title'));
}
    public function supplierReviews()
{
    // Only suppliers can access this
    if (auth()->user()->role !== 'supplier') {
        abort(403, 'Only suppliers can view this page.');
    }

    // Get reviews for products owned by this supplier
    $reviews = \App\Models\Review::whereHas('product', function ($query) {
        $query->where('user_id', auth()->id());
    })
    ->with(['product', 'order', 'user'])
    ->latest()
    ->get();

    return view('supplier.reviews', compact('reviews'));
}
}

