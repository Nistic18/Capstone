<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display inventory management dashboard
     */
    public function index(Request $request)
    {
        // Only allow sellers or admins
        if (auth()->user()->role === 'buyer') {
            abort(403, 'Unauthorized access.');
        }

        $query = Product::where('user_id', auth()->id());

        // Filter by stock status
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
                case 'low_stock':
                    $query->whereRaw('quantity > 0 AND quantity <= low_stock_threshold');
                    break;
                case 'in_stock':
                    $query->whereRaw('quantity > low_stock_threshold');
                    break;
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        switch ($request->sort) {
            case 'quantity_asc':
                $query->orderBy('quantity', 'asc');
                break;
            case 'quantity_desc':
                $query->orderBy('quantity', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('quantity', 'asc'); // Show low stock first
        }

        $products = $query->paginate(12);

        // Get inventory statistics
        $stats = [
            'total_products' => Product::where('user_id', auth()->id())->count(),
            'out_of_stock' => Product::where('user_id', auth()->id())->where('quantity', '<=', 0)->count(),
            'low_stock' => Product::where('user_id', auth()->id())
                ->whereRaw('quantity > 0 AND quantity <= low_stock_threshold')
                ->count(),
            'total_value' => Product::where('user_id', auth()->id())
                ->selectRaw('SUM(price * quantity) as total')
                ->value('total') ?? 0,
        ];

        return view('inventory.index', compact('products', 'stats'));
    }

    /**
     * Adjust product inventory
     */
    public function adjust(Request $request, Product $product)
    {
        // Authorization check
        if (auth()->id() !== $product->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        // Validate input
        $request->validate([
            'action' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'notes' => 'nullable|string|max:500',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        // Update low stock threshold if provided
        if ($request->filled('low_stock_threshold')) {
            $product->update([
                'low_stock_threshold' => $request->low_stock_threshold,
            ]);
        }

        // Perform inventory adjustment
        switch ($request->action) {
            case 'add':
                $product->addStock($request->quantity, $request->reason, $request->notes);
                $message = 'Stock added successfully! New quantity: ' . $product->quantity;
                break;
            case 'remove':
                $product->removeStock($request->quantity, $request->reason, $request->notes);
                $message = 'Stock removed successfully! New quantity: ' . $product->quantity;
                break;
            case 'set':
                $product->setStock($request->quantity, $request->reason, $request->notes);
                $message = 'Stock updated successfully! New quantity: ' . $product->quantity;
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Update low stock threshold only
     */
    public function updateThreshold(Request $request, Product $product)
    {
        // Authorization check
        if (auth()->id() !== $product->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        $product->update([
            'low_stock_threshold' => $request->low_stock_threshold,
        ]);

        return back()->with('success', 'Low stock threshold updated to ' . $request->low_stock_threshold . ' units!');
    }

    /**
     * Display inventory history for a product
     */
    public function history(Product $product)
    {
        // Authorization check
        if (auth()->id() !== $product->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        // Get inventory logs with user information
        $logs = $product->inventoryLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('inventory.history', compact('product', 'logs'));
    }

    /**
     * Bulk adjust multiple products at once
     */
    public function bulkAdjust(Request $request)
    {
        // Only allow sellers or admins
        if (auth()->user()->role === 'buyer') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:0',
            'reason' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $updated = 0;

        foreach ($request->products as $productData) {
            $product = Product::findOrFail($productData['id']);
            
            // Check authorization
            if ($product->user_id === auth()->id() || auth()->user()->role === 'admin') {
                $product->setStock($productData['quantity'], $request->reason, $request->notes);
                $updated++;
            }
        }

        return back()->with('success', "Bulk inventory adjustment completed! Updated {$updated} products.");
    }

    /**
     * Get low stock products (for alerts/notifications)
     */
    public function getLowStockProducts()
    {
        // Only allow sellers or admins
        if (auth()->user()->role === 'buyer') {
            abort(403, 'Unauthorized access.');
        }

        $lowStockProducts = Product::where('user_id', auth()->id())
            ->whereRaw('quantity > 0 AND quantity <= low_stock_threshold')
            ->get();

        return response()->json([
            'count' => $lowStockProducts->count(),
            'products' => $lowStockProducts,
        ]);
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts()
    {
        // Only allow sellers or admins
        if (auth()->user()->role === 'buyer') {
            abort(403, 'Unauthorized access.');
        }

        $outOfStockProducts = Product::where('user_id', auth()->id())
            ->where('quantity', '<=', 0)
            ->get();

        return response()->json([
            'count' => $outOfStockProducts->count(),
            'products' => $outOfStockProducts,
        ]);
    }

    /**
     * Export inventory report (CSV)
     */
    public function exportReport()
    {
        // Only allow sellers or admins
        if (auth()->user()->role === 'buyer') {
            abort(403, 'Unauthorized access.');
        }

        $products = Product::where('user_id', auth()->id())->get();

        $filename = 'inventory_report_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Product ID',
                'Product Name',
                'Current Stock',
                'Low Stock Threshold',
                'Status',
                'Price',
                'Total Value',
            ]);

            // Data rows
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->quantity,
                    $product->low_stock_threshold,
                    $product->getStockStatus(),
                    $product->price,
                    $product->price * $product->quantity,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}