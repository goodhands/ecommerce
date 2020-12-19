<?php

use App\Http\Controllers\SecretsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/store/{store}', 'middleware' => ['auth:sanctum']], function () {
    Route::post('/secrets', [SecretsController::class, 'store']); 
});