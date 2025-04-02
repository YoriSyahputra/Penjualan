<?php
use App\Http\Controllers\DriverController;
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

// API routes for tracking validation (PERBAIKAN DISINI)
Route::get('/check-tracking', [DriverController::class, 'checkTrackingNumber']);
Route::get('/check-tracking-details', [DriverController::class, 'checkTrackingDetails']);