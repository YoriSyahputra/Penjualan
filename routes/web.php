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
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return view('ecom.home');
})->name('logout');

// Home & Shop Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/search', [ProductController::class, 'search'])->name('search');
Route::get('/product-details/{id}', [ShopController::class, 'getProductDetails'])->name('product.details');

// Guest Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [ShopController::class, 'cart'])->name('index');
    Route::post('/add/{id}', [ShopController::class, 'addToCart'])->name('add');
    Route::post('/update/{key}', [ShopController::class, 'update'])->name('update');
    Route::post('/remove/{key}', [ShopController::class, 'remove'])->name('remove');
});

// Authentication Routes
require __DIR__ . '/auth.php';

// Password Reset Routes
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Checkout Routes
    Route::prefix('checkout')->group(function () {
        Route::get('/', [ShopController::class, 'checkout'])->name('checkout');
        Route::post('/place-order', [ShopController::class, 'placeOrder'])->name('order.place');
    });

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('photo');
    });

    // Store Management
    Route::prefix('store')->name('store.')->group(function () {
        Route::get('/', [StoreController::class, 'index'])->name('index');
        Route::get('/create', [StoreController::class, 'create'])->name('create');
        Route::post('/', [StoreController::class, 'store'])->name('store');
        Route::get('/edit', [StoreController::class, 'edit'])->name('edit');
        Route::put('/', [StoreController::class, 'update'])->name('update');
        Route::get('/settings', [StoreController::class, 'settings'])->name('settings');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
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
/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('super-admin')
    ->middleware(['auth', 'super'])
    ->name('super-admin.')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('dashboard');
        Route::post('/approve/{user}', [SuperAdminController::class, 'approveAdmin'])->name('approve');
        Route::post('/reject/{user}', [SuperAdminController::class, 'rejectAdmin'])->name('reject');
    });