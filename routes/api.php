<?php

use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CartItemController;
use App\Http\Controllers\Front\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('site')
    ->group(function () {
        Route::prefix('produits')
            ->group(function () {
                Route::get('/', [ProductController::class, 'index'])->name('product.index');
                Route::get('{product}', [ProductController::class, 'show'])->name('product.show');
            });

        Route::middleware('cart')
            ->prefix('panier')
            ->group(function () {
                Route::get('/', [CartController::class, 'show'])->name('cart.show');
                Route::put('/', [CartController::class, 'update'])->name('cart.update');
                Route::delete('/', [CartController::class, 'destroy'])->name('cart.destroy');

                Route::prefix('items/{product}')
                    ->group(function () {
                        Route::post('/', [CartItemController::class, 'store'])->name('cart-item.store');
                        Route::delete('/', [CartItemController::class, 'destroy'])->name('cart-item.destroy');
                    });
            });
    });
