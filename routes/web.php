<?php

use App\Http\Controllers\UserController;
use App\Livewire\Admin\Products\ProductList;
use App\Livewire\Admin\Store\StoreList;
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

    Route::get('/products', ProductList::class)->name('products');

    Route::get('/stores', StoreList::class)->name('stores');
});
