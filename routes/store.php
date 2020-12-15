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


// Endpoints that do not require auth
Route::get('store/{shortname}', [StoreController::class, 'show']);

//Endpoints that require auth
//TODO Add a middleware to ensure user is subscribed to a plan 
Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'store'], function () {
    Route::post('/', [StoreController::class, 'store']);
    Route::pup('/', [StoreController::class, 'update']);
});
