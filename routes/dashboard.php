<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'store/{store}/dashboard/', 'middleware' => ['auth:sanctum']], function () {
    Route::get('weekly-stats', [DashboardController::class, 'getWeeklyStats']);
    Route::get('recent-orders', [DashboardController::class, 'getRecentOrders']);
    Route::get('most-viewed', [DashboardController::class, 'getMostViewedProducts']);
});