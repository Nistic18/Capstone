<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductType;
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

        // Filter by category or type
        if ($request->filled('product_category_id')) {
            $query->where('product_category_id', $request->product_category_id);
        }
        if ($request->filled('product_type_id')) {
            $query->where('product_type_id', $request->product_type_id);
        }

        // Price filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->unit_type);
        }
        // Sorting
        switch ($request->sort) {
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            default: $query->latest();
        }

        $products = $query->paginate(9)->appends($request->query());
        $categories = ProductCategory::all();
        $types = ProductType::all();

        return view('home', compact('products', 'categories', 'types'));
    }

    public function index(Request $request)
    {
        if (auth()->user()->role === 'buyer') {
            abort(403, 'Unauthorized access.');
        }

        $query = Product::where('user_id', Auth::id());

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc': $query->orderBy('price', 'asc'); break;
                case 'price_desc': $query->orderBy('price', 'desc'); break;
                case 'name_asc': $query->orderBy('name', 'asc'); break;
                case 'name_desc': $query->orderBy('name', 'desc'); break;
            }
        }

        $products = $query->paginate(12);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $productCategories = ProductCategory::all();
        $productTypes = ProductType::all();

        return view('products.create', compact('productCategories', 'productTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'product_category_id' => 'required|exists:product_categories,id',
            'product_type_id' => 'required|exists:product_types,id',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'unit_type' => 'required|in:pack,kilo,gram,box,piece',
            'unit_value' => 'required|numeric|min:0.01',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'low_stock_threshold' => $request->low_stock_threshold ?? 10,
            'description' => $request->description,
            'product_category_id' => $request->product_category_id,
            'product_type_id' => $request->product_type_id,
            'user_id' => auth()->id(),
            'unit_type' => $request->unit_type,
            'unit_value' => $request->unit_value,
        ]);

        // Log initial stock
        if ($request->quantity > 0) {
            $product->inventoryLogs()->create([
                'user_id' => auth()->id(),
                'type' => 'in',
                'quantity' => $request->quantity,
                'old_quantity' => 0,
                'new_quantity' => $request->quantity,
                'reason' => 'initial_stock',
                'notes' => 'Initial product creation',
            ]);
        }

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {

                // Create folder if it doesn't exist
                $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/img/products';
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Move file to public/img/products
                $image->move($destinationPath, $filename);

                // Save only the relative path
                $product->images()->create([
                    'image' => 'img/products/' . $filename
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        if (auth()->id() !== $product->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $productCategories = ProductCategory::all();
        $productTypes = ProductType::all();

        return view('products.create', compact('product', 'productCategories', 'productTypes'));
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
            'low_stock_threshold' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'product_category_id' => 'required|exists:product_categories,id',
            'product_type_id' => 'required|exists:product_types,id',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'unit_type' => 'required|in:pack,kilo,gram,box,piece',
            'unit_value' => 'required|numeric|min:0.01',
        ]);

        $oldQuantity = $product->quantity;
        $newQuantity = $request->quantity;

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $newQuantity,
            'low_stock_threshold' => $request->low_stock_threshold ?? $product->low_stock_threshold,
            'description' => $request->description,
            'product_category_id' => $request->product_category_id,
            'product_type_id' => $request->product_type_id,
            'unit_type' => $request->unit_type,
            'unit_value' => $request->unit_value,
        ]);

        if ($oldQuantity != $newQuantity) {
            $difference = $newQuantity - $oldQuantity;
            $product->inventoryLogs()->create([
                'user_id' => auth()->id(),
                'type' => $difference > 0 ? 'in' : 'out',
                'quantity' => abs($difference),
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'reason' => 'adjustment',
                'notes' => 'Product update - quantity adjusted',
            ]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {

                // Destination folder: htdocs/img/products
                $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/img/products';

                // Create folder if it doesn't exist
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                // Generate a unique filename
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Move the file to the destination
                $image->move($destinationPath, $filename);

                // Save relative path in database
                $product->images()->create([
                    'image' => 'img/products/' . $filename
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        if (auth()->id() !== $product->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted!');
    }

    public function show(Product $product)
    {
        $product->load('images', 'user', 'reviews', 'category', 'type');
        
        return view('products.show', compact('product'));
    }

    public function landing()
    {
        $heroProducts = Product::inRandomOrder()->take(4)->get();
        return view('landing', compact('heroProducts'));
    }
}