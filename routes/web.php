<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProductDetailController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->middleware('throttle:public-search')->name('catalog.index');
Route::get('/katalog/{category:slug}', [CatalogController::class, 'byCategory'])->middleware('throttle:public-search')->name('catalog.category');
Route::get('/produk/{product:slug}', [ProductDetailController::class, 'show'])->name('product.show');
Route::get('/keranjang', [CartController::class, 'index'])->middleware('throttle:cart-operations')->name('cart.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
