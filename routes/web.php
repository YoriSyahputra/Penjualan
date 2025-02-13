<?php

use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EWalletController;
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
Route::get('/search', [ProductController::class, 'search'])->name('search');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
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

    Route::post('/payment/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/payment/process/{payment}', [PaymentController::class, 'process'])->name('payment.process');

    Route::get('/payment', [EWalletController::class, 'showPayment'])->name('ewallet.payment');
    Route::post('/payment', [EWalletController::class, 'processPayment'])->name('ewallet.process');
    
    Route::get('/transfer', [EWalletController::class, 'showSearch'])->name('ewallet.search');
    Route::get('/transfer/amount/{recipient}', [EWalletController::class, 'showTransferAmount'])->name('transfer.amount');
    Route::post('/transfer', [EWalletController::class, 'transfer'])->name('ewallet.transfer');
    
    Route::get('/api/search-users', [EWalletController::class, 'searchUsers']);
    Route::post('/pin/create', [EWalletController::class, 'createPin'])->name('user.create-pin');
    
    Route::get('/transfer/success/{transfer}', [EWalletController::class, 'transferSuccess'])
    ->name('ewallet.transfer.success');

    Route::prefix('checkout')->group(function () {
        Route::get('/', [ShopController::class, 'checkout'])->name('checkout');
        Route::post('/place-order', [ShopController::class, 'placeOrder'])->name('order.place');
    });    

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('photo');

    Route::prefix('address')->name('address.')->group(function () {
        Route::post('/', [ProfileController::class, 'addAddress'])->name('add');
        Route::post('/{address}/primary', [ProfileController::class, 'setPrimaryAddress'])->name('primary');
        Route::delete('/{address}', [ProfileController::class, 'deleteAddress'])->name('delete');
        Route::put('/{address}', [ProfileController::class, 'updateAddress'])->name('update');
    });
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