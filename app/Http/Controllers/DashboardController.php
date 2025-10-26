<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Only allow sellers or admins
        if (auth()->user()->role === 'buyer') {
            abort(403, 'Unauthorized access.');
        }

        $userId = auth()->id();
        
        // Get current date information
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();
        $currentYear = Carbon::now()->year;
        
        // 1. TOTAL SALES & REVENUE METRICS
        $totalRevenue = $this->getTotalRevenue($userId);
        $monthlyRevenue = $this->getMonthlyRevenue($userId, $currentMonth);
        $previousMonthRevenue = $this->getMonthlyRevenue($userId, $previousMonth);
        $revenueGrowth = $this->calculateGrowthPercentage($monthlyRevenue, $previousMonthRevenue);
        
        // 2. ORDER STATISTICS
        $totalOrders = $this->getTotalOrders($userId);
        $monthlyOrders = $this->getMonthlyOrders($userId, $currentMonth);
        $previousMonthOrders = $this->getMonthlyOrders($userId, $previousMonth);
        $orderGrowth = $this->calculateGrowthPercentage($monthlyOrders, $previousMonthOrders);
        
        // 3. PRODUCT STATISTICS
        $totalProducts = Product::where('user_id', $userId)->count();
        $activeProducts = Product::where('user_id', $userId)->where('quantity', '>', 0)->count();
        $outOfStockProducts = Product::where('user_id', $userId)->where('quantity', 0)->count();
        
        // 4. MONTHLY SALES CHART DATA (Last 12 months)
        $monthlySalesData = $this->getMonthlySalesData($userId);
        
        // 5. TOP SELLING PRODUCTS (Last 30 days)
        $topProducts = $this->getTopSellingProducts($userId, 30);
        
        // 6. RECENT ORDERS
        $recentOrders = $this->getRecentOrders($userId, 10);
        
        // 7. ORDER STATUS DISTRIBUTION
        $orderStatusData = $this->getOrderStatusDistribution($userId);
        
        // 8. DAILY SALES (Last 30 days)
        $dailySalesData = $this->getDailySalesData($userId, 30);
        
        // 9. AVERAGE ORDER VALUE
        $averageOrderValue = $this->getAverageOrderValue($userId);
        $monthlyAOV = $this->getMonthlyAverageOrderValue($userId, $currentMonth);
        
        // 10. PRODUCT PERFORMANCE METRICS
        $productPerformance = $this->getProductPerformanceMetrics($userId);

        $totalRefunds = $this->getTotalRefunds($userId);
        $monthlyRefunds = $this->getMonthlyRefunds($userId, $currentMonth);
        $refundRate = $this->getRefundRate($userId);
        $monthlyRefundData = $this->getMonthlyRefundData($userId);


        return view('dashboard.supplier', compact(
            'totalRevenue',
            'monthlyRevenue', 
            'revenueGrowth',
            'totalOrders',
            'monthlyOrders',
            'orderGrowth',
            'totalProducts',
            'activeProducts',
            'outOfStockProducts',
            'monthlySalesData',
            'topProducts',
            'recentOrders',
            'orderStatusData',
            'dailySalesData',
            'averageOrderValue',
            'monthlyAOV',
            'productPerformance',
            'totalRefunds',
            'monthlyRefunds',
            'refundRate',
            'monthlyRefundData'
        ));
    }

    private function getTotalRevenue($userId)
    {
        return DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->where('products.user_id', $userId)
            ->whereIn('orders.status', ['Delivered', 'Completed'])
            ->where(function ($query) {
            $query->whereNull('orders.refund_status')
                  ->orWhere('orders.refund_status', '!=', 'Approved');
            })
            ->sum(DB::raw('order_product.quantity * products.price'));
    }

    private function getMonthlyRevenue($userId, $month)
{
    $orderIds = DB::table('order_product')
        ->join('products', 'order_product.product_id', '=', 'products.id')
        ->where('products.user_id', $userId)
        ->pluck('order_product.order_id')
        ->unique();

    return DB::table('orders')
        ->whereIn('id', $orderIds)
        ->whereIn('status', ['Delivered', 'Completed'])
        ->whereYear('created_at', $month->year)
        ->whereMonth('created_at', $month->month)
        ->where(function ($query) {
            $query->whereNull('refund_status')
                  ->orWhere('refund_status', '!=', 'Approved');
        })
        ->sum('total_price');
}

    private function getTotalOrders($userId)
    {
        return Order::whereHas('products', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->count();
    }

    private function getMonthlyOrders($userId, $month)
    {
        return Order::whereHas('products', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereYear('created_at', $month->year)
        ->whereMonth('created_at', $month->month)
        ->where(function ($query) {
            $query->whereNull('refund_status')
                  ->orWhere('refund_status', '!=', 'Approved');
        })
        ->count();
    }

    private function getMonthlySalesData($userId)
{
    $data = [];
    for ($i = 11; $i >= 0; $i--) {
        $date = Carbon::now()->subMonths($i);
        
        $revenue = DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->where('products.user_id', $userId)
            ->whereIn('orders.status', ['Delivered', 'Completed'])
            ->whereYear('orders.created_at', $date->year)
            ->whereMonth('orders.created_at', $date->month)
            ->where(function ($query) {
                $query->whereNull('orders.refund_status')
                      ->orWhere('orders.refund_status', '!=', 'Approved');
            })
            ->sum(DB::raw('order_product.quantity * products.price'));

        $orders = Order::whereHas('products', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->where(function ($query) {
                $query->whereNull('refund_status')
                      ->orWhere('refund_status', '!=', 'Approved');
            })
            ->count();

        $data[] = [
            'month' => $date->format('M Y'),
            'revenue' => (float) $revenue,
            'orders' => $orders
        ];
    }
    return $data;
}

    private function getTopSellingProducts($userId, $days = 30)
    {
        return DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->where('products.user_id', $userId)
            ->where('orders.created_at', '>=', Carbon::now()->subDays($days))
            ->where(function ($query) {
            $query->whereNull('orders.refund_status')
                  ->orWhere('orders.refund_status', '!=', 'Approved');
            })
            ->select(
                'products.name',
                'products.price',
                'products.quantity as stock',
                DB::raw('SUM(order_product.quantity) as total_sold'),
                DB::raw('SUM(order_product.quantity * products.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.price', 'products.quantity')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();
    }

    private function getRecentOrders($userId, $limit = 10)
    {
        return Order::whereHas('products', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with(['user', 'products' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
        ->latest()
        ->limit($limit)
        ->get();
    }

    private function getOrderStatusDistribution($userId)
    {
        return Order::whereHas('products', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->select('status', DB::raw('count(*) as count'))
        ->groupBy('status')
        ->where(function ($query) {
                $query->whereNull('refund_status')
                      ->orWhere('refund_status', '!=', 'Approved');
            })
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->status => $item->count];
        });
    }

    private function getDailySalesData($userId, $days = 30)
{
    $data = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i);
        
        $revenue = DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->where('products.user_id', $userId)
            ->whereIn('orders.status', ['Delivered', 'Completed'])
            ->whereDate('orders.created_at', $date->toDateString())
            ->where(function ($query) {
                $query->whereNull('orders.refund_status')
                      ->orWhere('orders.refund_status', '!=', 'Approved');
            })
            ->sum(DB::raw('order_product.quantity * products.price'));

        $orders = Order::whereHas('products', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereDate('created_at', $date->toDateString())
            ->where(function ($query) {
                $query->whereNull('refund_status')
                      ->orWhere('refund_status', '!=', 'Approved');
            })
            ->count();

        $data[] = [
            'date' => $date->format('M j'),
            'revenue' => (float) $revenue,
            'orders' => $orders
        ];
    }
    return $data;
}


    private function getAverageOrderValue($userId)
    {
        $totalRevenue = $this->getTotalRevenue($userId);
        $totalOrders = $this->getTotalOrders($userId);
        
        return $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
    }

    private function getMonthlyAverageOrderValue($userId, $month)
    {
        $monthlyRevenue = $this->getMonthlyRevenue($userId, $month);
        $monthlyOrders = $this->getMonthlyOrders($userId, $month);
        
        return $monthlyOrders > 0 ? $monthlyRevenue / $monthlyOrders : 0;
    }

    private function getProductPerformanceMetrics($userId)
    {
        return DB::table('products')
            ->leftJoin('order_product', 'products.id', '=', 'order_product.product_id')
            ->leftJoin('orders', 'order_product.order_id', '=', 'orders.id')
            ->where('products.user_id', $userId)
            ->select(
                'products.name',
                'products.price',
                'products.quantity as current_stock',
                DB::raw('COALESCE(SUM(order_product.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(order_product.quantity * products.price), 0) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_product.order_id) as total_orders')
            )
            ->groupBy('products.id', 'products.name', 'products.price', 'products.quantity')
            ->orderByDesc('total_revenue')
            ->get();
    }

    private function calculateGrowthPercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 1);
    }
     /**
     * Get sales data by product categories (if you have categories)
     */
    private function getSalesByCategory($userId, $days = 30)
    {
        return DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id') // if you have categories
            ->where('products.user_id', $userId)
            ->where('orders.created_at', '>=', Carbon::now()->subDays($days))
            ->select(
                DB::raw('COALESCE(categories.name, "Uncategorized") as category'),
                DB::raw('SUM(order_product.quantity * products.price) as revenue'),
                DB::raw('SUM(order_product.quantity) as quantity_sold')
            )
            ->groupBy('categories.name')
            ->orderByDesc('revenue')
            ->get();
    }

    /**
     * Get hourly sales pattern (to identify peak selling hours)
     */
    private function getHourlySalesPattern($userId, $days = 30)
    {
        return DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->where('products.user_id', $userId)
            ->where('orders.created_at', '>=', Carbon::now()->subDays($days))
            ->select(
                DB::raw('HOUR(orders.created_at) as hour'),
                DB::raw('SUM(order_product.quantity * products.price) as revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    /**
     * Get customer analytics (repeat customers, new customers, etc.)
     */
    private function getCustomerAnalytics($userId)
    {
        $totalCustomers = Order::whereHas('products', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->distinct('user_id')->count('user_id');

        $repeatCustomers = DB::table('orders')
            ->whereExists(function ($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('order_product')
                    ->join('products', 'order_product.product_id', '=', 'products.id')
                    ->whereColumn('order_product.order_id', 'orders.id')
                    ->where('products.user_id', $userId);
            })
            ->select('user_id', DB::raw('COUNT(*) as order_count'))
            ->groupBy('user_id')
            ->having('order_count', '>', 1)
            ->count();

        $newCustomersThisMonth = Order::whereHas('products', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->distinct('user_id')
        ->count('user_id');

        return [
            'total_customers' => $totalCustomers,
            'repeat_customers' => $repeatCustomers,
            'new_customers_this_month' => $newCustomersThisMonth,
            'customer_retention_rate' => $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100, 1) : 0
        ];
    }

    /**
     * Get inventory alerts (low stock products)
     */
    private function getInventoryAlerts($userId, $lowStockThreshold = 10)
    {
        return Product::where('user_id', $userId)
            ->where('quantity', '<=', $lowStockThreshold)
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'asc')
            ->limit(10)
            ->get(['name', 'quantity', 'price']);
    }

    /**
     * Get sales forecast based on historical data (simple linear trend)
     */
    private function getSalesForecast($userId, $periods = 3)
    {
        $historicalData = $this->getMonthlySalesData($userId);
        
        if (count($historicalData) < 2) {
            return [];
        }

        // Simple linear regression for forecasting
        $x = range(1, count($historicalData));
        $y = array_column($historicalData, 'revenue');
        
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumX2 = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumX2 += $x[$i] * $x[$i];
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;
        
        $forecast = [];
        for ($i = 1; $i <= $periods; $i++) {
            $nextPeriod = $n + $i;
            $predictedRevenue = $slope * $nextPeriod + $intercept;
            $forecastDate = Carbon::now()->addMonths($i);
            
            $forecast[] = [
                'month' => $forecastDate->format('M Y'),
                'predicted_revenue' => max(0, $predictedRevenue) // Ensure non-negative
            ];
        }
        
        return $forecast;
    }

    /**
     * Export analytics data to CSV
     */
    public function exportAnalytics(Request $request)
    {
        if (auth()->user()->role === 'buyer') {
            abort(403, 'Unauthorized access.');
        }

        $userId = auth()->id();
        $type = $request->get('type', 'sales'); // sales, products, orders
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="analytics_' . $type . '_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($userId, $type) {
            $file = fopen('php://output', 'w');
            
            switch($type) {
                case 'sales':
                    fputcsv($file, ['Month', 'Revenue', 'Orders']);
                    $data = $this->getMonthlySalesData($userId);
                    foreach($data as $row) {
                        fputcsv($file, [$row['month'], $row['revenue'], $row['orders']]);
                    }
                    break;
                    
                case 'products':
                    fputcsv($file, ['Product Name', 'Price', 'Stock', 'Total Sold', 'Revenue']);
                    $data = $this->getProductPerformanceMetrics($userId);
                    foreach($data as $row) {
                        fputcsv($file, [
                            $row->name, 
                            $row->price, 
                            $row->current_stock, 
                            $row->total_sold, 
                            $row->total_revenue
                        ]);
                    }
                    break;
                    
                case 'orders':
                    fputcsv($file, ['Order ID', 'Customer', 'Total', 'Status', 'Date']);
                    $orders = $this->getRecentOrders($userId, 1000); // Get more orders for export
                    foreach($orders as $order) {
                        fputcsv($file, [
                            $order->id,
                            $order->user->name,
                            $order->total_price,
                            $order->status,
                            $order->created_at->format('Y-m-d H:i:s')
                        ]);
                    }
                    break;
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
// Total Refunds
private function getTotalRefunds($userId)
{
    return Order::whereHas('products', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('refund_status', 'Approved')
        ->sum('total_price');
}

// Monthly Refunds
private function getMonthlyRefunds($userId, $month)
{
    return Order::whereHas('products', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereYear('created_at', $month->year)
        ->whereMonth('created_at', $month->month)
        ->where('refund_status', 'Approved')
        ->sum('total_price');
}

// Refund Rate (%)
private function getRefundRate($userId)
{
    $totalOrders = $this->getTotalOrders($userId);
    $refundedOrders = Order::whereHas('products', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('refund_status', 'Approved')
        ->count();

    return $totalOrders > 0 ? round(($refundedOrders / $totalOrders) * 100, 1) : 0;
}

// Refund trend (last 12 months)
private function getMonthlyRefundData($userId)
{
    $data = [];
    for ($i = 11; $i >= 0; $i--) {
        $date = Carbon::now()->subMonths($i);
        $monthlyRefund = $this->getMonthlyRefunds($userId, $date);

        $data[] = [
            'month' => $date->format('M Y'),
            'refund' => (float) $monthlyRefund
        ];
    }
    return $data;
}




// Add these methods to your DashboardController.php

public function buyerDashboard()
{
    // Only allow buyers
    if (auth()->user()->role !== 'buyer') {
        abort(403, 'Unauthorized access.');
    }

    $userId = auth()->id();
    
    // Get current date information
    $currentMonth = Carbon::now();
    $previousMonth = Carbon::now()->subMonth();
    
    // 1. TOTAL ORDERS METRICS
    $totalOrders = $this->getBuyerTotalOrders($userId);
    $completedOrders = $this->getBuyerCompletedOrders($userId);
    $ongoingOrders = $this->getBuyerOngoingOrders($userId);
    
    // 2. SPENDING METRICS
    $totalSpent = $this->getBuyerTotalSpent($userId);
    $monthlySpending = $this->getBuyerMonthlySpending($userId, $currentMonth);
    $previousMonthSpending = $this->getBuyerMonthlySpending($userId, $previousMonth);
    $spendingGrowth = $this->calculateGrowthPercentage($monthlySpending, $previousMonthSpending);
    
    // 3. AVERAGE ORDER VALUE
    $averageOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
    
    // 4. MONTHLY SPENDING CHART DATA (Last 12 months)
    $monthlySpendingData = $this->getBuyerMonthlySpendingData($userId);
    
    // 5. PAYMENT METHOD DISTRIBUTION
    $paymentMethodData = $this->getBuyerPaymentMethodDistribution($userId);
    
    // 6. TOP 5 PRODUCT CATEGORIES
    $topCategoriesData = $this->getBuyerTopCategories($userId, 5);
    
    // 7. RECENT ORDERS
    $recentOrders = Order::where('user_id', $userId)
        ->with('products')
        ->latest()
        ->limit(10)
        ->get();
    
    // 8. ORDER STATUS BREAKDOWN
    $pendingOrders = Order::where('user_id', $userId)->where('status', 'Pending')->count();
    $shippedOrders = Order::where('user_id', $userId)->where('status', 'Shipped')->count();
    $deliveredOrders = Order::where('user_id', $userId)->where('status', 'Delivered')->count();
    $cancelledOrders = Order::where('user_id', $userId)->where('status', 'Cancelled')->count();
    
    return view('dashboard.buyer', compact(
        'totalOrders',
        'completedOrders',
        'ongoingOrders',
        'totalSpent',
        'monthlySpending',
        'spendingGrowth',
        'averageOrderValue',
        'monthlySpendingData',
        'paymentMethodData',
        'topCategoriesData',
        'recentOrders',
        'pendingOrders',
        'shippedOrders',
        'deliveredOrders',
        'cancelledOrders'
    ));
}

// BUYER-SPECIFIC HELPER METHODS

private function getBuyerTotalOrders($userId)
{
    return Order::where('user_id', $userId)->count();
}

private function getBuyerCompletedOrders($userId)
{
    return Order::where('user_id', $userId)
        ->whereIn('status', ['Delivered', 'Completed'])
        ->count();
}

private function getBuyerOngoingOrders($userId)
{
    return Order::where('user_id', $userId)
        ->whereIn('status', ['Pending', 'Processing', 'Shipped'])
        ->count();
}

private function getBuyerTotalSpent($userId)
{
    return Order::where('user_id', $userId)
        ->whereIn('status', ['Delivered', 'Completed', 'Pending', 'Shipped'])
        ->where(function ($query) {
            $query->whereNull('refund_status')
                  ->orWhere('refund_status', '!=', 'Approved');
        })
        ->sum('total_price');
}

private function getBuyerMonthlySpending($userId, $month)
{
    return Order::where('user_id', $userId)
        ->whereYear('created_at', $month->year)
        ->whereMonth('created_at', $month->month)
        ->whereIn('status', ['Delivered', 'Completed', 'Pending', 'Shipped'])
        ->where(function ($query) {
            $query->whereNull('refund_status')
                  ->orWhere('refund_status', '!=', 'Approved');
        })
        ->sum('total_price');
}

private function getBuyerMonthlySpendingData($userId)
{
    $data = [];
    for ($i = 11; $i >= 0; $i--) {
        $date = Carbon::now()->subMonths($i);
        
        $amount = Order::where('user_id', $userId)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->whereIn('status', ['Delivered', 'Completed', 'Pending', 'Shipped'])
            ->where(function ($query) {
                $query->whereNull('refund_status')
                      ->orWhere('refund_status', '!=', 'Approved');
            })
            ->sum('total_price');

        $data[] = [
            'month' => $date->format('M Y'),
            'amount' => (float) $amount
        ];
    }
    return $data;
}

private function getBuyerPaymentMethodDistribution($userId)
{
    return Order::where('user_id', $userId)
        ->select('payment_method', DB::raw('count(*) as count'))
        ->groupBy('payment_method')
        ->get()
        ->mapWithKeys(function ($item) {
            return [ucfirst($item->payment_method) => $item->count];
        })
        ->toArray();
}

// private function getBuyerTopCategories($userId, $limit = 5)
// {
//     // Get the top categories based on order count
//     return DB::table('orders')
//         ->join('order_product', 'orders.id', '=', 'order_product.order_id')
//         ->join('products', 'order_product.product_id', '=', 'products.id')
//         ->where('orders.user_id', $userId)
//         ->select(
//             DB::raw("COALESCE(products.category, 'Uncategorized') as category"),
//             DB::raw('COUNT(DISTINCT orders.id) as count')
//         )
//         ->groupBy('category')
//         ->orderByDesc('count')
//         ->limit($limit)
//         ->get()
//         ->map(function ($item) {
//             return [
//                 'category' => $item->category,
//                 'count' => $item->count
//             ];
//         })
//         ->toArray();
// }

/**
 * Get buyer's favorite products (most purchased)
 */

private function getBuyerTopCategories($userId, $limit = 5)
{
    // Since there's no category column, we'll return top purchased products instead
    return DB::table('orders')
        ->join('order_product', 'orders.id', '=', 'order_product.order_id')
        ->join('products', 'order_product.product_id', '=', 'products.id')
        ->where('orders.user_id', $userId)
        ->select(
            'products.name as category', // Using product name as category
            DB::raw('SUM(order_product.quantity) as count')
        )
        ->groupBy('products.id', 'products.name')
        ->orderByDesc('count')
        ->limit($limit)
        ->get()
        ->map(function ($item) {
            return [
                'category' => $item->category,
                'count' => $item->count
            ];
        })
        ->toArray();
}


private function getBuyerFavoriteProducts($userId, $limit = 5)
{
    return DB::table('order_product')
        ->join('products', 'order_product.product_id', '=', 'products.id')
        ->join('orders', 'order_product.order_id', '=', 'orders.id')
        ->where('orders.user_id', $userId)
        ->select(
            'products.name',
            DB::raw('SUM(order_product.quantity) as total_purchased'),
            DB::raw('SUM(order_product.quantity * products.price) as total_spent')
        )
        ->groupBy('products.id', 'products.name')
        ->orderByDesc('total_purchased')
        ->limit($limit)
        ->get();
}

/**
 * Get buyer's spending by supplier/seller
 */
private function getBuyerSpendingBySeller($userId)
{
    return DB::table('orders')
        ->join('order_product', 'orders.id', '=', 'order_product.order_id')
        ->join('products', 'order_product.product_id', '=', 'products.id')
        ->join('users', 'products.user_id', '=', 'users.id')
        ->where('orders.user_id', $userId)
        ->select(
            'users.name as seller_name',
            DB::raw('SUM(order_product.quantity * products.price) as total_spent'),
            DB::raw('COUNT(DISTINCT orders.id) as order_count')
        )
        ->groupBy('users.id', 'users.name')
        ->orderByDesc('total_spent')
        ->get();
}

/**
 * Get buyer's order frequency (average days between orders)
 */
private function getBuyerOrderFrequency($userId)
{
    $orders = Order::where('user_id', $userId)
        ->orderBy('created_at')
        ->pluck('created_at')
        ->toArray();

    if (count($orders) < 2) {
        return null;
    }

    $intervals = [];
    for ($i = 1; $i < count($orders); $i++) {
        $diff = Carbon::parse($orders[$i])->diffInDays(Carbon::parse($orders[$i - 1]));
        $intervals[] = $diff;
    }

    return count($intervals) > 0 ? array_sum($intervals) / count($intervals) : null;
}

/**
 * Get buyer's refund/return history
 */
private function getBuyerRefundHistory($userId)
{
    return [
        'total_refunds' => Order::where('user_id', $userId)
            ->where('refund_status', 'Approved')
            ->count(),
        'pending_refunds' => Order::where('user_id', $userId)
            ->where('refund_status', 'Pending')
            ->count(),
        'rejected_refunds' => Order::where('user_id', $userId)
            ->where('refund_status', 'Rejected')
            ->count(),
        'total_refunded_amount' => Order::where('user_id', $userId)
            ->where('refund_status', 'Approved')
            ->sum('total_price')
    ];
}

/**
 * Get buyer's order status timeline
 */
private function getBuyerOrderTimeline($userId, $days = 90)
{
    return Order::where('user_id', $userId)
        ->where('created_at', '>=', Carbon::now()->subDays($days))
        ->select(
            DB::raw('DATE(created_at) as date'),
            'status',
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('date', 'status')
        ->orderBy('date')
        ->get();
}

/**
 * Get buyer's savings (if you have discounts/promos)
 */
private function getBuyerSavings($userId)
{
    // If you have a discount or original_price field
    return DB::table('order_product')
        ->join('orders', 'order_product.order_id', '=', 'orders.id')
        ->join('products', 'order_product.product_id', '=', 'products.id')
        ->where('orders.user_id', $userId)
        ->select(
            DB::raw('SUM((products.original_price - products.price) * order_product.quantity) as total_savings')
        )
        ->value('total_savings') ?? 0;
}

/**
 * Export buyer analytics to CSV
 */
public function exportBuyerAnalytics(Request $request)
{
    if (auth()->user()->role !== 'buyer') {
        abort(403, 'Unauthorized access.');
    }

    $userId = auth()->id();
    $type = $request->get('type', 'orders'); // orders, spending, products
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="buyer_analytics_' . $type . '_' . date('Y-m-d') . '.csv"',
    ];

    $callback = function() use ($userId, $type) {
        $file = fopen('php://output', 'w');
        
        switch($type) {
            case 'orders':
                fputcsv($file, ['Order ID', 'Date', 'Total', 'Status', 'Payment Method', 'Items']);
                $orders = Order::where('user_id', $userId)->get();
                foreach($orders as $order) {
                    fputcsv($file, [
                        $order->id,
                        $order->created_at->format('Y-m-d H:i:s'),
                        $order->total_price,
                        $order->status,
                        $order->payment_method,
                        $order->products->count()
                    ]);
                }
                break;
                
            case 'spending':
                fputcsv($file, ['Month', 'Amount Spent', 'Orders']);
                $data = $this->getBuyerMonthlySpendingData($userId);
                foreach($data as $row) {
                    fputcsv($file, [
                        $row['month'],
                        $row['amount'],
                        Order::where('user_id', $userId)
                            ->whereYear('created_at', Carbon::parse($row['month'])->year)
                            ->whereMonth('created_at', Carbon::parse($row['month'])->month)
                            ->count()
                    ]);
                }
                break;
                
            case 'products':
                fputcsv($file, ['Product Name', 'Quantity Purchased', 'Total Spent']);
                $products = DB::table('order_product')
                    ->join('products', 'order_product.product_id', '=', 'products.id')
                    ->join('orders', 'order_product.order_id', '=', 'orders.id')
                    ->where('orders.user_id', $userId)
                    ->select(
                        'products.name',
                        DB::raw('SUM(order_product.quantity) as qty'),
                        DB::raw('SUM(order_product.quantity * products.price) as total')
                    )
                    ->groupBy('products.id', 'products.name')
                    ->get();
                    
                foreach($products as $product) {
                    fputcsv($file, [
                        $product->name,
                        $product->qty,
                        $product->total
                    ]);
                }
                break;
        }
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}