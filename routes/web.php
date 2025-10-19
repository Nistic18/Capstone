<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SupplierProductController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SupplierPostController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResellerApplicationController;
use App\Http\Controllers\Admin\AdminResellerController;
use App\Http\Controllers\Admin\LandingPageController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReviewController;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return view('landing');
})->name('landing');
// Keep Auth::routes() but add explicit login route
Auth::routes();

// Explicitly define login route (in case Auth::routes() doesn't work)
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\ProductController::class, 'home'])->name('home');
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::get('/products/show', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/index', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

    Route::get('/supplierproduct/show', [SupplierProductController::class, 'show'])->name('supplierproduct.show');
    Route::get('/supplierproduct/index', [SupplierProductController::class, 'index'])->name('supplierproduct.index');
    Route::get('/supplierproduct/create', [SupplierProductController::class, 'create'])->name('supplierproduct.create');

    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changepassword'])->name('profile.change-password');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::get('/blank-page', [App\Http\Controllers\HomeController::class, 'blank'])->name('blank');
    Route::get('/supplier/orders', [OrderController::class, 'supplierOrders'])
    ->name('supplier.orders')
    ->middleware('auth');
    Route::put('/supplier/orders/{order}/product/{product}/status', [OrderController::class, 'updateProductStatus'])
    
    ->name('supplier.orders.status.update')
    ->middleware('auth');

    Route::resource('products', ProductController::class);
    Route::resource('supplierproduct', SupplierProductController::class);
});
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::put('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});
Route::put('/supplier/orders/{order}/bulk-status-update', [OrderController::class, 'bulkUpdateProductStatus'])
    ->name('supplier.orders.status.bulk-update')
    ->middleware('auth');

Route::get('/map', function () {
    return view('map'); // your map.blade.php
});

Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');

Route::get('/location-map', function () {
    return view('map'); // make sure you have resources/views/map.blade.php
})->middleware('auth')->name('map');

Route::get('/location-map', [LocationController::class, 'showMap'])->name('map');
Route::post('/locations/store', [LocationController::class, 'store'])->name('locations.store');
Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

Route::post('/orders/{order}/products/{product}/review', [App\Http\Controllers\ReviewController::class, 'store'])
    ->name('reviews.store')
    ->middleware('auth');

Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/myprofile', [ProfileController::class, 'myprofile'])->name('profile.myprofile');

Route::middleware(['auth'])->group(function () {
    Route::get('/newsfeed', [PostController::class, 'index'])->name('newsfeed.index');
    Route::get('/newsfeed/create', [PostController::class, 'create'])->name('newsfeed.create');
    Route::post('/newsfeed', [PostController::class, 'store'])->name('newsfeed.store');
    Route::get('/newsfeed/{post}', [PostController::class, 'show'])->name('newsfeed.show');
    Route::get('/newsfeed/{post}/edit', [PostController::class, 'edit'])->name('newsfeed.edit');
    Route::put('/newsfeed/{post}', [PostController::class, 'update'])->name('newsfeed.update');
    Route::delete('/newsfeed/{post}', [PostController::class, 'destroy'])->name('newsfeed.destroy');
    Route::post('/newsfeed/{post}/react', [PostController::class, 'react'])->name('newsfeed.react');
    Route::post('/newsfeed/{post}/comment', [PostController::class, 'comment'])->name('newsfeed.comment');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/newsfeedsupplier', [SupplierPostController::class, 'index'])->name('newsfeedsupplier.index');
    Route::get('/newsfeedsupplier/create', [SupplierPostController::class, 'create'])->name('newsfeedsupplier.create');
    Route::post('/newsfeedsupplier', [SupplierPostController::class, 'store'])->name('newsfeedsupplier.store');
    Route::get('/newsfeedsupplier/{post}', [SupplierPostController::class, 'show'])->name('newsfeedsupplier.show');
    Route::get('/newsfeedsupplier/{post}/edit', [SupplierPostController::class, 'edit'])->name('newsfeedsupplier.edit');
    Route::put('/newsfeedsupplier/{post}', [SupplierPostController::class, 'update'])->name('newsfeedsupplier.update');
    Route::delete('/newsfeedsupplier/{post}', [SupplierPostController::class, 'destroy'])->name('newsfeedsupplier.destroy');
    Route::post('/newsfeedsupplier/{post}/react', [SupplierPostController::class, 'react'])->name('newsfeedsupplier.react');
    Route::post('/newsfeedsupplier/{post}/comment', [SupplierPostController::class, 'comment'])->name('newsfeedsupplier.comment');
});

Route::post('/gemini/generate', [App\Http\Controllers\GeminiController::class, 'generate'])->name('gemini.generate');
Route::get('/gemini/history', [App\Http\Controllers\GeminiController::class, 'history'])->name('gemini.history');

Route::middleware('auth')->group(function() {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
});

Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::get('/reseller/notifications', [App\Http\Controllers\NotificationController::class, 'reseller'])->name('reseller.notifications');

// Add this route to your routes/web.php file
Route::middleware(['auth', 'verified'])->group(function () {
    // Supplier Dashboard Route
    Route::get('/supplier/dashboard', [DashboardController::class, 'dashboard'])->name('supplier.dashboard');
});
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');



Route::post('/reseller/apply', [ResellerApplicationController::class, 'store'])->name('reseller.store');
Route::get('/reseller/apply', [ResellerApplicationController::class, 'create'])->name('reseller.create');
Route::post('/reseller/apply', [ResellerApplicationController::class, 'store'])->name('reseller.store');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/users/{id}/approve-reseller', [UserController::class, 'approveReseller'])->name('users.approveReseller');
    Route::post('/users/{id}/reject-reseller', [UserController::class, 'rejectReseller'])->name('users.rejectReseller');
});
Route::post('/orders/{order}/refund', [OrderController::class, 'requestRefund'])->name('orders.refund');
Route::post('/orders/{order}/refund/approve', [OrderController::class, 'approveRefund'])
    ->name('orders.refund.approve');

