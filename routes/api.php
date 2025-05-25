<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductAPIController;
use App\Http\Controllers\API\UserProfileAPIController;
use App\Http\Controllers\API\CustomProductAPIController;
use App\Http\Controllers\API\OrderAPIController;

// Public API routes
Route::get('/products', [ProductAPIController::class, 'index']);
Route::get('/products/{product}', [ProductAPIController::class, 'show']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('profiles', UserProfileAPIController::class);
    Route::apiResource('custom-products', CustomProductAPIController::class);
    Route::apiResource('orders', OrderAPIController::class);
});