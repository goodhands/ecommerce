<?php

use App\Http\Controllers\OrdersController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => "store/{store}"], function () {
    
    Route::get('/orders/{order}', [OrdersController::class, 'show']);

    //get all orders for an admin
    Route::get('/orders', [OrdersController::class, 'index']);

    //handle checkout operations using query string for each step
    Route::post('/checkout', [OrdersController::class, 'checkout']);
});