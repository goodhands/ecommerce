<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function (Request $request) {
    return [
        "status" => "Live",
        "message" => "Welcome to the back side",
        "version" => "v1/api"
    ];
});

/**
 * Subscription endpoints
 */
require __DIR__.'/subscription.php';

//Store Endpoints
require __DIR__.'/store.php';

//Orders
require __DIR__.'/order.php';

//Secrets
require __DIR__ .'/secrets.php';

//Payment Methods
require __DIR__ . '/payment.php';

//Delivery Methods
require __DIR__ . '/delivery.php';

//Dashboard Methods
require __DIR__ . '/dashboard.php';