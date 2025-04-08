<?php

use App\Http\Controllers\UserController;
use App\Livewire\Admin\Products\ProductList;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\StoreAdminController;
use App\Http\Controllers\Admin\OrderAdminController;


Route::get('/login', [UserController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::get('logout', [UserController::class, 'destroy'])->name('logout');

    Route::get('/', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    Route::get('/admin/products', ProductList::class)->name('products');
    Route::get('/admin/stores', [StoreAdminController::class, 'index'])->name('stores');


//    Route::prefix('admin')->group(function () {
//        Route::get('/orders', [OrderAdminController::class, 'index']);
//        Route::get('/stores', [StoreAdminController::class, 'index']);
//        Route::get('/products', [ProductAdminController::class, 'index']);
//    });
});
