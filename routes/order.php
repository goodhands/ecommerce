<?php

use App\Http\Controllers\OrdersController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => "store/{store}"], function () {
    
    Route::get('/orders/{order}', [OrdersController::class, 'show']);

    //get all orders for an admin
    Route::get('/orders', [OrdersController::class, 'index']);

    //handle checkout operations using query string for each step
    /**
     * 1. Create customer information
     * 2. Create a new order and attach customer. 
     * 3. Pay
     * Also load the rates of delivery providers set by 
     * the store and send that back as a response 
     */
    Route::post('/checkout', [OrdersController::class, 'checkout']);
});

//verify checkout payment
Route::get('/checkout/verify', [OrdersController::class, 'verify']);