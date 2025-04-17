<?php
use App\Http\Controllers\OrderCancellationController;
use App\Http\Controllers\OrderCompletionController;
use App\Http\Controllers\ProductPaymentController;
use App\Http\Controllers\LudwigPaymentController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EWalletController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\OrderController;
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
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->middleware('guest')->name('password.request');

    Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware('guest')
        ->name('password.email');

    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->middleware('guest')->name('password.reset');

    Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
        ->middleware('guest')
        ->name('password.update');
    // Home & Shop Routes
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/search', [ProductController::class, 'search'])->name('search');
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

    Route::get('/product/{id}', [ShopController::class, 'getProductDetail'])->name('product.show');
    Route::get('/api/product/{id}', [ShopController::class, 'getProductDetails'])->name('api.product.details');


    Route::get('/store/{storeId}/products/ajax', [ShopController::class, 'storeProductsAjax'])
        ->name('store.products.ajax');
    Route::post('/product/{id}/addComment', [ShopController::class, 'addComment'])->name('product.addComment');

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
    // Route untuk konfirmasi pesanan selesai (manual)
Route::post('/orders/{id}/confirm', [App\Http\Controllers\OrderCompletionController::class, 'confirmOrderCompletion'])
->middleware(['auth'])
->name('order.confirm');

// Route untuk schedule otomatis konfirmasi
Route::post('/orders/{id}/schedule-confirmation', [App\Http\Controllers\OrderCompletionController::class, 'scheduleAutomaticCompletion'])
->middleware(['auth'])
->name('order.schedule-confirmation');
    Route::get('/checkout', [ShopController::class, 'checkout'])->name('checkout');

    Route::post('/process-checkout', [ShopController::class, 'placeOrder'])->name('checkout.process');

    Route::get('/order/confirmation/{order}', [ShopController::class, 'orderConfirmation'])->name('order.confirmation');
    Route::get('/order/paid/{order}', [ShopController::class, 'orderConfirmation'])->name('order.receipt');
    
    Route::delete('/order/cancel/{order}', [ShopController::class, 'cancel'])->name('order.cancel');
    Route::get('/store/{storeId}/products', [ShopController::class, 'storeProducts'])->name('store.products');
    
    // Ludwig Payment Routes
    Route::get('/payment/search', [ProductPaymentController::class, 'showSearch'])
        ->name('payment.search');
    
        Route::get('/api/orders/payment-code/{code}', [ProductPaymentController::class, 'getOrderByPaymentCode']);
        
    Route::post('/payment/process', [ProductPaymentController::class, 'processPayment'])
        ->name('payment.process');
        Route::post('/order/payment/process', [App\Http\Controllers\ProductPaymentController::class, 'processOrderPayment'])->name('order.payment.process');

    Route::get('/unpaid-orders', [ShopController::class, 'unpaidOrders'])->name('ecom.list_order_payment');
    
    Route::get('/payment', [EWalletController::class, 'showPayment'])->name('ewallet.payment');
    Route::post('/payment', [EWalletController::class, 'processPayment'])->name('ewallet.process');
    
    Route::get('/transfer', [EWalletController::class, 'showSearch'])->name('ewallet.search');
    
    Route::get('/transfer/amount/{recipient}', [EWalletController::class, 'showTransferAmount'])->name('transfer.amount');
