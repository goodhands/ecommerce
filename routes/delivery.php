<?php

use App\Http\Controllers\DeliveryMethodsController;
use Illuminate\Support\Facades\Route;

Route::get('/deliveries/boot', [DeliveryMethodsController::class, 'loadMethods']);

Route::group(['prefix' => '/store/{store}', 'middleware' => ['auth:sanctum']], function () {
    Route::post('/providers/delivery', [DeliveryMethodsController::class, 'store']);
});