<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Review;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BuyerReportsController extends Controller
{
    /**
     * Download Buyer Purchase Report
     */
    public function downloadPurchaseReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $dateFrom = $request->get('date_from') 
    ? Carbon::parse($request->get('date_from'))->startOfDay()
    : Carbon::minValue();

$dateTo = $request->get('date_to') 
    ? Carbon::parse($request->get('date_to'))->endOfDay()
    : Carbon::maxValue();

        $userId = auth()->id();

        // Get orders with products
        $orders = Order::with(['products'])
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get()
            ->map(function($order) {
                return [
                    'order_id' => $order->id,
                    'order_date' => $order->created_at->format('Y-m-d H:i:s'),
                    'products_count' => $order->products->count(),
                    'product_names' => $order->products->pluck('name')->join(', '),
                    'total_amount' => $order->total_price,
                    'payment_method' => $order->payment_method ?? 'N/A',
                    'status' => $order->status,
                    'refund_status' => $order->refund_status ?? 'N/A',
                    'delivery_date' => $order->updated_at->format('Y-m-d H:i:s')
                ];
            });

        // Calculate summary
        $totalOrders = $orders->count();
        $completedOrders = $orders->whereIn('status', ['Delivered', 'Completed'])->count();
        $ongoingOrders = $orders->whereIn('status', ['Pending', 'Shipped'])->count();
        $cancelledOrders = $orders->where('status', 'Cancelled')->count();
        
        $totalSpent = $orders->whereIn('status', ['Delivered', 'Completed'])
            ->where('refund_status', '!=', 'Approved')
            ->sum('total_amount');
        
        $totalRefunds = $orders->where('refund_status', 'Approved')->sum('total_amount');
        $averageOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;

        $summary = [
            'user_name' => auth()->user()->name,
            'user_email' => auth()->user()->email,
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'ongoing_orders' => $ongoingOrders,
            'cancelled_orders' => $cancelledOrders,
            'total_spent' => $totalSpent,
            'total_refunds' => $totalRefunds,
            'net_spent' => $totalSpent - $totalRefunds,
            'average_order_value' => $averageOrderValue,
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d')
        ];

        if ($format === 'csv') {
            return $this->exportPurchaseCSV($orders, $summary);
        } elseif ($format === 'excel') {
            return $this->exportPurchaseExcel($orders, $summary);
        } else {
            return $this->exportPurchasePDF($orders, $summary);
        }
        
    }

    /**
     * Download Buyer Spending Analysis Report
     */
    public function downloadSpendingReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from')) : Carbon::now()->subYear();
        $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to')) : Carbon::now();

        $userId = auth()->id();

        // Monthly spending breakdown
        $monthlySpending = [];
        $currentDate = $dateFrom->copy();
        
        while ($currentDate <= $dateTo) {
            $spent = Order::where('user_id', $userId)
                ->whereIn('status', ['Delivered', 'Completed'])
                ->whereYear('created_at', $currentDate->year)
                ->whereMonth('created_at', $currentDate->month)
                ->where(function ($query) {
                    $query->whereNull('refund_status')
                          ->orWhere('refund_status', '!=', 'Approved');
                })
                ->sum('total_price');

            $orders = Order::where('user_id', $userId)
                ->whereYear('created_at', $currentDate->year)
                ->whereMonth('created_at', $currentDate->month)
                ->count();

            $monthlySpending[] = [
                'month' => $currentDate->format('M Y'),
                'total_spent' => $spent,
                'orders' => $orders,
                'average_order_value' => $orders > 0 ? $spent / $orders : 0
            ];

            $currentDate->addMonth();
        }

        // Top purchased products
        $topProducts = DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->where('orders.user_id', $userId)
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->whereIn('orders.status', ['Delivered', 'Completed'])
            ->select(
                'products.name as product_name',
                DB::raw('SUM(order_product.quantity) as total_quantity'),
                DB::raw('SUM(order_product.quantity * products.price) as total_spent')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Payment method breakdown
        $paymentMethods = Order::where('user_id', $userId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['Delivered', 'Completed'])
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_price) as total'))
            ->groupBy('payment_method')
            ->get();

        $summary = [
            'user_name' => auth()->user()->name,
            'total_spent' => collect($monthlySpending)->sum('total_spent'),
            'total_orders' => collect($monthlySpending)->sum('orders'),
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d')
        ];

        if ($format === 'csv') {
            return $this->exportSpendingCSV($monthlySpending, $topProducts, $paymentMethods, $summary);
        } elseif ($format === 'excel') {
            return $this->exportSpendingExcel($monthlySpending, $topProducts, $paymentMethods, $summary);
        } else {
            return $this->exportSpendingPDF($monthlySpending, $topProducts, $paymentMethods, $summary);
        }
    }

    /**
     * Download My Reviews Report
     */
    public function downloadReviewsReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $userId = auth()->id();

        $reviews = Review::with(['product'])
            ->where('user_id', $userId)
            ->get()
            ->map(function($review) {
                return [
                    'review_id' => $review->id,
                    'product_name' => $review->product->name,
                    'rating' => $review->rating,
                    'comment' => $review->comment ?? 'No comment',
                    'review_date' => $review->created_at->format('Y-m-d H:i:s')
                ];
            });

        $summary = [
            'user_name' => auth()->user()->name,
            'total_reviews' => $reviews->count(),
            'average_rating' => $reviews->avg('rating')
        ];

        if ($format === 'csv') {
            return $this->exportReviewsCSV($reviews, $summary);
        } elseif ($format === 'excel') {
            return $this->exportReviewsExcel($reviews, $summary);
        } else {
            return $this->exportReviewsPDF($reviews, $summary);
        }
    }

    // ==================== CSV EXPORT METHODS ====================
    
    private function exportPurchaseCSV($orders, $summary)
    {
        $filename = 'my_purchase_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($orders, $summary) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['My Purchase Report']);
            fputcsv($file, ['Customer:', $summary['user_name']]);
            fputcsv($file, ['Email:', $summary['user_email']]);
            fputcsv($file, ['Period:', $summary['date_from'] . ' to ' . $summary['date_to']]);
            fputcsv($file, ['Generated:', date('Y-m-d H:i:s')]);
            fputcsv($file, []);
            fputcsv($file, ['Summary Statistics']);
            fputcsv($file, ['Total Orders:', $summary['total_orders']]);
            fputcsv($file, ['Completed Orders:', $summary['completed_orders']]);
            fputcsv($file, ['Ongoing Orders:', $summary['ongoing_orders']]);
            fputcsv($file, ['Cancelled Orders:', $summary['cancelled_orders']]);
            fputcsv($file, ['Total Spent:', '₱' . number_format($summary['total_spent'], 2)]);
            fputcsv($file, ['Total Refunds:', '₱' . number_format($summary['total_refunds'], 2)]);
            fputcsv($file, ['Net Spent:', '₱' . number_format($summary['net_spent'], 2)]);
            fputcsv($file, ['Average Order Value:', '₱' . number_format($summary['average_order_value'], 2)]);
            fputcsv($file, []);
            
            fputcsv($file, ['Order Details']);
            fputcsv($file, ['Order ID', 'Order Date', 'Products', 'Product Names', 'Total Amount', 'Payment Method', 'Status', 'Refund Status', 'Delivery Date']);
            
            foreach($orders as $order) {
                fputcsv($file, [
                    $order['order_id'],
                    $order['order_date'],
                    $order['products_count'],
                    $order['product_names'],
                    '₱' . number_format($order['total_amount'], 2),
                    $order['payment_method'],
                    $order['status'],
                    $order['refund_status'],
                    $order['delivery_date']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportSpendingCSV($monthlySpending, $topProducts, $paymentMethods, $summary)
    {
        $filename = 'my_spending_analysis_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($monthlySpending, $topProducts, $paymentMethods, $summary) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['My Spending Analysis Report']);
            fputcsv($file, ['Customer:', $summary['user_name']]);
            fputcsv($file, ['Period:', $summary['date_from'] . ' to ' . $summary['date_to']]);
            fputcsv($file, ['Total Spent:', '₱' . number_format($summary['total_spent'], 2)]);
            fputcsv($file, ['Total Orders:', $summary['total_orders']]);
            fputcsv($file, []);
            
            fputcsv($file, ['Monthly Spending Breakdown']);
            fputcsv($file, ['Month', 'Total Spent', 'Orders', 'Avg Order Value']);
            foreach($monthlySpending as $month) {
                fputcsv($file, [
                    $month['month'],
                    '₱' . number_format($month['total_spent'], 2),
                    $month['orders'],
                    '₱' . number_format($month['average_order_value'], 2)
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ['Top 10 Most Purchased Products']);
            fputcsv($file, ['Product Name', 'Total Quantity', 'Total Spent']);
            foreach($topProducts as $product) {
                fputcsv($file, [
                    $product->product_name,
                    $product->total_quantity,
                    '₱' . number_format($product->total_spent, 2)
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ['Payment Method Breakdown']);
            fputcsv($file, ['Payment Method', 'Number of Orders', 'Total Amount']);
            foreach($paymentMethods as $method) {
                fputcsv($file, [
                    $method->payment_method,
                    $method->count,
                    '₱' . number_format($method->total, 2)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportReviewsCSV($reviews, $summary)
    {
        $filename = 'my_reviews_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($reviews, $summary) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['My Reviews Report']);
            fputcsv($file, ['Customer:', $summary['user_name']]);
            fputcsv($file, ['Total Reviews:', $summary['total_reviews']]);
            fputcsv($file, ['Average Rating:', number_format($summary['average_rating'], 2)]);
            fputcsv($file, []);
            
            fputcsv($file, ['Review ID', 'Product Name', 'Rating', 'Comment', 'Review Date']);
            
            foreach($reviews as $review) {
                fputcsv($file, [
                    $review['review_id'],
                    $review['product_name'],
                    $review['rating'],
                    $review['comment'],
                    $review['review_date']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==================== PDF EXPORT METHODS ====================
    
    private function exportPurchasePDF($orders, $summary)
    {
        $data = [
            'title' => 'My Purchase Report',
            'date' => date('Y-m-d H:i:s'),
            'orders' => $orders,
            'summary' => $summary
        ];

        $pdf = PDF::loadView('buyer.reports.pdf.purchases', $data);
        return $pdf->download('my_purchase_report_' . date('Y-m-d') . '.pdf');
    }

    private function exportSpendingPDF($monthlySpending, $topProducts, $paymentMethods, $summary)
    {
        $data = [
            'title' => 'My Spending Analysis',
            'date' => date('Y-m-d H:i:s'),
            'monthlySpending' => $monthlySpending,
            'topProducts' => $topProducts,
            'paymentMethods' => $paymentMethods,
            'summary' => $summary
        ];

        $pdf = PDF::loadView('buyer.reports.pdf.spending', $data);
        return $pdf->download('my_spending_analysis_' . date('Y-m-d') . '.pdf');
    }

    private function exportReviewsPDF($reviews, $summary)
    {
        $data = [
            'title' => 'My Reviews Report',
            'date' => date('Y-m-d H:i:s'),
            'reviews' => $reviews,
            'summary' => $summary
        ];

        $pdf = PDF::loadView('buyer.reports.pdf.reviews', $data);
        return $pdf->download('my_reviews_' . date('Y-m-d') . '.pdf');
    }

    // Excel methods (using CSV for now)
    private function exportPurchaseExcel($orders, $summary)
    {
        return $this->exportPurchaseCSV($orders, $summary);
    }

    private function exportSpendingExcel($monthlySpending, $topProducts, $paymentMethods, $summary)
    {
        return $this->exportSpendingCSV($monthlySpending, $topProducts, $paymentMethods, $summary);
    }

    private function exportReviewsExcel($reviews, $summary)
    {
        return $this->exportReviewsCSV($reviews, $summary);
    }
    /**
 * Preview report before downloading
 */
public function preview(Request $request, $type)
{
    $request->validate([
        'format' => 'required|in:pdf,csv',
        'date_from' => 'nullable|date',
        'date_to' => 'nullable|date|after_or_equal:date_from',
    ]);

    $format = $request->get('format', 'pdf');

    switch ($type) {
        case 'purchases':
            return $this->generatePurchaseReport($request, true);
        case 'spending':
            return $this->generateSpendingReport($request, true);
        case 'reviews':
            return $this->generateReviewsReport($request, true);
        default:
            abort(404, 'Report type not found');
    }
}

/**
 * Download report
 */
public function download(Request $request, $type)
{
    $request->validate([
        'format' => 'required|in:pdf,csv',
        'date_from' => 'nullable|date',
        'date_to' => 'nullable|date|after_or_equal:date_from',
    ]);

    switch ($type) {
        case 'purchases':
            return $this->generatePurchaseReport($request, false);
        case 'spending':
            return $this->generateSpendingReport($request, false);
        case 'reviews':
            return $this->generateReviewsReport($request, false);
        default:
            abort(404, 'Report type not found');
    }
}

/**
 * Generate Purchase Report (for both preview and download)
 */
private function generatePurchaseReport(Request $request, $isPreview = false)
{
    $format = $request->get('format', 'pdf');
    $dateFrom = $request->get('date_from') 
        ? Carbon::parse($request->get('date_from'))->startOfDay()
        : Carbon::minValue();

    $dateTo = $request->get('date_to') 
        ? Carbon::parse($request->get('date_to'))->endOfDay()
        : Carbon::maxValue();

    $userId = auth()->id();

    $orders = Order::with(['products'])
        ->where('user_id', $userId)
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->get()
        ->map(function($order) {
            return [
                'order_id' => $order->id,
                'order_date' => $order->created_at->format('Y-m-d H:i:s'),
                'products_count' => $order->products->count(),
                'product_names' => $order->products->pluck('name')->join(', '),
                'total_amount' => $order->total_price,
                'payment_method' => $order->payment_method ?? 'N/A',
                'status' => $order->status,
                'refund_status' => $order->refund_status ?? 'N/A',
                'delivery_date' => $order->updated_at->format('Y-m-d H:i:s')
            ];
        });

    $totalOrders = $orders->count();
    $completedOrders = $orders->whereIn('status', ['Delivered', 'Completed'])->count();
    $ongoingOrders = $orders->whereIn('status', ['Pending', 'Shipped'])->count();
    $cancelledOrders = $orders->where('status', 'Cancelled')->count();
    
    $totalSpent = $orders->whereIn('status', ['Delivered', 'Completed'])
        ->where('refund_status', '!=', 'Approved')
        ->sum('total_amount');
    
    $totalRefunds = $orders->where('refund_status', 'Approved')->sum('total_amount');
    $averageOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;

    $summary = [
        'user_name' => auth()->user()->name,
        'user_email' => auth()->user()->email,
        'total_orders' => $totalOrders,
        'completed_orders' => $completedOrders,
        'ongoing_orders' => $ongoingOrders,
        'cancelled_orders' => $cancelledOrders,
        'total_spent' => $totalSpent,
        'total_refunds' => $totalRefunds,
        'net_spent' => $totalSpent - $totalRefunds,
        'average_order_value' => $averageOrderValue,
        'date_from' => $dateFrom->format('Y-m-d'),
        'date_to' => $dateTo->format('Y-m-d')
    ];

    if ($format === 'pdf') {
        $pdf = PDF::loadView('buyer.reports.pdf.purchases', [
            'title' => 'My Purchase Report',
            'date' => date('Y-m-d H:i:s'),
            'orders' => $orders,
            'summary' => $summary
        ]);

        if ($isPreview) {
            return $pdf->stream();
        } else {
            return $pdf->download('my_purchase_report_' . date('Y-m-d') . '.pdf');
        }
    } else {
        return $this->exportPurchaseCSV($orders, $summary);
    }
}

/**
 * Generate Spending Report (for both preview and download)
 */
private function generateSpendingReport(Request $request, $isPreview = false)
{
    $format = $request->get('format', 'pdf');
    $dateFrom = $request->get('date_from') ? Carbon::parse($request->get('date_from')) : Carbon::now()->subYear();
    $dateTo = $request->get('date_to') ? Carbon::parse($request->get('date_to')) : Carbon::now();

    $userId = auth()->id();

    $monthlySpending = [];
    $currentDate = $dateFrom->copy();
    
    while ($currentDate <= $dateTo) {
        $spent = Order::where('user_id', $userId)
            ->whereIn('status', ['Delivered', 'Completed'])
            ->whereYear('created_at', $currentDate->year)
            ->whereMonth('created_at', $currentDate->month)
            ->where(function ($query) {
                $query->whereNull('refund_status')
                      ->orWhere('refund_status', '!=', 'Approved');
            })
            ->sum('total_price');

        $orders = Order::where('user_id', $userId)
            ->whereYear('created_at', $currentDate->year)
            ->whereMonth('created_at', $currentDate->month)
            ->count();

        $monthlySpending[] = [
            'month' => $currentDate->format('M Y'),
            'total_spent' => $spent,
            'orders' => $orders,
            'average_order_value' => $orders > 0 ? $spent / $orders : 0
        ];

        $currentDate->addMonth();
    }

    $topProducts = DB::table('order_product')
        ->join('products', 'order_product.product_id', '=', 'products.id')
        ->join('orders', 'order_product.order_id', '=', 'orders.id')
        ->where('orders.user_id', $userId)
        ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
        ->whereIn('orders.status', ['Delivered', 'Completed'])
        ->select(
            'products.name as product_name',
            DB::raw('SUM(order_product.quantity) as total_quantity'),
            DB::raw('SUM(order_product.quantity * products.price) as total_spent')
        )
        ->groupBy('products.id', 'products.name')
        ->orderByDesc('total_quantity')
        ->limit(10)
        ->get();

    $paymentMethods = Order::where('user_id', $userId)
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->whereIn('status', ['Delivered', 'Completed'])
        ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_price) as total'))
        ->groupBy('payment_method')
        ->get();

    $summary = [
        'user_name' => auth()->user()->name,
        'total_spent' => collect($monthlySpending)->sum('total_spent'),
        'total_orders' => collect($monthlySpending)->sum('orders'),
        'date_from' => $dateFrom->format('Y-m-d'),
        'date_to' => $dateTo->format('Y-m-d')
    ];

    if ($format === 'pdf') {
        $pdf = PDF::loadView('buyer.reports.pdf.spending', [
            'title' => 'My Spending Analysis',
            'date' => date('Y-m-d H:i:s'),
            'monthlySpending' => $monthlySpending,
            'topProducts' => $topProducts,
            'paymentMethods' => $paymentMethods,
            'summary' => $summary
        ]);

        if ($isPreview) {
            return $pdf->stream();
        } else {
            return $pdf->download('my_spending_analysis_' . date('Y-m-d') . '.pdf');
        }
    } else {
        return $this->exportSpendingCSV($monthlySpending, $topProducts, $paymentMethods, $summary);
    }
}

/**
 * Generate Reviews Report (for both preview and download)
 */
private function generateReviewsReport(Request $request, $isPreview = false)
{
    $format = $request->get('format', 'pdf');
    $userId = auth()->id();

    $reviews = Review::with(['product'])
        ->where('user_id', $userId)
        ->get()
        ->map(function($review) {
            return [
                'review_id' => $review->id,
                'product_name' => $review->product->name,
                'rating' => $review->rating,
                'comment' => $review->comment ?? 'No comment',
                'review_date' => $review->created_at->format('Y-m-d H:i:s')
            ];
        });

    $summary = [
        'user_name' => auth()->user()->name,
        'total_reviews' => $reviews->count(),
        'average_rating' => $reviews->avg('rating')
    ];

    if ($format === 'pdf') {
        $pdf = PDF::loadView('buyer.reports.pdf.reviews', [
            'title' => 'My Reviews Report',
            'date' => date('Y-m-d H:i:s'),
            'reviews' => $reviews,
            'summary' => $summary
        ]);

        if ($isPreview) {
            return $pdf->stream();
        } else {
            return $pdf->download('my_reviews_' . date('Y-m-d') . '.pdf');
        }
    } else {
        return $this->exportReviewsCSV($reviews, $summary);
    }
}
}