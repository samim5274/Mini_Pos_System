<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Order\CartController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Report\ReportController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum', 'tenant')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/products/{id?}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/tenants/{tenant}/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products-delete/{id}', [ProductController::class, 'destroy']);

    Route::post('/add-cart/{id}', [CartController::class, 'addCart']);
    Route::post('/cart-view/{reg?}', [CartController::class, 'cartView']);
    Route::post('/remove-to-cart/{reg}/{id}', [CartController::class, 'cartRemove']);

    Route::post('/confirm-order', [OrderController::class, 'confirmOrder']);
    Route::post('/cancel-order', [OrderController::class, 'cancelOrder']);

    Route::post('/payment', [PaymentController::class, 'payment']);

    Route::post('/daily-sale-summary', [ReportController::class, 'dailySaleSummary']);
    Route::post('/top-5-sale', [ReportController::class, 'topSellingProducts']);
    Route::post('/low-stock-repot', [ReportController::class, 'lowStock']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
