<?php

use App\Http\Controllers\OrdersController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => "store/{store}"], function () {
    Route::post('/orders', [OrdersController::class, 'store']);
    Route::post('/checkout', [OrdersController::class, 'checkout']);
});