<?php

use App\Http\Controllers\UserController;
use App\Livewire\Admin\Order\OrderCreate;
use App\Livewire\Admin\Order\OrderList;
use App\Livewire\Admin\Products\ProductList;
use App\Livewire\Admin\Store\StoreList;
use Illuminate\Support\Facades\Route;


Route::get('/login', [UserController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::get('logout', [UserController::class, 'destroy'])->name('logout');

    Route::get('/', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    Route::get('/products', ProductList::class)->name('products');

    Route::get('/stores', StoreList::class)->name('stores');

    Route::get('/orders', OrderList::class)->name('orders');
    Route::get('/orders/create', OrderCreate::class)->name('orders.create');
});
