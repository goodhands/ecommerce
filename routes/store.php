<?php

use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Auth\RegisteredUserController;

//Create a store as a new user
//?step=0 -> email, password, store, name
//?step=1 -> industry, category, size
Route::post('/sign-up', [RegisteredUserController::class, 'createStore'])
        ->middleware('guest');

/**
 * Create an additional store as an existing user
 */
//TODO Add a middleware to ensure user is subscribed to a plan 
Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'store'], function () {
    Route::get('{shortname}', [StoreController::class, 'show']);
    Route::post('/new', [StoreController::class, 'store']);
    Route::post('/update', [StoreController::class, 'update']);
});