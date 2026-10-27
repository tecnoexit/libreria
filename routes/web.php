<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\StoreController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'index'])->name('store.index');
Route::post('/checkout', [StoreController::class, 'checkout'])->name('store.checkout');
Route::post('/cart/add/{product}', fn(Product $product) => tap(redirect()->back(), function() use ($product) { $cart=session('cart', []); $cart[$product->id]=($cart[$product->id]??0)+1; session(['cart'=>$cart]); }))->name('cart.add');
Route::post('/cart/remove/{product}', fn(Product $product) => tap(redirect()->back(), function() use ($product) { $cart=session('cart', []); unset($cart[$product->id]); session(['cart'=>$cart]); }))->name('cart.remove');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('admin.auth')->name('logout');
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [AdminController::class,'dashboard'])->name('dashboard');
        Route::post('/factor', [AdminController::class,'saveFactor'])->name('factor');
        Route::post('/products', [AdminController::class,'storeProduct'])->name('products.store');
        Route::post('/products/{product}', [AdminController::class,'updateProduct'])->name('products.update');
    });
});
