<?php

use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ProductController;

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
    Route::put('/', [StoreController::class, 'update']);

    // interact with the products
    Route::get('/{shortname}/products/{slug}', [ProductController::class, 'getProductByShortname']);
    Route::get('/{shortname}/products', [ProductController::class, 'index']);
    Route::post('/{shortname}/products/', [ProductController::class, 'createProduct']);

    //collections
    Route::post('/{shortname}/collections', [CollectionController::class, 'store']);
    Route::get('/{shortname}/collections', [CollectionController::class, 'index']);
    Route::get('/{shortname}/collections/{collection}', [CollectionController::class, 'show']);
    Route::get('/{shortname}/collections/search/{keyword}', [CollectionController::class, 'search']);
    Route::post('/{shortname}/collections/products', [CollectionController::class, 'addProduct']);
});