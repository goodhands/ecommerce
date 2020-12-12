<?php

use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Auth\RegisteredUserController;

//Create a store as a new user
Route::post('/sign-up', [RegisteredUserController::class, 'store'])
                ->middleware('guest');

/**
 * Create an additional store as an existing user
 */
//TODO Add a middleware to ensure user is subscribed to a plan 
// Route::group(['middleware' => ['auth:sanctum']], function () {
//     Route::post('/store/new', [StoreController::class, 'store']);
// });

Route::middleware('auth:sanctum')->post('/store/new', function (Request $request) {
    return auth()->user();
});