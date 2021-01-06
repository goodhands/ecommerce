<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/store/{store}/customers', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [CustomerController::class, 'index']);
});