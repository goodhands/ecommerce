<?php

use App\Http\Controllers\PaymentMethodsController;
use App\Models\Store;
use App\Repositories\StoreRepository;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'store', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/{store}/payment-options', [PaymentMethodsController::class, 'providers']);
    // Ideally you wouldn't need this. Might remove it later
    Route::get('/payment-options/paystack', [PaymentMethodsController::class, 'paystack']);

    Route::post('/{store}/providers/payment', [PaymentMethodsController::class, 'store']);
    Route::get('/{store}/providers/payment', [PaymentMethodsController::class, 'index']);
});

/**
 * Load all payment methods we have in the config
 */
Route::get('/loadpayments', function () {
    $s = new StoreRepository(new Store());
    return $s->loadPaymentProvidersFromConfig();
});

// Test
Route::get('/checkout/intent', [PaymentMethodsController::class, 'intent']);
Route::post('/checkout/pay', [PaymentMethodsController::class, 'pay']);

Route::group(['prefix' => 'store'], function () {
    Route::get('/{shortname}/checkout/intent', [PaymentMethodsController::class, 'intent']);
});
