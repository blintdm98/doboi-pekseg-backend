<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\StoreAdminController;
use App\Http\Controllers\Admin\OrderAdminController;

Route::get('/', function () {
    return redirect('/admin/orders');
});

Route::get('/admin/products', [ProductAdminController::class, 'index'])->name('admin.products');
Route::get('/admin/stores', [StoreAdminController::class, 'index'])->name('admin.stores');


Route::prefix('admin')->group(function () {
    Route::get('/orders', [OrderAdminController::class, 'index']);
    Route::get('/stores', [StoreAdminController::class, 'index']);
    Route::get('/products', [ProductAdminController::class, 'index']);
});
