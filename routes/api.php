<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderDetailController;

Route::middleware(['auth:sanctum'])->group(function () {
    //TODO kell token generálás, csak tokennel lehessen elérni az apit
    //TODO Apiban legyen minden POST szerintem, nem kell get/delete/put stb

    Route::get('/stores', [StoreController::class, 'index']);
    Route::post('/stores', [StoreController::class, 'store']);
    Route::delete('/stores/{id}', [StoreController::class, 'destroy']);
    Route::put('/stores/{store}', [StoreController::class, 'update']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::put('/products/{product}', [ProductController::class, 'update']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    Route::put('/order-details/{id}/dispatch', [OrderDetailController::class, 'updateDispatched']);

    Route::get('/users', [UserController::class, 'index']);
});