<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'store/{store}/dashboard/', 'middleware' => ['auth:sanctum']], function () {
    Route::get('weekly/sales', [DashboardController::class, 'getWeeklyStats']);
});