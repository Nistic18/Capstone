<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SupplierReportsController extends Controller
{
    /**
     * Display the supplier reports dashboard
     */
    public function index(Request $request)
{
    if (auth()->user()->role === 'buyer') {
        abort(403, 'Unauthorized access.');
    }

    $supplierId = auth()->id();

    // Get dates from request or default to last year to today
    $dateFrom = $request->input('date_from', Carbon::now()->subYear()->format('Y-m-d'));
    $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));

    $totalProducts = Product::where('user_id', $supplierId)->count();

    $totalDeliveredOrders = Order::whereHas('products', function ($query) use ($supplierId) {
        $query->where('user_id', $supplierId);
    })
    ->where('status', 'Delivered')
    ->whereBetween('created_at', [$dateFrom, $dateTo])
    ->count();

    $totalRevenue = DB::table('order_product')
        ->join('products', 'order_product.product_id', '=', 'products.id')
        ->join('orders', 'order_product.order_id', '=', 'orders.id')
        ->where('products.user_id', $supplierId)
        ->whereIn('orders.status', ['Delivered', 'Completed'])
        ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
        ->where(function ($query) {
            $query->whereNull('orders.refund_status')
                  ->orWhere('orders.refund_status', '!=', 'Approved');
        })
        ->sum(DB::raw('order_product.quantity * products.price'));

    $totalReviews = Review::whereHas('product', function ($query) use ($supplierId) {
        $query->where('user_id', $supplierId);
    })->count();

    $averageRating = Review::whereHas('product', function ($query) use ($supplierId) {
        $query->where('user_id', $supplierId);
    })->avg('rating');

    $currentMonth = Carbon::now();
    $monthlyOrders = Order::whereHas('products', function ($query) use ($supplierId) {
        $query->where('user_id', $supplierId);
    })
    ->whereMonth('created_at', $currentMonth->month)
    ->whereYear('created_at', $currentMonth->year)
    ->where('status', 'Delivered')
    ->count();

    $monthlyRevenue = DB::table('order_product')
        ->join('products', 'order_product.product_id', '=', 'products.id')
        ->join('orders', 'order_product.order_id', '=', 'orders.id')
        ->where('products.user_id', $supplierId)
        ->whereMonth('orders.created_at', $currentMonth->month)
        ->whereYear('orders.created_at', $currentMonth->year)
        ->whereIn('orders.status', ['Delivered', 'Completed'])
        ->where(function ($query) {
            $query->whereNull('orders.refund_status')
                  ->orWhere('orders.refund_status', '!=', 'Approved');
        })
        ->sum(DB::raw('order_product.quantity * products.price'));

    // Determine if there is no data in the selected date range
    $noData = $totalDeliveredOrders == 0 && $totalRevenue == 0;

    return view('supplier.reports.index', compact(
        'totalProducts',
        'totalDeliveredOrders',
        'totalRevenue',
        'totalReviews',
        'averageRating',
        'monthlyOrders',
        'monthlyRevenue',
        'dateFrom',
        'dateTo',
        'noData'
    ));
}

    /**
     * Download Delivered Orders Report
     */
    public function downloadDeliveredOrdersReport(Request $request)
    {   
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,csv,excel'
        ]);

        $format = $request->get('format', 'pdf');
        
        $dateFrom = $request->get('date_from') 
            ? Carbon::parse($request->get('date_from'))->startOfDay() 
            : Carbon::now()->subYear()->startOfDay();
            
        $dateTo = $request->get('date_to') 
            ? Carbon::parse($request->get('date_to'))->endOfDay() 
            : Carbon::now()->endOfDay();

        $supplierId = auth()->id();

        // Query orders based on created_at (order date) not updated_at
        $orders = Order::whereHas('products', function ($query) use ($supplierId) {
                $query->where('user_id', $supplierId);
            })
            ->with(['products' => function ($query) use ($supplierId) {
                $query->where('user_id', $supplierId);
            }, 'user'])
            ->where('status', 'Delivered')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($order) use ($supplierId) {
                $supplierProducts = $order->products->where('user_id', $supplierId);
                $totalAmount = $supplierProducts->sum(fn($p) => $p->pivot->quantity * $p->price);
                
                return [
                    'order_id' => $order->id,
                    'customer_name' => $order->user->name,
                    'customer_email' => $order->user->email,
                    'customer_phone' => $order->user->phone ?? 'N/A',
                    'products_count' => $supplierProducts->count(),
                    'products_list' => $supplierProducts->pluck('name')->implode(', '),
                    'total_quantity' => $supplierProducts->sum('pivot.quantity'),
                    'total_amount' => $totalAmount,
                    'payment_method' => $order->payment_method ?? 'N/A',
                    'order_date' => $order->created_at->format('Y-m-d H:i:s'),
                    'delivery_date' => $order->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        $summary = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_amount'),
            'total_products_sold' => $orders->sum('total_quantity'),
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d')
        ];

        if ($orders->count() === 0) {
            return back()->with('warning', 'No delivered orders found for the selected date range.');
        }

        switch ($format) {
            case 'csv':
                return $this->exportDeliveredOrdersCSV($orders, $summary);
            case 'excel':
                return $this->exportDeliveredOrdersExcel($orders, $summary);
            default:
                return $this->exportDeliveredOrdersPDF($orders, $summary);
        }
    }

    /**
     * Download Products Report
     */
    public function downloadProductsReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $supplierId = auth()->id();

        $products = Product::where('user_id', $supplierId)
            ->with(['reviews'])
            ->withCount('reviews')
            ->get()
            ->map(function($product) {
                $totalSold = DB::table('order_product')
                    ->join('orders', 'order_product.order_id', '=', 'orders.id')
                    ->where('order_product.product_id', $product->id)
                    ->whereIn('orders.status', ['Delivered', 'Completed'])
                    ->where(function ($query) {
                        $query->whereNull('orders.refund_status')
                              ->orWhere('orders.refund_status', '!=', 'Approved');
                    })
                    ->sum('order_product.quantity');

                $totalRevenue = DB::table('order_product')
                    ->join('orders', 'order_product.order_id', '=', 'orders.id')
                    ->where('order_product.product_id', $product->id)
                    ->whereIn('orders.status', ['Delivered', 'Completed'])
                    ->where(function ($query) {
                        $query->whereNull('orders.refund_status')
                              ->orWhere('orders.refund_status', '!=', 'Approved');
                    })
                    ->sum(DB::raw('order_product.quantity * ' . $product->price));

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name ?? 'N/A',
                    'price' => $product->price,
                    'current_stock' => $product->quantity,
                    'total_sold' => $totalSold ?? 0,
                    'total_revenue' => $totalRevenue ?? 0,
                    'average_rating' => $product->reviews->avg('rating') ?? 0,
                    'total_reviews' => $product->reviews_count,
                    'status' => $product->quantity > 0 ? 'In Stock' : 'Out of Stock',
                    'created_date' => $product->created_at->format('Y-m-d')
                ];
            });

        switch ($format) {
            case 'csv':
                return $this->exportProductsCSV($products);
            case 'excel':
                return $this->exportProductsExcel($products);
            default:
                return $this->exportProductsPDF($products);
        }
    }

    /**
     * Download Product Inventory Report
     */
    public function downloadInventoryReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $supplierId = auth()->id();

        $products = Product::where('user_id', $supplierId)
            ->get()
            ->map(function($product) {
                $totalSold = DB::table('order_product')
                    ->join('orders', 'order_product.order_id', '=', 'orders.id')
                    ->where('order_product.product_id', $product->id)
                    ->whereIn('orders.status', ['Delivered', 'Completed'])
                    ->where(function ($query) {
                        $query->whereNull('orders.refund_status')
                              ->orWhere('orders.refund_status', '!=', 'Approved');
                    })
                    ->sum('order_product.quantity');

                $pendingOrders = DB::table('order_product')
                    ->join('orders', 'order_product.order_id', '=', 'orders.id')
                    ->where('order_product.product_id', $product->id)
                    ->whereIn('orders.status', ['Pending', 'Processing', 'Shipped'])
                    ->sum('order_product.quantity');

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku ?? 'N/A',
                    'category' => $product->category ? $product->category->name : 'N/A',
                    'current_stock' => $product->quantity,
                    'total_sold' => $totalSold ?? 0,
                    'pending_orders' => $pendingOrders ?? 0,
                    'available_stock' => max(0, $product->quantity - ($pendingOrders ?? 0)),
                    'stock_value' => $product->quantity * $product->price,
                    'status' => $product->quantity > 10 ? 'Good Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock'),
                    'reorder_needed' => $product->quantity <= 10 ? 'Yes' : 'No',
                    'last_updated' => $product->updated_at->format('Y-m-d H:i:s')
                ];
            });

        $summary = [
            'total_products' => $products->count(),
            'total_stock_value' => $products->sum('stock_value'),
            'in_stock' => $products->where('current_stock', '>', 0)->count(),
            'out_of_stock' => $products->where('current_stock', '<=', 0)->count(),
            'low_stock' => $products->where('reorder_needed', 'Yes')->count()
        ];

        switch ($format) {
            case 'csv':
                return $this->exportInventoryCSV($products, $summary);
            case 'excel':
                return $this->exportInventoryExcel($products, $summary);
            default:
                return $this->exportInventoryPDF($products, $summary);
        }
    }

    /**
     * Download Sales Revenue Report
     */
    public function downloadSalesRevenueReport(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,csv,excel'
        ]);

        $format = $request->get('format', 'pdf');
        
        $dateFrom = $request->get('date_from') 
            ? Carbon::parse($request->get('date_from'))->startOfDay() 
            : Carbon::now()->subYear()->startOfDay();
            
        $dateTo = $request->get('date_to') 
            ? Carbon::parse($request->get('date_to'))->endOfDay() 
            : Carbon::now()->endOfDay();

        $supplierId = auth()->id();

        // Monthly breakdown
        $monthlySales = [];
        $currentDate = $dateFrom->copy()->startOfMonth();
        $endDate = $dateTo->copy()->endOfMonth();
        
        while ($currentDate <= $endDate) {
            $revenue = DB::table('order_product')
                ->join('products', 'order_product.product_id', '=', 'products.id')
                ->join('orders', 'order_product.order_id', '=', 'orders.id')
                ->where('products.user_id', $supplierId)
                ->whereYear('orders.created_at', $currentDate->year)
                ->whereMonth('orders.created_at', $currentDate->month)
                ->whereIn('orders.status', ['Delivered', 'Completed'])
                ->where(function ($query) {
                    $query->whereNull('orders.refund_status')
                          ->orWhere('orders.refund_status', '!=', 'Approved');
                })
                ->sum(DB::raw('order_product.quantity * products.price'));

            $orders = Order::whereHas('products', function ($query) use ($supplierId) {
                    $query->where('user_id', $supplierId);
                })
                ->whereYear('created_at', $currentDate->year)
                ->whereMonth('created_at', $currentDate->month)
                ->whereIn('status', ['Delivered', 'Completed'])
                ->count();

            $itemsSold = DB::table('order_product')
                ->join('products', 'order_product.product_id', '=', 'products.id')
                ->join('orders', 'order_product.order_id', '=', 'orders.id')
                ->where('products.user_id', $supplierId)
                ->whereYear('orders.created_at', $currentDate->year)
                ->whereMonth('orders.created_at', $currentDate->month)
                ->whereIn('orders.status', ['Delivered', 'Completed'])
                ->sum('order_product.quantity');

            $monthlySales[] = [
                'month' => $currentDate->format('M Y'),
                'revenue' => $revenue ?? 0,
                'orders' => $orders,
                'items_sold' => $itemsSold ?? 0,
                'average_order_value' => $orders > 0 ? ($revenue / $orders) : 0
            ];

            $currentDate->addMonth();
        }

        // Top selling products
        $topProducts = DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->where('products.user_id', $supplierId)
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->whereIn('orders.status', ['Delivered', 'Completed'])
            ->where(function ($query) {
                $query->whereNull('orders.refund_status')
                      ->orWhere('orders.refund_status', '!=', 'Approved');
            })
            ->select(
                'products.name as product_name',
                DB::raw('SUM(order_product.quantity) as total_quantity'),
                DB::raw('SUM(order_product.quantity * products.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $summary = [
            'total_revenue' => collect($monthlySales)->sum('revenue'),
            'total_orders' => collect($monthlySales)->sum('orders'),
            'total_items_sold' => collect($monthlySales)->sum('items_sold'),
            'average_order_value' => collect($monthlySales)->where('average_order_value', '>', 0)->avg('average_order_value') ?? 0,
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d')
        ];

        if ($summary['total_orders'] === 0) {
            return back()->with('warning', 'No sales data found for the selected date range.');
        }

        switch ($format) {
            case 'csv':
                return $this->exportSalesRevenueCSV($monthlySales, $topProducts, $summary);
            case 'excel':
                return $this->exportSalesRevenueExcel($monthlySales, $topProducts, $summary);
            default:
                return $this->exportSalesRevenuePDF($monthlySales, $topProducts, $summary);
        }
    }

    /**
     * Download Customer Feedback Report
     */
    public function downloadFeedbackReport(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,csv,excel'
        ]);

        $format = $request->get('format', 'pdf');
        
        $dateFrom = $request->get('date_from') 
            ? Carbon::parse($request->get('date_from'))->startOfDay() 
            : Carbon::now()->subYear()->startOfDay();
            
        $dateTo = $request->get('date_to') 
            ? Carbon::parse($request->get('date_to'))->endOfDay() 
            : Carbon::now()->endOfDay();

        $supplierId = auth()->id();

        $reviews = Review::whereHas('product', function ($query) use ($supplierId) {
                $query->where('user_id', $supplierId);
            })
            ->with(['user', 'product'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($review) {
                return [
                    'review_id' => $review->id,
                    'product_name' => $review->product->name,
                    'customer_name' => $review->user->name,
                    'customer_email' => $review->user->email,
                    'rating' => $review->rating,
                    'comment' => $review->comment ?? 'No comment',
                    'review_date' => $review->created_at->format('Y-m-d H:i:s')
                ];
            });

        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;
        $ratingDistribution = [
            '5_star' => $reviews->where('rating', 5)->count(),
            '4_star' => $reviews->where('rating', 4)->count(),
            '3_star' => $reviews->where('rating', 3)->count(),
            '2_star' => $reviews->where('rating', 2)->count(),
            '1_star' => $reviews->where('rating', 1)->count(),
        ];

        $summary = [
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 2),
            'rating_distribution' => $ratingDistribution,
            'positive_reviews' => $reviews->whereIn('rating', [4, 5])->count(),
            'negative_reviews' => $reviews->whereIn('rating', [1, 2])->count(),
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d')
        ];

        if ($reviews->count() === 0) {
            return back()->with('warning', 'No customer reviews found for the selected date range.');
        }

        switch ($format) {
            case 'csv':
                return $this->exportFeedbackCSV($reviews, $summary);
            case 'excel':
                return $this->exportFeedbackExcel($reviews, $summary);
            default:
                return $this->exportFeedbackPDF($reviews, $summary);
        }
    }

    // ==================== CSV EXPORT METHODS ====================
    
    private function exportDeliveredOrdersCSV($orders, $summary)
    {
        $filename = 'delivered_orders_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($orders, $summary) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Delivered Orders Report']);
            fputcsv($file, ['Period:', $summary['date_from'] . ' to ' . $summary['date_to']]);
            fputcsv($file, ['Total Orders:', $summary['total_orders']]);
            fputcsv($file, ['Total Revenue:', '₱' . number_format($summary['total_revenue'], 2)]);
            fputcsv($file, ['Total Products Sold:', $summary['total_products_sold']]);
            fputcsv($file, []);
            
            fputcsv($file, ['Order ID', 'Customer Name', 'Email', 'Phone', 'Products', 'Products List', 'Total Qty', 'Total Amount', 'Payment Method', 'Order Date', 'Delivery Date']);
            
            foreach($orders as $order) {
                fputcsv($file, [
                    $order['order_id'],
                    $order['customer_name'],
                    $order['customer_email'],
                    $order['customer_phone'],
                    $order['products_count'],
                    $order['products_list'],
                    $order['total_quantity'],
                    number_format($order['total_amount'], 2),
                    $order['payment_method'],
                    $order['order_date'],
                    $order['delivery_date']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportProductsCSV($products)
    {
        $filename = 'products_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Products Report']);
            fputcsv($file, ['Generated:', date('Y-m-d H:i:s')]);
            fputcsv($file, []);
            
            fputcsv($file, ['ID', 'Product Name', 'Category', 'Price', 'Current Stock', 'Total Sold', 'Total Revenue', 'Avg Rating', 'Total Reviews', 'Status', 'Created Date']);
            
            foreach($products as $product) {
                fputcsv($file, [
                    $product['id'],
                    $product['name'],
                    $product['category'],
                    number_format($product['price'], 2),
                    $product['current_stock'],
                    $product['total_sold'],
                    number_format($product['total_revenue'], 2),
                    number_format($product['average_rating'], 2),
                    $product['total_reviews'],
                    $product['status'],
                    $product['created_date']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportInventoryCSV($products, $summary)
    {
        $filename = 'inventory_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($products, $summary) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Product Inventory Report']);
            fputcsv($file, ['Generated:', date('Y-m-d H:i:s')]);
            fputcsv($file, ['Total Products:', $summary['total_products']]);
            fputcsv($file, ['Total Stock Value:', '₱' . number_format($summary['total_stock_value'], 2)]);
            fputcsv($file, ['In Stock:', $summary['in_stock']]);
            fputcsv($file, ['Out of Stock:', $summary['out_of_stock']]);
            fputcsv($file, ['Low Stock:', $summary['low_stock']]);
            fputcsv($file, []);
            
            fputcsv($file, ['ID', 'Product Name', 'SKU', 'Category', 'Current Stock', 'Total Sold', 'Pending Orders', 'Available Stock', 'Stock Value', 'Status', 'Reorder Needed', 'Last Updated']);
            
            foreach($products as $product) {
                fputcsv($file, [
                    $product['id'],
                    $product['name'],
                    $product['sku'],
                    $product['category'],
                    $product['current_stock'],
                    $product['total_sold'],
                    $product['pending_orders'],
                    $product['available_stock'],
                    number_format($product['stock_value'], 2),
                    $product['status'],
                    $product['reorder_needed'],
                    $product['last_updated']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportSalesRevenueCSV($monthlySales, $topProducts, $summary)
    {
        $filename = 'sales_revenue_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($monthlySales, $topProducts, $summary) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Sales Revenue Report']);
            fputcsv($file, ['Period:', $summary['date_from'] . ' to ' . $summary['date_to']]);
            fputcsv($file, ['Total Revenue:', '₱' . number_format($summary['total_revenue'], 2)]);
            fputcsv($file, ['Total Orders:', $summary['total_orders']]);
            fputcsv($file, ['Total Items Sold:', $summary['total_items_sold']]);
            fputcsv($file, ['Average Order Value:', '₱' . number_format($summary['average_order_value'], 2)]);
            fputcsv($file, []);
            
            fputcsv($file, ['Monthly Breakdown']);
            fputcsv($file, ['Month', 'Revenue', 'Orders', 'Items Sold', 'Avg Order Value']);
            
            foreach($monthlySales as $month) {
                fputcsv($file, [
                    $month['month'],
                    number_format($month['revenue'], 2),
                    $month['orders'],
                    $month['items_sold'],
                    number_format($month['average_order_value'], 2)
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ['Top Selling Products']);
            fputcsv($file, ['Product Name', 'Total Quantity', 'Total Revenue']);
            
            foreach($topProducts as $product) {
                fputcsv($file, [
                    $product->product_name,
                    $product->total_quantity,
                    number_format($product->total_revenue, 2)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportFeedbackCSV($reviews, $summary)
    {
        $filename = 'customer_feedback_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($reviews, $summary) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Customer Feedback Report']);
            fputcsv($file, ['Period:', $summary['date_from'] . ' to ' . $summary['date_to']]);
            fputcsv($file, ['Total Reviews:', $summary['total_reviews']]);
            fputcsv($file, ['Average Rating:', $summary['average_rating']]);
            fputcsv($file, ['Positive Reviews (4-5 stars):', $summary['positive_reviews']]);
            fputcsv($file, ['Negative Reviews (1-2 stars):', $summary['negative_reviews']]);
            fputcsv($file, []);
            fputcsv($file, ['Rating Distribution:']);
            fputcsv($file, ['5 Stars:', $summary['rating_distribution']['5_star']]);
            fputcsv($file, ['4 Stars:', $summary['rating_distribution']['4_star']]);
            fputcsv($file, ['3 Stars:', $summary['rating_distribution']['3_star']]);
            fputcsv($file, ['2 Stars:', $summary['rating_distribution']['2_star']]);
            fputcsv($file, ['1 Star:', $summary['rating_distribution']['1_star']]);
            fputcsv($file, []);
            
            fputcsv($file, ['Review ID', 'Product Name', 'Customer Name', 'Email', 'Rating', 'Comment', 'Review Date']);
            
            foreach($reviews as $review) {
                fputcsv($file, [
                    $review['review_id'],
                    $review['product_name'],
                    $review['customer_name'],
                    $review['customer_email'],
                    $review['rating'],
                    $review['comment'],
                    $review['review_date']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==================== EXCEL EXPORT METHODS ====================
    
    private function exportDeliveredOrdersExcel($orders, $summary)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title
        $sheet->setCellValue('A1', 'Delivered Orders Report');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        
        // Summary
        $sheet->setCellValue('A2', 'Period:');
        $sheet->setCellValue('B2', $summary['date_from'] . ' to ' . $summary['date_to']);
        $sheet->setCellValue('A3', 'Total Orders:');
        $sheet->setCellValue('B3', $summary['total_orders']);
        $sheet->setCellValue('A4', 'Total Revenue:');
        $sheet->setCellValue('B4', '₱' . number_format($summary['total_revenue'], 2));
        $sheet->setCellValue('A5', 'Total Products Sold:');
        $sheet->setCellValue('B5', $summary['total_products_sold']);
        
        // Headers
        $row = 7;
        $headers = ['Order ID', 'Customer Name', 'Email', 'Phone', 'Products', 'Products List', 'Total Qty', 'Total Amount', 'Payment Method', 'Order Date', 'Delivery Date'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }
        
        // Data
        $row = 8;
        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, $order['order_id']);
            $sheet->setCellValue('B' . $row, $order['customer_name']);
            $sheet->setCellValue('C' . $row, $order['customer_email']);
            $sheet->setCellValue('D' . $row, $order['customer_phone']);
            $sheet->setCellValue('E' . $row, $order['products_count']);
            $sheet->setCellValue('F' . $row, $order['products_list']);
            $sheet->setCellValue('G' . $row, $order['total_quantity']);
            $sheet->setCellValue('H' . $row, number_format($order['total_amount'], 2));
            $sheet->setCellValue('I' . $row, $order['payment_method']);
            $sheet->setCellValue('J' . $row, $order['order_date']);
            $sheet->setCellValue('K' . $row, $order['delivery_date']);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'delivered_orders_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
    
    private function exportProductsExcel($products)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Products Report');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        
        $sheet->setCellValue('A2', 'Generated:');
        $sheet->setCellValue('B2', date('Y-m-d H:i:s'));
        
        $row = 4;
        $headers = ['ID', 'Product Name', 'Category', 'Price', 'Current Stock', 'Total Sold', 'Total Revenue', 'Avg Rating', 'Total Reviews', 'Status', 'Created Date'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }
        
        $row = 5;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product['id']);
            $sheet->setCellValue('B' . $row, $product['name']);
            $sheet->setCellValue('C' . $row, $product['category']);
            $sheet->setCellValue('D' . $row, number_format($product['price'], 2));
            $sheet->setCellValue('E' . $row, $product['current_stock']);
            $sheet->setCellValue('F' . $row, $product['total_sold']);
            $sheet->setCellValue('G' . $row, number_format($product['total_revenue'], 2));
            $sheet->setCellValue('H' . $row, number_format($product['average_rating'], 2));
            $sheet->setCellValue('I' . $row, $product['total_reviews']);
            $sheet->setCellValue('J' . $row, $product['status']);
            $sheet->setCellValue('K' . $row, $product['created_date']);
            $row++;
        }
        
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'products_report_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
    
    private function exportInventoryExcel($products, $summary)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Product Inventory Report');
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        
        $sheet->setCellValue('A2', 'Generated:');
        $sheet->setCellValue('B2', date('Y-m-d H:i:s'));
        $sheet->setCellValue('A3', 'Total Products:');
        $sheet->setCellValue('B3', $summary['total_products']);
        $sheet->setCellValue('A4', 'Total Stock Value:');
        $sheet->setCellValue('B4', '₱' . number_format($summary['total_stock_value'], 2));
        $sheet->setCellValue('A5', 'In Stock:');
        $sheet->setCellValue('B5', $summary['in_stock']);
        $sheet->setCellValue('D5', 'Out of Stock:');
        $sheet->setCellValue('E5', $summary['out_of_stock']);
        $sheet->setCellValue('G5', 'Low Stock:');
        $sheet->setCellValue('H5', $summary['low_stock']);
        
        $row = 7;
        $headers = ['ID', 'Product Name', 'SKU', 'Category', 'Current Stock', 'Total Sold', 'Pending Orders', 'Available Stock', 'Stock Value', 'Status', 'Reorder Needed', 'Last Updated'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }
        
        $row = 8;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product['id']);
            $sheet->setCellValue('B' . $row, $product['name']);
            $sheet->setCellValue('C' . $row, $product['sku']);
            $sheet->setCellValue('D' . $row, $product['category']);
            $sheet->setCellValue('E' . $row, $product['current_stock']);
            $sheet->setCellValue('F' . $row, $product['total_sold']);
            $sheet->setCellValue('G' . $row, $product['pending_orders']);
            $sheet->setCellValue('H' . $row, $product['available_stock']);
            $sheet->setCellValue('I' . $row, number_format($product['stock_value'], 2));
            $sheet->setCellValue('J' . $row, $product['status']);
            $sheet->setCellValue('K' . $row, $product['reorder_needed']);
            $sheet->setCellValue('L' . $row, $product['last_updated']);
            $row++;
        }
        
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'inventory_report_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
    
    private function exportSalesRevenueExcel($monthlySales, $topProducts, $summary)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Sales Revenue Report');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        
        $sheet->setCellValue('A2', 'Period:');
        $sheet->setCellValue('B2', $summary['date_from'] . ' to ' . $summary['date_to']);
        $sheet->setCellValue('A3', 'Total Revenue:');
        $sheet->setCellValue('B3', '₱' . number_format($summary['total_revenue'], 2));
        $sheet->setCellValue('A4', 'Total Orders:');
        $sheet->setCellValue('B4', $summary['total_orders']);
        $sheet->setCellValue('A5', 'Total Items Sold:');
        $sheet->setCellValue('B5', $summary['total_items_sold']);
        $sheet->setCellValue('A6', 'Average Order Value:');
        $sheet->setCellValue('B6', '₱' . number_format($summary['average_order_value'], 2));
        
        $row = 8;
        $sheet->setCellValue('A' . $row, 'Monthly Breakdown');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        
        $row = 9;
        $headers = ['Month', 'Revenue', 'Orders', 'Items Sold', 'Avg Order Value'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }
        
        $row = 10;
        foreach ($monthlySales as $month) {
            $sheet->setCellValue('A' . $row, $month['month']);
            $sheet->setCellValue('B' . $row, number_format($month['revenue'], 2));
            $sheet->setCellValue('C' . $row, $month['orders']);
            $sheet->setCellValue('D' . $row, $month['items_sold']);
            $sheet->setCellValue('E' . $row, number_format($month['average_order_value'], 2));
            $row++;
        }
        
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Top Selling Products');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Product Name');
        $sheet->setCellValue('B' . $row, 'Total Quantity');
        $sheet->setCellValue('C' . $row, 'Total Revenue');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        
        $row++;
        foreach ($topProducts as $product) {
            $sheet->setCellValue('A' . $row, $product->product_name);
            $sheet->setCellValue('B' . $row, $product->total_quantity);
            $sheet->setCellValue('C' . $row, number_format($product->total_revenue, 2));
            $row++;
        }
        
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'sales_revenue_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
    
    private function exportFeedbackExcel($reviews, $summary)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Customer Feedback Report');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        
        $sheet->setCellValue('A2', 'Period:');
        $sheet->setCellValue('B2', $summary['date_from'] . ' to ' . $summary['date_to']);
        $sheet->setCellValue('A3', 'Total Reviews:');
        $sheet->setCellValue('B3', $summary['total_reviews']);
        $sheet->setCellValue('A4', 'Average Rating:');
        $sheet->setCellValue('B4', $summary['average_rating']);
        $sheet->setCellValue('A5', 'Positive Reviews (4-5 stars):');
        $sheet->setCellValue('B5', $summary['positive_reviews']);
        $sheet->setCellValue('A6', 'Negative Reviews (1-2 stars):');
        $sheet->setCellValue('B6', $summary['negative_reviews']);
        
        $row = 8;
        $sheet->setCellValue('A' . $row, 'Rating Distribution:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, '5 Stars:');
        $sheet->setCellValue('B' . $row, $summary['rating_distribution']['5_star']);
        $row++;
        $sheet->setCellValue('A' . $row, '4 Stars:');
        $sheet->setCellValue('B' . $row, $summary['rating_distribution']['4_star']);
        $row++;
        $sheet->setCellValue('A' . $row, '3 Stars:');
        $sheet->setCellValue('B' . $row, $summary['rating_distribution']['3_star']);
        $row++;
        $sheet->setCellValue('A' . $row, '2 Stars:');
        $sheet->setCellValue('B' . $row, $summary['rating_distribution']['2_star']);
        $row++;
        $sheet->setCellValue('A' . $row, '1 Star:');
        $sheet->setCellValue('B' . $row, $summary['rating_distribution']['1_star']);
        
        $row += 2;
        $headers = ['Review ID', 'Product Name', 'Customer Name', 'Email', 'Rating', 'Comment', 'Review Date'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }

        $row++;
        foreach ($reviews as $review) {
            $sheet->setCellValue('A' . $row, $review['review_id']);
            $sheet->setCellValue('B' . $row, $review['product_name']);
            $sheet->setCellValue('C' . $row, $review['customer_name']);
            $sheet->setCellValue('D' . $row, $review['customer_email']);
            $sheet->setCellValue('E' . $row, $review['rating']);
            $sheet->setCellValue('F' . $row, $review['comment']);
            $sheet->setCellValue('G' . $row, $review['review_date']);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'customer_feedback_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // ==================== PDF EXPORT METHODS ====================
    
    private function exportDeliveredOrdersPDF($orders, $summary)
{
    $pdf = PDF::loadView('supplier.reports.pdf.delivered-orders', [
        'title' => 'Delivered Orders Report',
        'date' => now()->format('F d, Y h:i A'),
        'supplier' => auth()->user()->name,
        'orders' => $orders,
        'summary' => $summary
    ]);

    return $pdf->download('delivered_orders_' . now()->format('Y-m-d') . '.pdf');
}

    private function exportProductsPDF($products)
{
    // Ensure $products already contains total_sold, total_revenue, etc. (mapped version)
    $data = [
        'title' => 'Products Report',
        'date' => date('Y-m-d H:i:s'),
        'products' => $products,
        'supplier' => auth()->user()->name
    ];

    $pdf = PDF::loadView('supplier.reports.pdf.products', $data);
    $pdf->setPaper('a4', 'landscape');
    return $pdf->download('products_report_' . date('Y-m-d') . '.pdf');
}

    private function exportInventoryPDF($products, $summary)
    {
        $data = [
            'title' => 'Product Inventory Report',
            'date' => date('Y-m-d H:i:s'),
            'products' => $products,
            'summary' => $summary,
            'supplier' => auth()->user()->name
        ];

        $pdf = PDF::loadView('supplier.reports.pdf.inventory', $data);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('inventory_report_' . date('Y-m-d') . '.pdf');
    }

    private function exportSalesRevenuePDF($monthlySales, $topProducts, $summary)
    {
        $data = [
            'title' => 'Sales Revenue Report',
            'date' => date('Y-m-d H:i:s'),
            'monthlySales' => $monthlySales,
            'topProducts' => $topProducts,
            'summary' => $summary,
            'supplier' => auth()->user()->name
        ];

        $pdf = PDF::loadView('supplier.reports.pdf.sales-revenue', $data);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('sales_revenue_' . date('Y-m-d') . '.pdf');
    }

    private function exportFeedbackPDF($reviews, $summary)
    {
        $data = [
            'title' => 'Customer Feedback Report',
            'date' => date('Y-m-d H:i:s'),
            'reviews' => $reviews,
            'summary' => $summary,
            'supplier' => auth()->user()->name
        ];

        $pdf = PDF::loadView('supplier.reports.pdf.feedback', $data);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('customer_feedback_' . date('Y-m-d') . '.pdf');
    }
}