<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StoreSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// Product resource routes (excluding show - not needed for admin)
Route::resource('products', ProductController::class)
    ->except(['show'])
    ->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);

// Category sort order update (must be before resource routes to avoid conflict)
Route::post('/categories/update-order', [CategoryController::class, 'updateOrder'])
    ->name('admin.categories.update-order');

// Category resource routes (excluding show - not needed for admin)
Route::resource('categories', CategoryController::class)
    ->except(['show'])
    ->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);

// Store Settings (singleton)
Route::get('/settings', [StoreSettingController::class, 'edit'])->name('admin.settings.index');
Route::put('/settings', [StoreSettingController::class, 'update'])->name('admin.settings.update');
