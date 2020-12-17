<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => "store/{store}"], function () {
    Route::post('/orders', [OrderController::class, 'store']);
});