Route::post('/orders/{order}/refund/decline', [OrderController::class, 'declineRefund'])
    ->name('orders.refund.decline');


// Admin Routes (add middleware for admin authentication)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/reseller-applications', [AdminResellerController::class, 'index'])->name('reseller.index');
    Route::get('/reseller-applications/{id}', [AdminResellerController::class, 'show'])->name('reseller.show');
    Route::post('/reseller-applications/{id}/approve', [AdminResellerController::class, 'approve'])->name('reseller.approve');
    Route::post('/reseller-applications/{id}/reject', [AdminResellerController::class, 'reject'])->name('reseller.reject');
});

// Supplier Application Management (Admin only)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/users/approve-supplier/{application}', [UserController::class, 'approveSupplier'])
        ->name('users.approveSupplier');
    
    Route::post('/users/reject-supplier/{application}', [UserController::class, 'rejectSupplier'])
        ->name('users.rejectSupplier');
});



// Existing routes...
Route::resource('newsfeed', PostController::class);

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/posts/review', [App\Http\Controllers\Admin\PostReviewController::class, 'index'])->name('admin.posts.review');
    Route::post('/posts/approve', [App\Http\Controllers\Admin\PostReviewController::class, 'approve'])->name('admin.posts.approve');
    Route::post('/posts/reject', [App\Http\Controllers\Admin\PostReviewController::class, 'reject'])->name('admin.posts.reject');
});


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/landing', [LandingPageController::class, 'index'])->name('landing.index');
    Route::get('/landing/{section}/edit', [LandingPageController::class, 'edit'])->name('landing.edit');
    Route::post('/landing/{section}/update', [LandingPageController::class, 'update'])->name('landing.update');
});

Route::middleware(['auth'])->group(function () {
    
    // Inventory Management Routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::post('/{product}/adjust', [InventoryController::class, 'adjust'])->name('adjust');
        Route::post('/{product}/threshold', [InventoryController::class, 'updateThreshold'])->name('updateThreshold');
        Route::get('/{product}/history', [InventoryController::class, 'history'])->name('history');
        Route::post('/bulk-adjust', [InventoryController::class, 'bulkAdjust'])->name('bulkAdjust');
    });
    
    // Existing Product Routes
    Route::resource('products', ProductController::class);
});

Route::post('/orders/{order}/cancel', [OrderController::class, 'cancelOrder'])
    ->name('orders.cancel')
    ->middleware('auth');
Route::put('/supplier/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('supplier.orders.cancel');

Route::get('/profile/me/reviews', [ReviewController::class, 'index'])
     ->name('profile.reviews')
     ->middleware('auth');