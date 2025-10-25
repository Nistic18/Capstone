<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminReportsController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        // Get summary statistics
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::whereIn('status', ['Delivered', 'Completed'])
            ->where(function ($query) {
                $query->whereNull('refund_status')
                      ->orWhere('refund_status', '!=', 'Approved');
            })
            ->sum('total_price');
        
        $totalReviews = Review::count();
        $averageRating = Review::avg('rating');
        
        // Monthly statistics
        $currentMonth = Carbon::now();
        $monthlyUsers = User::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
        
        $monthlyOrders = Order::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
        
        $monthlyRevenue = Order::whereIn('status', ['Delivered', 'Completed'])
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->where(function ($query) {
                $query->whereNull('refund_status')
                      ->orWhere('refund_status', '!=', 'Approved');
            })
            ->sum('total_price');

        return view('admin.reports.index', compact(
            'totalUsers',
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'totalReviews',
            'averageRating',
            'monthlyUsers',
            'monthlyOrders',
            'monthlyRevenue'
        ));
    }

    /**
     * Download User Report
     */
    public function downloadUserReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from')) : Carbon::now()->subYear();
        $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to')) : Carbon::now();

        $users = User::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['orders' => function($query) {
                $query->whereIn('status', ['Delivered', 'Completed']);
            }])
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone' => $user->phone ?? 'N/A',
                    'address' => $user->address ?? 'N/A',
                    'total_orders' => $user->orders->count(),
                    'total_spent' => $user->orders->sum('total_price'),
                    'registered_date' => $user->created_at->format('Y-m-d'),
                    'last_order' => $user->orders->max('created_at') ? Carbon::parse($user->orders->max('created_at'))->format('Y-m-d') : 'N/A'
                ];
            });

        if ($format === 'csv') {
            return $this->exportUserCSV($users, $dateFrom, $dateTo);
        } elseif ($format === 'excel') {
            return $this->exportUserExcel($users, $dateFrom, $dateTo);
        } else {
            return $this->exportUserPDF($users, $dateFrom, $dateTo);
        }
    }

    /**
     * Download Product Report
     */
    public function downloadProductReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        
        $products = Product::with(['user', 'reviews'])
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
                    'supplier' => $product->user->name,
                    'price' => $product->price,
                    'current_stock' => $product->quantity,
                    'total_sold' => $totalSold,
                    'total_revenue' => $totalRevenue,
                    'average_rating' => $product->reviews->avg('rating') ?? 0,
                    'total_reviews' => $product->reviews_count,
                    'status' => $product->quantity > 0 ? 'In Stock' : 'Out of Stock',
                    'created_date' => $product->created_at->format('Y-m-d')
                ];
            });

        if ($format === 'csv') {
            return $this->exportProductCSV($products);
        } elseif ($format === 'excel') {
            return $this->exportProductExcel($products);
        } else {
            return $this->exportProductPDF($products);
        }
    }

    /**
     * Download Sales Report
     */
    public function downloadSalesReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from')) : Carbon::now()->subMonth();
        $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to')) : Carbon::now();

        $orders = Order::with(['user', 'products'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get()
            ->map(function($order) {
                return [
                    'order_id' => $order->id,
                    'customer_name' => $order->user->name,
                    'customer_email' => $order->user->email,
                    'products_count' => $order->products->count(),
                    'total_amount' => $order->total_price,
                    'status' => $order->status,
                    'refund_status' => $order->refund_status ?? 'N/A',
                    'payment_method' => $order->payment_method ?? 'N/A',
                    'order_date' => $order->created_at->format('Y-m-d H:i:s'),
                    'delivery_date' => $order->updated_at->format('Y-m-d H:i:s')
                ];
            });

        // Calculate summary
        $totalOrders = $orders->count();
        $totalRevenue = $orders->whereIn('status', ['Delivered', 'Completed'])
            ->where('refund_status', '!=', 'Approved')
            ->sum('total_amount');
        $totalRefunds = $orders->where('refund_status', 'Approved')->sum('total_amount');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $summary = [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'total_refunds' => $totalRefunds,
            'net_revenue' => $totalRevenue - $totalRefunds,
            'average_order_value' => $averageOrderValue,
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d')
        ];

        if ($format === 'csv') {
            return $this->exportSalesCSV($orders, $summary);
        } elseif ($format === 'excel') {
            return $this->exportSalesExcel($orders, $summary);
        } else {
            return $this->exportSalesPDF($orders, $summary);
        }
    }

    /**
     * Download Feedback/Rating Report
     */
    public function downloadFeedbackReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from')) : Carbon::now()->subMonth();
        $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to')) : Carbon::now();

        $reviews = Review::with(['user', 'product'])
            ->whereBetween('created_at', [
    $dateFrom->startOfDay(),
    $dateTo->endOfDay()
])
            ->get()
            ->map(function($review) {
                return [
                    'review_id' => $review->id,
                    'product_name' => $review->product->name,
                    'customer_name' => $review->user->name,
                    'rating' => $review->rating,
                    'comment' => $review->comment ?? 'No comment',
                    'review_date' => $review->created_at->format('Y-m-d H:i:s')
                ];
            });

        // Calculate statistics
        $totalReviews = $reviews->count();
        $averageRating = $reviews->avg('rating');
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
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d')
        ];

        if ($format === 'csv') {
            return $this->exportFeedbackCSV($reviews, $summary);
        } elseif ($format === 'excel') {
            return $this->exportFeedbackExcel($reviews, $summary);
        } else {
            return $this->exportFeedbackPDF($reviews, $summary);
        }
    }

    /**
     * Download Income Summary Report
     */
    public function downloadIncomeSummaryReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from')) : Carbon::now()->subYear();
        $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to')) : Carbon::now();

        // Monthly breakdown
        $monthlyIncome = [];
        $currentDate = $dateFrom->copy();
        
        while ($currentDate <= $dateTo) {
            $revenue = Order::whereIn('status', ['Delivered', 'Completed'])
                ->whereYear('created_at', $currentDate->year)
                ->whereMonth('created_at', $currentDate->month)
                ->where(function ($query) {
                    $query->whereNull('refund_status')
                          ->orWhere('refund_status', '!=', 'Approved');
                })
                ->sum('total_price');

            $refunds = Order::where('refund_status', 'Approved')
                ->whereYear('created_at', $currentDate->year)
                ->whereMonth('created_at', $currentDate->month)
                ->sum('total_price');

            $orders = Order::whereYear('created_at', $currentDate->year)
                ->whereMonth('created_at', $currentDate->month)
                ->count();

            $monthlyIncome[] = [
                'month' => $currentDate->format('M Y'),
                'gross_revenue' => $revenue,
                'refunds' => $refunds,
                'net_revenue' => $revenue - $refunds,
                'orders' => $orders,
                'average_order_value' => $orders > 0 ? $revenue / $orders : 0
            ];

            $currentDate->addMonth();
        }

        // Top suppliers
        $topSuppliers = DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->join('users', 'products.user_id', '=', 'users.id')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->whereIn('orders.status', ['Delivered', 'Completed'])
            ->where(function ($query) {
                $query->whereNull('orders.refund_status')
                      ->orWhere('orders.refund_status', '!=', 'Approved');
            })
            ->select(
                'users.name as supplier_name',
                DB::raw('SUM(order_product.quantity * products.price) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $summary = [
            'total_gross_revenue' => collect($monthlyIncome)->sum('gross_revenue'),
            'total_refunds' => collect($monthlyIncome)->sum('refunds'),
            'total_net_revenue' => collect($monthlyIncome)->sum('net_revenue'),
            'total_orders' => collect($monthlyIncome)->sum('orders'),
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d')
        ];

        if ($format === 'csv') {
            return $this->exportIncomeSummaryCSV($monthlyIncome, $topSuppliers, $summary);
        } elseif ($format === 'excel') {
            return $this->exportIncomeSummaryExcel($monthlyIncome, $topSuppliers, $summary);
        } else {
            return $this->exportIncomeSummaryPDF($monthlyIncome, $topSuppliers, $summary);
        }
    }

    // ==================== CSV EXPORT METHODS ====================
    
    private function exportUserCSV($users, $dateFrom, $dateTo)
    {
        $filename = 'user_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($users, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            // Add title and date range
            fputcsv($file, ['User Report']);
            fputcsv($file, ['Generated:', date('Y-m-d H:i:s')]);
            fputcsv($file, ['Period:', $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d')]);
            fputcsv($file, []);
            
            // Headers
            fputcsv($file, ['ID', 'Name', 'Email', 'Role', 'Phone', 'Address', 'Total Orders', 'Total Spent', 'Registered Date', 'Last Order']);
            
            // Data
            foreach($users as $user) {
                fputcsv($file, [
                    $user['id'],
                    $user['name'],
                    $user['email'],
                    $user['role'],
                    $user['phone'],
                    $user['address'],
                    $user['total_orders'],
                    number_format($user['total_spent'], 2),
                    $user['registered_date'],
                    $user['last_order']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportProductCSV($products)
    {
        $filename = 'product_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Product Report']);
            fputcsv($file, ['Generated:', date('Y-m-d H:i:s')]);
            fputcsv($file, []);
            
            fputcsv($file, ['ID', 'Product Name', 'Supplier', 'Price', 'Current Stock', 'Total Sold', 'Total Revenue', 'Avg Rating', 'Total Reviews', 'Status', 'Created Date']);
            
            foreach($products as $product) {
                fputcsv($file, [
                    $product['id'],
                    $product['name'],
                    $product['supplier'],
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

    private function exportSalesCSV($orders, $summary)
    {
        $filename = 'sales_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($orders, $summary) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Sales Report']);
            fputcsv($file, ['Period:', $summary['date_from'] . ' to ' . $summary['date_to']]);
            fputcsv($file, ['Total Orders:', $summary['total_orders']]);
            fputcsv($file, ['Total Revenue:', '₱' . number_format($summary['total_revenue'], 2)]);
            fputcsv($file, ['Total Refunds:', '₱' . number_format($summary['total_refunds'], 2)]);
            fputcsv($file, ['Net Revenue:', '₱' . number_format($summary['net_revenue'], 2)]);
            fputcsv($file, ['Average Order Value:', '₱' . number_format($summary['average_order_value'], 2)]);
            fputcsv($file, []);
            
            fputcsv($file, ['Order ID', 'Customer Name', 'Email', 'Products', 'Total Amount', 'Status', 'Refund Status', 'Payment Method', 'Order Date', 'Delivery Date']);
            
            foreach($orders as $order) {
                fputcsv($file, [
                    $order['order_id'],
                    $order['customer_name'],
                    $order['customer_email'],
                    $order['products_count'],
                    number_format($order['total_amount'], 2),
                    $order['status'],
                    $order['refund_status'],
                    $order['payment_method'],
                    $order['order_date'],
                    $order['delivery_date']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportFeedbackCSV($reviews, $summary)
    {
        $filename = 'feedback_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($reviews, $summary) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Feedback & Rating Report']);
            fputcsv($file, ['Period:', $summary['date_from'] . ' to ' . $summary['date_to']]);
            fputcsv($file, ['Total Reviews:', $summary['total_reviews']]);
            fputcsv($file, ['Average Rating:', $summary['average_rating']]);
            fputcsv($file, []);
            fputcsv($file, ['Rating Distribution:']);
            fputcsv($file, ['5 Stars:', $summary['rating_distribution']['5_star']]);
            fputcsv($file, ['4 Stars:', $summary['rating_distribution']['4_star']]);
            fputcsv($file, ['3 Stars:', $summary['rating_distribution']['3_star']]);
            fputcsv($file, ['2 Stars:', $summary['rating_distribution']['2_star']]);
            fputcsv($file, ['1 Star:', $summary['rating_distribution']['1_star']]);
            fputcsv($file, []);
            
            fputcsv($file, ['Review ID', 'Product Name', 'Customer Name', 'Rating', 'Comment', 'Review Date']);
            
            foreach($reviews as $review) {
                fputcsv($file, [
                    $review['review_id'],
                    $review['product_name'],
                    $review['customer_name'],
                    $review['rating'],
                    $review['comment'],
                    $review['review_date']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportIncomeSummaryCSV($monthlyIncome, $topSuppliers, $summary)
    {
        $filename = 'income_summary_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($monthlyIncome, $topSuppliers, $summary) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Income Summary Report']);
            fputcsv($file, ['Period:', $summary['date_from'] . ' to ' . $summary['date_to']]);
            fputcsv($file, ['Total Gross Revenue:', '₱' . number_format($summary['total_gross_revenue'], 2)]);
            fputcsv($file, ['Total Refunds:', '₱' . number_format($summary['total_refunds'], 2)]);
            fputcsv($file, ['Total Net Revenue:', '₱' . number_format($summary['total_net_revenue'], 2)]);
            fputcsv($file, ['Total Orders:', $summary['total_orders']]);
            fputcsv($file, []);
            
            fputcsv($file, ['Monthly Breakdown']);
            fputcsv($file, ['Month', 'Gross Revenue', 'Refunds', 'Net Revenue', 'Orders', 'Avg Order Value']);
            
            foreach($monthlyIncome as $month) {
                fputcsv($file, [
                    $month['month'],
                    number_format($month['gross_revenue'], 2),
                    number_format($month['refunds'], 2),
                    number_format($month['net_revenue'], 2),
                    $month['orders'],
                    number_format($month['average_order_value'], 2)
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ['Top Suppliers']);
            fputcsv($file, ['Supplier Name', 'Total Revenue', 'Total Orders']);
            
            foreach($topSuppliers as $supplier) {
                fputcsv($file, [
                    $supplier->supplier_name,
                    number_format($supplier->total_revenue, 2),
                    $supplier->total_orders
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==================== PDF EXPORT METHODS ====================
    // Note: These require barryvdh/laravel-dompdf package
    // Install: composer require barryvdh/laravel-dompdf
    
    private function exportUserPDF($users, $dateFrom, $dateTo)
    {
        $data = [
            'title' => 'User Report',
            'date' => date('Y-m-d H:i:s'),
            'period' => $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'),
            'users' => $users,
            'total_users' => $users->count(),
            'total_spent' => $users->sum('total_spent')
        ];

        $pdf = PDF::loadView('admin.reports.pdf.users', $data);
        return $pdf->download('user_report_' . date('Y-m-d') . '.pdf');
    }

    private function exportProductPDF($products)
    {
        $data = [
            'title' => 'Product Report',
            'date' => date('Y-m-d H:i:s'),
            'products' => $products,
            'total_products' => $products->count(),
            'total_revenue' => $products->sum('total_revenue')
        ];

        $pdf = PDF::loadView('admin.reports.pdf.products', $data);
        return $pdf->download('product_report_' . date('Y-m-d') . '.pdf');
    }

    private function exportSalesPDF($orders, $summary)
    {
        $data = [
            'title' => 'Sales Report',
            'date' => date('Y-m-d H:i:s'),
            'orders' => $orders,
            'summary' => $summary
        ];

        $pdf = PDF::loadView('admin.reports.pdf.sales', $data);
        return $pdf->download('sales_report_' . date('Y-m-d') . '.pdf');
    }

    private function exportFeedbackPDF($reviews, $summary)
    {
        $data = [
            'title' => 'Feedback & Rating Report',
            'date' => date('Y-m-d H:i:s'),
            'reviews' => $reviews,
            'summary' => $summary
        ];

        $pdf = PDF::loadView('admin.reports.pdf.feedback', $data);
        return $pdf->download('feedback_report_' . date('Y-m-d') . '.pdf');
    }

    private function exportIncomeSummaryPDF($monthlyIncome, $topSuppliers, $summary)
    {
        $data = [
            'title' => 'Income Summary Report',
            'date' => date('Y-m-d H:i:s'),
            'monthlyIncome' => $monthlyIncome,
            'topSuppliers' => $topSuppliers,
            'summary' => $summary
        ];

        $pdf = PDF::loadView('admin.reports.pdf.income-summary', $data);
        return $pdf->download('income_summary_' . date('Y-m-d') . '.pdf');
    }

    // Excel methods would be similar but using Laravel Excel package
    // For now, returning CSV as Excel alternative
    private function exportUserExcel($users, $dateFrom, $dateTo)
    {
        return $this->exportUserCSV($users, $dateFrom, $dateTo);
    }

    private function exportProductExcel($products)
    {
        return $this->exportProductCSV($products);
    }

    private function exportSalesExcel($orders, $summary)
    {
        return $this->exportSalesCSV($orders, $summary);
    }

    private function exportFeedbackExcel($reviews, $summary)
    {
        return $this->exportFeedbackCSV($reviews, $summary);
    }

    private function exportIncomeSummaryExcel($monthlyIncome, $topSuppliers, $summary)
    {
        return $this->exportIncomeSummaryCSV($monthlyIncome, $topSuppliers, $summary);
    }
}