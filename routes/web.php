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
use App\Http\Controllers\ChatController;
Route::get('/', function () {
    return view('auth.login');
});
Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/', [ProductController::class, 'home'])->name('home');
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


Route::post('/gemini/generate', [App\Http\Controllers\GeminiController::class, 'generate'])->name('gemini.generate');
Route::get('/gemini/history', [App\Http\Controllers\GeminiController::class, 'history'])->name('gemini.history');

Route::middleware('auth')->group(function() {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
});