Route::post('/transfer', [EWalletController::class, 'transfer'])->name('ewallet.transfer');
    
    Route::get('/api/search-users', [EWalletController::class, 'searchUsers']);
    Route::post('/pin/create', [EWalletController::class, 'createPin'])->name('user.create-pin');
    Route::get('/user/has-pin', function() {
        $user = auth()->user();
        $pinExists = \App\Models\Pin::where('user_id', $user->id)->exists();
        return response()->json(['hasPin' => $pinExists]);
    })->name('user.has-pin');
    
    Route::get('/transfer/success/{transfer}', [EWalletController::class, 'transferSuccess'])
    ->name('ewallet.transfer.success');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('photo');

    Route::prefix('address')->name('address.')->group(function () {
        Route::post('/', [ProfileController::class, 'addAddress'])->name('addup');
        Route::post('/{address}/primary', [ProfileController::class, 'setPrimaryAddress'])->name('priup');
        Route::delete('/{address}', [ProfileController::class, 'deleteAddress'])->name('delup');
        Route::put('/{address}', [ProfileController::class, 'updateAddress'])->name('upadd');
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

    // Top Up Routes
    Route::get('/top-up', [EWalletController::class, 'showTopUp'])->name('ewallet.top-up');
    Route::post('/top-up', [EWalletController::class, 'processTopUp'])->name('ewallet.top-up.process');
    Route::get('/top-up/{topUp}/instructions', [EWalletController::class, 'showInstructions'])->name('ewallet.top-up.instructions');
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

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('/orders/generate-tracking', [OrderController::class, 'generateMultipleTrackingNumbers'])
            ->name('generate.tracking');
        Route::get('/orders/{order}/resi-sticker', [OrderController::class, 'generateResiSticker'])
            ->name('orders.resi-sticker');
        Route::get('/export-daily-sales', [DashboardController::class, 'exportDailySales'])->name('export.daily_sales');
        Route::get('/export-monthly-sales', [DashboardController::class, 'exportMonthlySales'])->name('export.monthly_sales');
        Route::get('/dashboard/export/yearly-sales', [DashboardController::class, 'exportYearlySales'])
    ->name('export.yearly_sales');

        Route::post('/orders/{order}/cancel', [OrderCancellationController::class, 'cancelOrder'])
        ->name('orders.cancel');
        Route::get('/orders/multiple/resi-sticker', [OrderController::class, 'generateBulkResiSticker'])
        ->name('orders.bulk-resi-sticker');
        Route::get('/export-daily-sales', [DashboardController::class, 'exportDailySales'])->name('export.daily_sales');
        Route::get('/export-monthly-sales', [DashboardController::class, 'exportMonthlySales'])->name('export.monthly_sales');
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
        Route::get('/top-up-requests', [SuperAdminController::class, 'topUpRequests'])->name('top-up-requests');
        Route::post('/top-up/{topUp}/confirm', [SuperAdminController::class, 'confirmTopUp'])->name('top-up.confirm');
        Route::get('/manual-top-up', [SuperAdminController::class, 'manualTopUp'])->name('manual-top-up');
        Route::get('/search-payment-code', [SuperAdminController::class, 'searchPaymentCode'])->name('search-payment-code');
        Route::post('/manual-top-up/confirm', [SuperAdminController::class, 'confirmManualTopUp'])->name('manual-top-up.confirm');
        Route::get('/refund-history', [SuperAdminController::class, 'refundHistory'])->name('refund-history');
        Route::get('/order-history', [SuperAdminController::class, 'orderHistory'])->name('order-history');
        Route::get('/driver-history', [SuperAdminController::class, 'driverHistory'])->name('driver-history');
        Route::get('/seller-history', [SuperAdminController::class, 'sellerHistory'])->name('seller-history');
    });


/*
|--------------------------------------------------------------------------
| Driver Routes
|--------------------------------------------------------------------------
*/
// Tambahkan routes ini ke dalam group route driver yang sudah ada

Route::middleware(['auth', 'driver'])
    ->prefix('driver')
    ->name('driver.')
    ->group(function () {
    Route::get('/dashboard', [DriverController::class, 'dashboard'])->name('dashboard');
    Route::get('/check-tracking', [DriverController::class, 'checkTracking'])->name('check.tracking');
    Route::get('/delivery/{id}', [DriverController::class, 'deliveryProcess'])->name('delivery.process');

    Route::post('/start-delivery/{id}', [DriverController::class, 'startDelivery'])->name('start.delivery');
    Route::post('/update-delivery-status/{id}', [DriverController::class, 'updateDeliveryStatus'])->name('update.delivery.status');
    Route::post('/complete-delivery/{id}', [DriverController::class, 'completeDelivery'])->name('complete.delivery');
    
    Route::get('/delivery-history', [DriverController::class, 'deliveryHistory'])->name('delivery.history');
    Route::get('/delivery-history/{id}', [DriverController::class, 'deliveryHistoryDetail'])->name('delivery.history.detail');
    Route::get('/export-delivery-history', [DriverController::class, 'exportDeliveryHistory'])->name('export.delivery.history');
    
    Route::get('/delivery-history', [DriverController::class, 'deliveryHistory'])
        ->name('delivery.history');
    Route::get('/delivery-history/{id}', [DriverController::class, 'deliveryHistoryDetail'])
        ->name('delivery.history.detail');
    Route::get('/export-delivery-history', [DriverController::class, 'exportDeliveryHistory'])
        ->name('export.delivery.history');

    Route::get('/profile', [DriverController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [DriverController::class, 'updateProfile'])->name('profile.update');
    Route::get('/customers', [DriverController::class, 'customers'])->name('customers');
});