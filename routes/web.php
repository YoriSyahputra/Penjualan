<?php

use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/', [HomeController::class, 'index'])->name('ecom.home');

   Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->middleware('guest')->name('password.request');

    Route::get('/dshb', [StoreController::class, 'index'])->name('dshb');

    // Shop & Cart Public Routes
    Route::get('/shop', [ShopController::class, 'index'])->name('shop');
    Route::get('/cart', [ShopController::class, 'cart'])->name('cart.index');
    Route::post('/cart/add/{id}', [ShopController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update/{key}', [ShopController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{key}', [ShopController::class, 'remove'])->name('cart.remove');
    Route::get('/product-details/{id}', [ShopController::class, 'getProductDetails']);
    
    Route::get('/checkout', [ShopController::class, 'checkout'])->name('checkout');
    Route::post('/place-order', [ShopController::class, 'placeOrder'])->name('place.order');


    // Product Search
    Route::get('/search', [ProductController::class, 'search'])->name('search');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Home Route
    Route::get('/LudWig', function () {
        return view('ecom.home');
    })->name('win');

    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('photo');
    });

    /*
    |--------------------------------------------------------------------------
    | Store Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('store')->name('store.')->group(function () {
        Route::get('/create', [StoreController::class, 'create'])->name('create');
        Route::post('/', [StoreController::class, 'store'])->name('store');
        Route::get('/edit', [StoreController::class, 'edit'])->name('edit');
        Route::put('/', [StoreController::class, 'update'])->name('update');
        Route::get('/settings', [StoreController::class, 'settings'])->name('settings');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('dashboard')
    ->middleware(['auth', 'admin'])
    ->name('dashboard.')
    ->group(function () {
        // Dashboard Home
        Route::get('/', [DashboardController::class, 'index'])->name('index');
            
        // Product Management
        Route::get('/list-sale', [ProductController::class, 'index'])->name('list_sale');
        Route::get('/sell-product', [ProductController::class, 'create'])->name('sell_product');
        Route::post('/sell-product', [ProductController::class, 'store'])->name('store_product');

        // Perbaikan routes untuk edit dan update
        Route::get('/edit-product/{product}', [ProductController::class, 'edit'])->name('edit_product');
        Route::put('/update-product/{product}', [ProductController::class, 'update'])->name('update_product');
            
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('delete_product');
        Route::delete('/product-images/{image}', [ProductController::class, 'deleteImage'])->name('delete_product_image');
        Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle_status');
    });

    Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'super'])->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('dashboard');
        Route::post('/approve/{user}', [SuperAdminController::class, 'approveAdmin'])->name('approve');
        Route::post('/reject/{user}', [SuperAdminController::class, 'rejectAdmin'])->name('reject');
    });
});