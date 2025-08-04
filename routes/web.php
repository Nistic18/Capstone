<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LocationController;

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

    Route::get('/hakakses', [App\Http\Controllers\HakaksesController::class, 'index'])->name('hakakses.index')->middleware('superadmin');
    Route::get('/hakakses/edit/{id}', [App\Http\Controllers\HakaksesController::class, 'edit'])->name('hakakses.edit')->middleware('superadmin');
    Route::put('/hakakses/update/{id}', [App\Http\Controllers\HakaksesController::class, 'update'])->name('hakakses.update')->middleware('superadmin');
    Route::delete('/hakakses/delete/{id}', [App\Http\Controllers\HakaksesController::class, 'destroy'])->name('hakakses.delete')->middleware('superadmin');

    Route::get('/table-example', [App\Http\Controllers\ExampleController::class, 'table'])->name('table.example');
    // Product resource routes (CRUD)
    Route::resource('products', ProductController::class);
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




