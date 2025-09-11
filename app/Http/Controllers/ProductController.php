<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
public function home(Request $request)
{
    $query = Product::query();

    // Search
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Price filter
    if ($request->filled('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }
    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    // Sorting
    switch ($request->sort) {
        case 'price_asc':
            $query->orderBy('price', 'asc');
            break;
        case 'price_desc':
            $query->orderBy('price', 'desc');
            break;
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        default:
            $query->latest();
    }

    $products = $query->paginate(9)->appends($request->query());

    return view('home', compact('products'));
}


public function index(Request $request)
{
    // Only allow sellers or admins
    if (auth()->user()->role === 'buyer') {
        abort(403, 'Unauthorized access.');
    }

    $query = \App\Models\Product::where('user_id', Auth::id());

    // Optional: search filter
    if ($request->filled('search')) {
        $query->where('name', 'like', '%'.$request->search.'%');
    }

    // Optional: sorting
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
        }
    }

    $products = $query->paginate(12);

    return view('products.index', compact('products'));
}



    public function create()
    {
        return view('products.create');
    }

public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'price' => 'required|numeric',
        'quantity' => 'required|integer|min:0',
        'description' => 'nullable|string',
        'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048', // validate multiple images
    ]);

    $product = Product::create([
        'name' => $request->name,
        'price' => $request->price,
        'quantity' => $request->quantity,
        'description' => $request->description,
        'user_id' => auth()->id(),
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $product->images()->create(['image' => $path]);
        }
    }

    return redirect()->route('products.index')->with('success', 'Product created with images!');
}

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
    if (auth()->id() !== $product->user_id && auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized access.');
    }

        return view('products.create', compact('product'));
    }

public function update(Request $request, Product $product)
{
    if (auth()->id() !== $product->user_id && auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized access.');
    }

    $request->validate([
        'name' => 'required',
        'price' => 'required|numeric',
        'quantity' => 'required|integer|min:0',
        'description' => 'nullable|string',
        'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $product->update([
        'name' => $request->name,
        'price' => $request->price,
        'quantity' => $request->quantity,
        'description' => $request->description,
    ]);

    // Handle new images if uploaded
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $product->images()->create(['image' => $path]);
        }
    }

    return redirect()->route('products.index')->with('success', 'Product updated with images!');
}

    public function destroy(Product $product)
    {
    if (auth()->id() !== $product->user_id && auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized access.');
    }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted!');
    }
    
    
}
