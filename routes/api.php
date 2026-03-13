<?php

use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CartItemController;
use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Front\OrderController;
use App\Http\Controllers\Front\PasswordController;
use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('site')
    ->group(function () {
        Route::prefix('auth')
            ->group(function () {
                Route::middleware('guest')
                    ->group(function () {
                        Route::post('login', [AuthController::class, 'login'])->name('auth.login');
                    });

                Route::middleware(['auth:front-api', 'customer'])
                    ->group(function () {
                        Route::get('me', [AuthController::class, 'me'])->name('auth.me');
                        Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
                        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
                    });
            });

        Route::middleware(['auth:front-api', 'customer'])
            ->group(function () {
                Route::prefix('profile')
                    ->group(function () {
                        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
                        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
                    });

                Route::put('password', [PasswordController::class, 'update'])->name('password.update');

                Route::prefix('commandes')
                    ->group(function () {
                        Route::get('/', [OrderController::class, 'index'])->name('order.index');
                        Route::post('/', [OrderController::class, 'store'])
                            ->name('order.store')
                            ->middleware('cart');
                        Route::get('{order}', [OrderController::class, 'show'])
                            ->name('order.show')
                            ->can('view', 'order');
                    });
            });

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
