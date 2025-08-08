<?php

namespace App\Http\Controllers;

use App\Models\SupplierProduct;
use Illuminate\Http\Request;

class SupplierProductController extends Controller
{
    public function home()
    {
        $products = SupplierProduct::latest()->paginate(9);
        return view('home', compact('products'));
    }

    public function index()
    {
        if (auth()->user()->role === 'admin') {
            $products = SupplierProduct::with('user')->get();
        } else {
            $products = SupplierProduct::with('user')->where('user_id', auth()->id())->get();
        }
        return view('supplierproduct.index', compact('products'));
    }

    public function create()
    {
        return view('supplierproduct.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        SupplierProduct::create($data);

        return redirect()->route('supplierproduct.index')->with('success', 'Product created!');
    }

    public function show(SupplierProduct $supplierproduct)
    {
        return view('supplierproduct.show', ['product' => $supplierproduct]);
    }

    public function edit(SupplierProduct $supplierproduct)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        return view('supplierproduct.create', ['product' => $supplierproduct]);
    }

    public function update(Request $request, SupplierProduct $supplierproduct)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only('name', 'description', 'price', 'quantity');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $supplierproduct->update($data);

        return redirect()->route('supplierproduct.index')->with('success', 'Product updated!');
    }

    public function destroy(SupplierProduct $supplierproduct)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $supplierproduct->delete();
        return redirect()->route('supplierproduct.index')->with('success', 'Product deleted!');
    }
}
