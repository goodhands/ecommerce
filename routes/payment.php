<?php

use App\Http\Controllers\PaymentMethodsController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/payment-options', [PaymentMethodsController::class, 'providers']);
    // Ideally you wouldn't need this. Might remove it later
    Route::get('/payment-options/paystack', [PaymentMethodsController::class, 'paystack']);
    
    Route::post('/store/{store}/providers/payment', [PaymentMethodsController::class, 'store']); 
    Route::get('/store/{store}/providers/payment', [PaymentMethodsController::class, 'index']); 
});