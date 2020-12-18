<?php

use App\Http\Controllers\OrdersController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => "store/{store}"], function () {
    
    //get all orders for an admin
    Route::get('/orders', [OrdersController::class, 'index']);
        //TODO: ->middleware('store_admin');

    Route::post('/checkout', [OrdersController::class, 'checkout']);
});