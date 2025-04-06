<?php
use App\Http\Controllers\DriverController;
use App\Http\Controllers\Api\DriverApiController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])
    ->prefix('driver')
    ->name('api.driver.')
    ->group(function () {
    Route::post('/update-location/{id}', [DriverApiController::class, 'updateLocation'])->name('update.location');
    Route::get('/active-deliveries', [DriverApiController::class, 'activeDeliveries'])->name('active.deliveries');
});

// EXISTING API ROUTES FOR TRACKING CHECK
Route::get('/check-tracking', [DriverController::class, 'checkTrackingNumber']);
Route::get('/check-tracking-details', [DriverController::class, 'checkTrackingDetails']);
