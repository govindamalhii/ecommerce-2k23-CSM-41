<?php

// Merge the group below into your existing routes/api.php — this file is
// just the Sprint 2 additions in isolation for easy copy-paste.

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SkuController;
use App\Http\Controllers\Admin\VariantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('v1/admin')
    ->group(function () {
        Route::get('categories', [CategoryController::class, 'index']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::patch('categories/{category}', [CategoryController::class, 'update']);

        Route::get('products', [ProductController::class, 'index']);
        Route::post('products', [ProductController::class, 'store']);
        Route::patch('products/{product}', [ProductController::class, 'update']);

        Route::post('products/{product}/variants', [VariantController::class, 'store']);
        Route::post('products/{product}/skus', [SkuController::class, 'store']);
        Route::patch('skus/{sku}', [SkuController::class, 'update']);
    });
