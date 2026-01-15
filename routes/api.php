<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Order\CartController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Report\ReportController;

// ======================
// Public Routes
// ======================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


// ======================
// Protected Routes
// ======================
Route::middleware(['auth:sanctum', 'tenant'])->group(function () {

    // Common (Owner + Staff)
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/products/{id?}', [ProductController::class, 'show']);


    // ======================
    // Staff Routes (Owner + Staff)
    // ======================
    Route::middleware('staff')->group(function () {

        Route::post('/add-cart/{id}', [CartController::class, 'addCart']);
        Route::post('/cart-view/{reg?}', [CartController::class, 'cartView']);
        Route::post('/remove-to-cart/{reg}/{id}', [CartController::class, 'cartRemove']);

        Route::post('/confirm-order', [OrderController::class, 'confirmOrder']);
        Route::post('/cancel-order', [OrderController::class, 'cancelOrder']);

        Route::post('/payment', [PaymentController::class, 'payment']);
    });


    // ======================
    // Owner Only Routes
    // ======================
    Route::middleware('owner')->group(function () {

        // Product Management
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/tenants/{tenant}/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products-delete/{id}', [ProductController::class, 'destroy']);

        // Reports
        Route::post('/daily-sale-summary', [ReportController::class, 'dailySaleSummary']);
        Route::post('/top-5-sale', [ReportController::class, 'topSellingProducts']);
        Route::post('/low-stock-report', [ReportController::class, 'lowStock']);
    });
});
